<?php


include_once __DIR__ . "/../auth.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        empty($_POST['name']) ||
        empty($_POST['email']) ||
        empty($_POST['emp_rank']) ||
        empty($_POST['password']) ||
        empty($_POST['salary']) ||
        empty($_POST['immediate_supervisor_id'])
    ) {
        $msg = urlencode('all fields must be present');
        header("Location: createEmployee.php?msg=$msg");
        exit();
    }

    $db = PDO_class::initializer();

    $name       = $_POST['name'] ?? null;
    $email      = $_POST['email'] ?? null;
    $emp_rank   = $_POST['emp_rank'] ?? null;
    $password   = $_POST['password'] ?? null;
    $salary     = $_POST['salary'] ?? null;
    $supervisor = $_POST['immediate_supervisor_id'] ?? null;

    $emp_profile_picture_link = $db->image_upload();

    if (
        !$name || !$email || !$emp_rank || !$password ||
        !$salary || !$supervisor || !$emp_profile_picture_link
    ) {
        $msg = urlencode("all fields are required");
        header("Location: createEmployee.php?msg=$msg");
        exit;
    }

    $db->createEmployeeForce(
        $name,
        $email,
        $emp_rank,
        $password,
        $salary,
        $emp_profile_picture_link,
        $supervisor,
        $_SESSION['id']
    );

    header("Location: createEmployee.php?msg=" . urlencode("Success"));
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Employee</title>

<style>
body {
    font-family: Arial;
    background: #eef2f7;
    padding: 20px;
}

.container {
    max-width: 600px;
    background: white;
    padding: 20px;
    border-radius: 12px;
}

input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
}

button {
    width: 100%;
    padding: 10px;
    background: green;
    color: white;
    border: none;
    cursor: pointer;
}

button:hover {
    background: darkgreen;
}

/* SEARCH BOX */
.left_side {
    margin-top: 20px;
    position: relative;
}

.search-results {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #dbe4f0;
    display: none;
    max-height: 250px;
    overflow-y: auto;
    z-index: 999;
}

.search-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #eee;
}

.search-item:hover {
    background: #f3f8ff;
}

.search-item h6 {
    margin: 0;
}
</style>
</head>

<body>

<div class="container">
    <h2>Create Employee</h2>

    <form action="" method="POST" enctype="multipart/form-data">

        <input type="text" name="name" placeholder="Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="number" name="emp_rank" placeholder="Employee Rank" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="number" name="salary" placeholder="Salary" required>
        <input type="file" name="fileToUpload" required>

        <input type="hidden" name="immediate_supervisor_id" id="immediate_supervisor_id">

        <button name="submit" type="submit">Create Employee</button>
    </form>

    <!-- LIVE SEARCH -->
    <div class="left_side">

        <input
            type="text"
            id="managerSearch"
            placeholder="Search employee..."
            autocomplete="off"
        >

        <button type="button" id="searchManagerBtn">Search</button>

        <div id="searchResults" class="search-results"></div>
    </div>
</div>


<script>

const searchManagerBtn = document.getElementById('searchManagerBtn');
const managerSearch = document.getElementById("managerSearch");
const searchResults = document.getElementById("searchResults");

let typingTimer = null;

searchManagerBtn.onclick = () => {
    fetchEmployees(managerSearch.value);
};

managerSearch.addEventListener("input", () => {

    clearTimeout(typingTimer);

    const query = managerSearch.value.trim();

    if (query.length < 1) {
        searchResults.style.display = "none";
        return;
    }

    typingTimer = setTimeout(() => {
        fetchEmployees(query);
    }, 300);
});

async function fetchEmployees(query) {

    const body =
        `name=${encodeURIComponent(query)}&rank=1&submit=submit`;

    try {
        const res = await fetch(
            "http://localhost:80/dashboard/admin/Employee/searchEmployeesEX.php",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: body
            }
        );

        const data = await res.json();
        console.log(data);
        renderEmployees(data);

    } catch (err) {
        console.error(err);
    }
}

function renderEmployees(data) {

    searchResults.innerHTML = "";

    if (!data || data.length === 0) {
        searchResults.innerHTML =
            `<div class="search-item"><h6>No employee found</h6></div>`;
        searchResults.style.display = "block";
        return;
    }

    data.forEach(emp => {

        const div = document.createElement("div");
        div.className = "search-item";

        div.innerHTML = `
            <h6>${emp.emp_name}</h6>
            <small>${emp.email}</small>
        `;

        div.onclick = () => {

            managerSearch.value = emp.emp_name;

            document.getElementById("immediate_supervisor_id").value = emp.emp_id;

            searchResults.style.display = "none";
        };

        searchResults.appendChild(div);
    });

    searchResults.style.display = "block";
}

document.addEventListener("click", (e) => {
    if (
        !managerSearch.contains(e.target) &&
        !searchResults.contains(e.target)
    ) {
        searchResults.style.display = "none";
    }
});

</script>

</body>
</html>