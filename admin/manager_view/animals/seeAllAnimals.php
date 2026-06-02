<?php
include_once __DIR__ ."/../auth_all_Employee.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>See All Animals</title>

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
    border-radius: 8px;
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
    <h1 class="text-2xl font-bold mb-4">Manager Dashboard - See All Animals</h1>

    <div class="flex flex-col md:flex-row gap-3 mb-6">

        <select id="orderBy"
            class="p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">
            <option value="asc">Ascending</option>
            <option value="desc">Descending</option>
        </select>

        <select id="rankBy"
            class="p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">
            <option value="animal_age">Age</option>
            <option value="health_status">Health Status</option>
            <option value="added_at">Added Date</option>
            <option value="is_removed">Removed Status</option>
        </select>

        <input type="text"
            id="searchInput"
            placeholder="Search animal name..."
            class="p-2 flex-1 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">

    </div>

    <div id="tableContainer" class="overflow-x-auto bg-white dark:bg-slate-900 rounded-xl shadow border border-gray-100 dark:border-slate-700 p-2">
        <div class="p-4">Loading animals...</div>
    </div>
</div>

<script>
const tableContainer = document.getElementById("tableContainer");
const searchInput = document.getElementById("searchInput");
const rankFilter = document.getElementById("orderBy");
const rankBy = document.getElementById("rankBy");

let debounceTimer = null;

async function fetchAnimals() {

    const order = document.getElementById("orderBy").value;    const name = searchInput.value.trim();
    const rank_by1 = rankBy.value;

    try {
        tableContainer.innerHTML = `<div class="p-4">Loading...</div>`;

        const response = await fetch(
            `./seeAllAnimalsHelper.php?name=${encodeURIComponent(name)}&rank_by=${encodeURIComponent(rank_by1)}&order=${order}`
        );

        const data = await response.json();

        renderTable(data);

    } catch (err) {
        console.log(err);
        tableContainer.innerHTML = `<div class="p-4">Error loading data</div>`;
    }
}

function renderTable(data) {

    if (!data || data.length === 0) {
        tableContainer.innerHTML = `<div class="p-4">No animals found</div>`;
        return;
    }

    let html = `
    <table class="min-w-full text-sm">
        <thead class="bg-gray-100 dark:bg-slate-800">
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Health</th>
                <th>Images</th>
                <th>Properties</th>
                <th>Listed</th>
                <th>Added</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    `;

    data.forEach(animal => {

        let healthText =
            animal.health_status == 1 ? "Normal" :
            animal.health_status == 2 ? "Attention" :
            "Emergency";

        html += `
        <tr class="border-t border-gray-200 dark:border-slate-700 hover:bg-gray-50 dark:hover:bg-slate-800">
            <td>${animal.animal_name}</td>
            <td>${animal.animal_age}</td>
            <td>${healthText}</td>
            <td>${animal.image_count}</td>
            <td>${animal.prop_count}</td>
            <td>
                <span class="rank">${animal.is_listed}</span>
            </td>
            <td>${animal.added_at}</td>
            <td>
                <a class="text-blue-500 hover:underline"
                   href="./seeIndividualAnimal.php?animal_id=${animal.animal_id}"
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
    debounceTimer = setTimeout(fetchAnimals, 300);
});

rankFilter.addEventListener("change", fetchAnimals);
rankBy.addEventListener("change", fetchAnimals);

fetchAnimals();
</script>

<script>
const btn = document.getElementById("themeToggle");

function applyTheme(theme) {
    document.documentElement.classList.toggle("dark", theme === "dark");
}

function getTheme() {
    return localStorage.getItem("theme") || "light";
}

applyTheme(getTheme());

btn.onclick = () => {
    const newTheme = getTheme() === "light" ? "dark" : "light";
    localStorage.setItem("theme", newTheme);
    applyTheme(newTheme);
};
</script>

</body>
</html>