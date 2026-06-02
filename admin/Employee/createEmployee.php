<?php


include_once __DIR__ . "/../auth.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_POST['emp_rank'] =4;
    if (

        empty($_POST['name']) ||
        empty($_POST['email']) ||
        empty($_POST['emp_rank']) ||
        empty($_POST['password']) ||
        empty($_POST['salary'])
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
        !$salary  || !$emp_profile_picture_link
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

<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    darkMode: 'class',
    theme: {
        screens: {
            sm: "680px",
            md: "768px",
            lg: "1024px"
        }
    }
}
</script>

<style>

.search-results {
    display: none;
}
</style>

</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white transition-colors duration-500">

<div class="sticky top-0 z-50 flex gap-3 items-center justify-between px-6 py-3 backdrop-blur-md bg-white/70 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

    <a class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition" href="../index.php">
        Go Back
    </a>

    <button id="themeToggle"
        class="px-4 py-2 rounded-xl bg-gray-900 text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition shadow-md">
        Theme
    </button>

</div>

<div class="max-w-2xl mx-auto mt-10 bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-lg border border-gray-100 dark:border-slate-700">

    <h2 class="text-2xl font-bold mb-6">Create Employee</h2>

    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">

        <input class="w-full p-3 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
            type="text" name="name" placeholder="Name" required>

        <input class="w-full p-3 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
            type="email" name="email" placeholder="Email" required>

        <input class="w-full p-3 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
            type="hidden" name="emp_rank" placeholder="Employee Rank" value="4">

        <input class="w-full p-3 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
            type="password" name="password" placeholder="Password" required>

        <input class="w-full p-3 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
            type="number" name="salary" placeholder="Salary" required>

        <input class="w-full p-3 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
            type="file" name="fileToUpload" required>

        <input type="hidden" name="immediate_supervisor_id" id="immediate_supervisor_id">

        <button class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg transition">
            Create Employee
        </button>

    </form>


    <div class="mt-8 relative">

        <input
            type="text"
            id="managerSearch"
            placeholder="Search employee..."
            autocomplete="off"
            class="w-full p-3 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
        >

        <button type="button" id="searchManagerBtn"
            class="mt-3 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition">
            Search
        </button>

        <div id="searchResults"
            class="absolute w-full mt-2 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-lg max-h-64 overflow-y-auto hidden z-50">
        </div>

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
        searchResults.classList.add("hidden");
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
        renderEmployees(data);

    } catch (err) {
        console.error(err);
    }
}

function renderEmployees(data) {

    searchResults.innerHTML = "";

    if (!data || data.length === 0) {
        searchResults.innerHTML =
            `<div class="p-3 text-gray-500 dark:text-gray-300">No employee found</div>`;
        searchResults.classList.remove("hidden");
        return;
    }

    data.forEach(emp => {

        const div = document.createElement("div");
        div.className = "p-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-slate-700 border-b border-gray-100 dark:border-slate-700";

        div.innerHTML = `
            <div class="font-semibold">${emp.emp_name}</div>
            <div class="text-sm text-gray-500 dark:text-gray-300">${emp.email}</div>
        `;

        div.onclick = () => {

            managerSearch.value = emp.emp_name;
            document.getElementById("immediate_supervisor_id").value = emp.emp_id;

            searchResults.classList.add("hidden");
        };

        searchResults.appendChild(div);
    });

    searchResults.classList.remove("hidden");
}

document.addEventListener("click", (e) => {
    if (
        !managerSearch.contains(e.target) &&
        !searchResults.contains(e.target)
    ) {
        searchResults.classList.add("hidden");
    }
});

</script>

<script>
const btn = document.getElementById("themeToggle");

function applyTheme(theme) {
    if (theme === 'dark') {
        document.documentElement.classList.add("dark");
    } else {
        document.documentElement.classList.remove("dark");
    }
}

function getTheme() {
    return localStorage.getItem('theme') || 'light';
}

applyTheme(getTheme());

btn.onclick = () => {
    let newTheme = (getTheme() === 'light') ? 'dark' : 'light';
    localStorage.setItem('theme', newTheme);
    applyTheme(newTheme);
};
</script>

</body>
</html>