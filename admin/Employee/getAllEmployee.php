<?php
include_once __DIR__ ."/../auth_all_Employee.php";
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

    <!-- CONTROLS -->
    <div class="flex flex-col md:flex-row gap-3 mb-6">

        <select id="rankFilter"
            class="p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">
            <option value="0">All Ranks</option>
            <option value="1">Rank > 1</option>
            <option value="2">Rank > 2</option>
            <option value="3">Rank > 3</option>
        </select>

        <select id="rankBy"
            class="p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">
            <option value="emp_rank">Employee Rank</option>
            <option value="salary">Salary</option>
            <option value="joing_date">Joining date</option>
        </select>

        <input type="text"
            id="searchInput"
            placeholder="Search employee name..."
            class="p-2 flex-1 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">

    </div>

    <!-- TABLE -->
    <div id="tableContainer" class="overflow-x-auto bg-white dark:bg-slate-900 rounded-xl shadow border border-gray-100 dark:border-slate-700 p-2">
        <div class="p-4">Loading employees...</div>
    </div>
</div>

<script>
const tableContainer = document.getElementById("tableContainer");
const searchInput = document.getElementById("searchInput");
const rankFilter = document.getElementById("rankFilter");
const rankBy = document.getElementById("rankBy");

let debounceTimer = null;

async function fetchEmployees() {

    const rank = rankFilter.value;
    const name = searchInput.value.trim();
    const rank_by1 = rankBy.value;

    try {
        tableContainer.innerHTML = `<div class="p-4">Loading...</div>`;

        const response = await fetch(
            `./searchEmployees.php?rank=${rank}&name=${encodeURIComponent(name)}&rank_by=${encodeURIComponent(rank_by1)}`
        );

        const data = await response.json();
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
                <th>${rankBy.value}</th>
                <th>Rank</th>
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
            <td>${emp[rankBy.value] ?? '-'}</td>
            <td><span class="rank">${emp.rank}</span></td>
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

rankFilter.addEventListener("change", fetchEmployees);
rankBy.addEventListener("change", fetchEmployees);

fetchEmployees();
</script>

<script src="../../js/themetoggle.js"></script>

</body>
</html>