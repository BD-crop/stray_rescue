<?php
    include_once __DIR__."/../auth.php";
    
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
    Manager Dashboard - Rescue Point Control Panel
</header>

<div class="container">

    <div class="controls">


        <select id="rankBy">
            <option value="creation_date">Creation Date</option>
            <option value="animal_count">Animal Number</option>
            <option value="emp_count">Employee Count</option>
            <option value ="image_count" >Image Count</option>
        </select>
        <select id="order">
            <option value="asc">Asc</option>
            <option value="desc">Desc</option>
        </select>
        <input type="text" id="searchInput" placeholder="Search employee name...">
    </div>

    <div id="tableContainer">
        <div class="loading">Loading employees...</div>
    </div>

</div>

<script>
const searchInput = document.getElementById("searchInput");
const order = document.getElementById('order');
const rankBy = document.getElementById('rankBy');
let debounceTimer = null;


// Fetch employees
async function fetchPoint() {


    const rank = rankBy.value;
    const name = searchInput.value.trim();
    const order1 =order.value;
    try {
        tableContainer.innerHTML = `<div class="loading">Loading...</div>`;

        const response = await fetch(
            `./seeRescuePointHelper.php?rankBy=${encodeURIComponent(rank)}&name=${encodeURIComponent(name)}&order=${encodeURIComponent(order1)}`
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
                    <th>Point Name</th>
                    <th>Image Number</th>
                    <th>Employee Count</th>
                    <th>Animal Count</th>
                    <th>Creation Date</th>
                    <th>See detail</th>
                </tr>
            </thead>
            <tbody>
    `;

    data.forEach(emp => {
        html += `
            <tr>

                <td>${emp.point_name}</td>
                <td>${emp.image_count}</td>
                <td>${emp.emp_count}</td>
                <td><span class="rank">${emp.animal_count}</span></td>
                <td>${emp.creation_date}</td>
                <td><a href="./seeIndividualLocation.php?id=${emp.point_id}" target="_blank">see detail</a></td>

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
    debounceTimer = setTimeout(fetchPoint, 300);
});

order.addEventListener("change", fetchPoint);
rankBy.addEventListener("change" , fetchPoint);
// Initial load
fetchPoint();

</script>

</body>
</html>