<?php
    include_once __DIR__ ."/../auth.php";

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manager Dashboard - Employees</title>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        background: #f4f6f8;
    }

    header {
        background: #1f2937;
        color: white;
        padding: 15px 20px;
        font-size: 20px;
    }

    .container {
        padding: 20px;
    }

    .controls {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

    input, select {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 10px;
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    th {
        background: #111827;
        color: white;
    }

    tr:hover {
        background: #f3f4f6;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }

    .rank {
        padding: 4px 8px;
        border-radius: 5px;
        background: #e5e7eb;
        display: inline-block;
    }

    .loading {
        padding: 20px;
        text-align: center;
        color: #666;
    }
</style>
</head>

<body>

<header>
    Manager Dashboard - Employee Control Panel
</header>

<div class="container">

    <div class="controls">
        <select id="rankFilter">
            <option value="0">All Ranks</option>
            <option value="1">Rank > 1</option>
            <option value="2">Rank > 2</option>
            <option value="3">Rank > 3</option>
        </select>

        <select id="rankBy">
            <option value="emp_rank">Employee Rank</option>
            <option value="salary">Salary</option>
            <option value="joing_date">Joining date</option>
            <option value ="rank_assign_date" >Rank assigned</option>
        </select>

        <input type="text" id="searchInput" placeholder="Search employee name...">
    </div>

    <div id="tableContainer">
        <div class="loading">Loading employees...</div>
    </div>

</div>

<script>
const tableContainer = document.getElementById("tableContainer");
const searchInput = document.getElementById("searchInput");
const rankFilter = document.getElementById("rankFilter");
let debounceTimer = null;
// Fetch employees
async function fetchEmployees() {
    console.log(rankFilter.value);

    const rank = rankFilter.value;
    const name = searchInput.value.trim();
    const rank_by1 =rankBy.value;
    try {
        tableContainer.innerHTML = `<div class="loading">Loading...</div>`;

        const response = await fetch(
            `./searchEmployees.php?rank=${rank}&name=${encodeURIComponent(name)}&rank_by=${encodeURIComponent(rank_by1)}`
        );

        const data = await response.json();
        console.log(data);
        renderTable(data);
    } catch (err) {
        tableContainer.innerHTML = `<div class="loading">Error loading data</div>`;
        console.error(err);
    }
}

// Render table
function renderTable(data) {

    if (!data || data.length === 0) {
        tableContainer.innerHTML = `<div class="loading">No employees found</div>`;
        return;
    }

    let html = `
        <table>
            <thead>
                <tr>
                    <th>Profile</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>${rankBy.value}</th>
                    <th>Employee Rank</th>
                    <th>See detail</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(emp => {
        html += `
            <tr>
                <td>
                    <img class="avatar" src="${emp.emp_profile_picture_link}" />
                </td>
                <td>${emp.emp_name}</td>
                <td>${emp.email}</td>
                <td>${emp[rankBy.value]}</td>
                <td><span class="rank">${emp.emp_rank}</span></td>
                <td><a href="./seeIndividualEmployee.php?id=${emp.emp_id}" target="_blank">see detail</a></td>

            </tr>
        `;
    });
    console.log();
    html += `</tbody></table>`;

    tableContainer.innerHTML = html;
}

// Live search with debounce
searchInput.addEventListener("input", () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchEmployees, 300);
});

rankFilter.addEventListener("change", fetchEmployees);
rankBy.addEventListener("change" , fetchEmployees);
// Initial load
fetchEmployees();

</script>

</body>
</html>