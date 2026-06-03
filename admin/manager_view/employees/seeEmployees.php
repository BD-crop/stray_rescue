<?php
include_once __DIR__ ."/../../auth_all_Employee.php";

//  ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL); 

$obj = PDO_class::initializer();





$point_id= $obj-> getManagerRescuePointID($_SESSION['id']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manager Dashboard - Employees</title>

<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    darkMode: 'class'
}
</script>

<style>
table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 12px;
    text-align: left;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.rank {
    padding: 4px 8px;
    border-radius: 6px;
    background: #e5e7eb;
}

.dark .rank {
    background: #334155;
}
</style>

</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white transition-all duration-500">

<div class="sticky top-0 z-50 flex items-center justify-between p-4 backdrop-blur-md bg-white/70 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

    <a class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition" href="../index.php">
        Go Back
    </a>

    <button id="themeToggle"
        class="px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition">
        Theme
    </button>

</div>

<div class="max-w-6xl mx-auto mt-6 px-4">
    <h1 class="text-2xl font-bold mb-4">Manager Dashboard - Employee Control Panel</h1>

    <div class="flex flex-col md:flex-row gap-3 mb-6">

        <input type="text"
            id="searchInput"
            placeholder="Search employee name..."
            class="p-2 flex-1 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">

    </div>

    <div id="tableContainer" class="overflow-x-auto bg-white dark:bg-slate-900 rounded-xl shadow border border-gray-100 dark:border-slate-700 p-2">
        <div class="p-4">Loading employees...</div>
    </div>
</div>

<script>
const tableContainer = document.getElementById("tableContainer");
const searchInput = document.getElementById("searchInput");

let debounceTimer = null;

async function fetchEmployees() {


    const name = searchInput.value.trim();


    try {
        tableContainer.innerHTML = `<div class="p-4">Loading...</div>`;

        const pointId = <?= json_encode($point_id) ?>;

        const response = await fetch(
            `./seeEmployeesHelper.php?point=${encodeURIComponent(pointId)}&name=${encodeURIComponent(name)}`
        );
        const data = await response.json();
        console.log(data);
        renderTable(data);

    } catch (err) {
        tableContainer.innerHTML = `<div class="p-4">Error loading data</div>`;
    }
}

function renderTable(data) {

    if (!data || data.length === 0) {
        tableContainer.innerHTML = `<div class="p-4">No employees found</div>`;
        return;
    }

    let html = `
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100 dark:bg-slate-800">
            <tr>
                <th>Profile</th>
                <th>Name</th>
                <th>Email</th>
                <th>emp_rank</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    `;

    data.forEach(emp => {
        html += `
        <tr class="border-t border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800">
            <td><img class="avatar" src="${emp.emp_profile_picture_link}"></td>
            <td>${emp.emp_name}</td>
            <td>${emp.email}</td>
            <td>${emp.emp_rank}</td>
            <td>
                <a class="text-blue-500 hover:underline"
                   href="./seeIndividualEmployee.php?id=${emp.emp_id}"
                   target="_blank">
                   see detail
                </a>
            </td>
        </tr>
        `;
    });

    html += `</tbody></table>`;
    tableContainer.innerHTML = html;
}

searchInput.addEventListener("input", () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchEmployees, 300);
});

fetchEmployees();
</script>

<script src="../../js/themetoggle.js"></script>

</body>
</html>