<?php
include_once __DIR__."/../auth.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manager Dashboard - Rescue Points</title>

<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    darkMode: 'class'
}
</script>

</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white transition-all duration-500">

<div class="sticky top-0 z-50 flex items-center justify-between px-6 py-3 backdrop-blur-md bg-white/70 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

    <h1 class="font-semibold text-lg">
        Rescue Point Control Panel
    </h1>

    <button id="themeToggle"
        class="px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition">
        Theme
    </button>

</div>

<div class="max-w-6xl mx-auto mt-8 px-4">

<div class="flex flex-col md:flex-row gap-3 mb-6">

    <select id="rankBy"
        class="p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">
        <option value="creation_date">Creation Date</option>
        <option value="animal_count">Animal Number</option>
        <option value="emp_count">Employee Count</option>
        <option value="image_count">Image Count</option>
    </select>

    <select id="order"
        class="p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">
        <option value="asc">Asc</option>
        <option value="desc">Desc</option>
    </select>

    <input
        type="text"
        id="searchInput"
        placeholder="Search Point name..."
        class="p-2 flex-1 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
    >

</div>

<div class="bg-white dark:bg-slate-900 rounded-2xl shadow border border-gray-100 dark:border-slate-700 overflow-x-auto">

    <div id="tableContainer" class="p-4">
        <div class="text-gray-500 dark:text-gray-300">Loading rescue points...</div>
    </div>

</div>

</div>

<script>

const searchInput = document.getElementById("searchInput");
const order = document.getElementById('order');
const rankBy = document.getElementById('rankBy');
const tableContainer = document.getElementById("tableContainer");

let debounceTimer = null;

async function fetchPoint() {

    const rank = rankBy.value;
    const name = searchInput.value.trim();
    const order1 = order.value;

    try {
        tableContainer.innerHTML = `<div class="p-4">Loading...</div>`;

        const response = await fetch(
            `./seeRescuePointHelper.php?rankBy=${encodeURIComponent(rank)}&name=${encodeURIComponent(name)}&order=${encodeURIComponent(order1)}`
        );

        const data = await response.json();
        renderTable(data);

    } catch (err) {
        tableContainer.innerHTML = `<div class="p-4 text-red-500">Error loading data</div>`;
    }
}

function renderTable(data) {

    if (!data || data.length === 0) {
        tableContainer.innerHTML = `<div class="p-4">No Rescue Point found</div>`;
        return;
    }

    let html = `
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100 dark:bg-slate-800">
            <tr>
                <th class="p-3 text-left">Point Name</th>
                <th class="p-3 text-left">Images</th>
                <th class="p-3 text-left">Employees</th>
                <th class="p-3 text-left">Animals</th>
                <th class="p-3 text-left">Created</th>
                <th class="p-3 text-left">Action</th>
            </tr>
        </thead>
        <tbody>
    `;

    data.forEach(point => {

        html += `
        <tr class="border-t border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800">

            <td class="p-3">${point.point_name}</td>
            <td class="p-3">${point.image_count}</td>
            <td class="p-3">${point.emp_count}</td>
            <td class="p-3">${point.animal_count}</td>
            <td class="p-3">${point.creation_date}</td>
            <td class="p-3">
                <a class="text-blue-500 hover:underline"
                   href="./seeIndividualLocation.php?id=${point.point_id}"
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
    debounceTimer = setTimeout(fetchPoint, 300);
});

order.addEventListener("change", fetchPoint);
rankBy.addEventListener("change", fetchPoint);

fetchPoint();

</script>

<script src="../../js/themetoggle.js"></script>

</body>
</html>