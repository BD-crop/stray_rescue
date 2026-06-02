<?php

include_once __DIR__ . "/../auth.php";
include_once __DIR__ . "/../template/admin_check.php";

if (isset($_POST['submit'])) {
    $obj = PDO_class::initializer();

    if (
        empty($_POST['manager_id']) ||
        empty($_POST['lat']) ||
        empty($_POST['lang']) ||
        empty($_POST['name'])
    ) {
        $msg = urlencode('all fields must be present');
        header("Location: createRescuePoint.php?msg=$msg");
        exit();
    }

    $level = $obj->find_employee_level();

    if (($level === null) || ($level > 1) || ($level < 0)) {
        $msg = urlencode(" must be an senior employee or upper ");
        header("Location: createRescuePoint.php?msg=$msg");
        exit();
    }

    $msg = urlencode(
        $obj->create_rescue_point(
            $_POST['manager_id'],
            $_POST['lat'],
            $_POST['lang'],
            $_POST['name']
        )
    );
    header("Location: seeIndividualLocation.php?id=$msg");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Assign Rescue Point</title>

<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    darkMode: 'class'
}
</script>

<style>
#map {
    width: 100%;
    height: 420px;
    border-radius: 12px;
}

.search-results {
    position: absolute;
    width: 100%;
    max-height: 220px;
    overflow-y: auto;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    display: none;
    z-index: 50;
}

.dark .search-results {
    background: #1e293b;
    border-color: #334155;
}

.search-item {
    padding: 10px;
    cursor: pointer;
    border-bottom: 1px solid #e5e7eb;
}

.dark .search-item {
    border-bottom: 1px solid #334155;
}

.search-item:hover {
    background: #f3f4f6;
}

.dark .search-item:hover {
    background: #334155;
}
</style>

</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white transition-colors duration-500">

<div class="sticky top-0 z-50 flex items-center justify-between px-6 py-3 backdrop-blur-md bg-white/70 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

    <a class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition" href="../index.php">
        Go Back
    </a>

    <button id="themeToggle"
        class="px-4 py-2 rounded-xl bg-gray-900 text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition">
        Theme
    </button>

</div>

<div class="max-w-6xl mx-auto mt-10 px-4">

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

<div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow border border-gray-100 dark:border-slate-700">

<h2 class="text-xl font-bold mb-5">Assign Rescue Point</h2>

<form id="rescueForm" method="POST" class="space-y-4">

    <input
        class="w-full p-3 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
        type="text"
        name="name"
        placeholder="Rescue Point Name"
        required
    >

    <label class="font-semibold">Assign Manager</label>

    <div class="relative">

        <div class="flex gap-2">
            <input
                type="text"
                id="managerSearch"
                class="w-full p-3 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"
                placeholder="Search employee..."
                autocomplete="off"
            >

            <button
                type="button"
                id="searchManagerBtn"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg"
            >
                Search
            </button>
        </div>

        <div id="searchResults" class="search-results"></div>

    </div>

    <div id="selectedManagerBox"
        class="hidden p-3 rounded-lg bg-green-100 dark:bg-green-900 mt-3">
        Assigned:
        <strong id="selectedManagerName"></strong>
    </div>

    <input type="hidden" id="manager_id" name="manager_id">
    <input type="hidden" id="lat" name="lat">
    <input type="hidden" id="lang" name="lang">

    <input type="submit" name="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg" value="Create Rescue Point">
        

</form>

</div>


<div class="bg-white dark:bg-slate-900 p-4 rounded-2xl shadow border border-gray-100 dark:border-slate-700">

<div class="mb-3 text-sm text-gray-600 dark:text-gray-300">
Click map to assign rescue point inside Bangladesh
</div>

<h3 id="Location_shown" class="font-semibold mb-3">
Dhaka, Bangladesh
</h3>

<div id="map"></div>

</div>

</div>
</div>

<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>

const defaultLng = 90.4125;
const defaultLat = 23.8103;

const map = new maplibregl.Map({
    container: 'map',
    style: 'https://tiles.openfreemap.org/styles/liberty',
    center: [defaultLng, defaultLat],
    zoom: 6.5,
    maxBounds: [[88.0, 20.5], [92.8, 26.8]]
});

map.addControl(new maplibregl.NavigationControl());

const marker = new maplibregl.Marker({ draggable: true, color: "#1a73e8" })
    .setLngLat([defaultLng, defaultLat])
    .addTo(map);

function updateLocation(lat, lng) {
    document.getElementById("lat").value = lat;
    document.getElementById("lang").value = lng;
}

async function checkLocation(lat, lng) {
    try {
        const res = await fetch(`http://localhost:80/dashboard/proxy/proxy.php?lat=${lat}&lng=${lng}`);
        const data = await res.json();

        if (!data.address || data.address.country_code !== 'bd') {
            alert("Only Bangladesh rescue points are allowed.");

            marker.setLngLat([defaultLng, defaultLat]);
            map.flyTo({ center: [defaultLng, defaultLat], zoom: 6.5 });

            return false;
        }

        let text = "";
        if (data.address?.suburb) text += data.address.suburb + ", ";
        if (data.address?.city) text += data.address.city + ", ";
        else if (data.address?.state_district) text += data.address.state_district + ", ";
        if (data.address?.country) text += data.address.country;

        document.getElementById("Location_shown").innerText = text;

        return true;

    } catch (e) {
        console.error(e);
        return false;
    }
}

updateLocation(defaultLat, defaultLng);

map.on('click', async (e) => {
    if (!(await checkLocation(e.lngLat.lat, e.lngLat.lng))) return;

    marker.setLngLat(e.lngLat);
    updateLocation(e.lngLat.lat, e.lngLat.lng);
});

marker.on('dragend', async () => {
    const pos = marker.getLngLat();
    if (!(await checkLocation(pos.lat, pos.lng))) return;
    updateLocation(pos.lat, pos.lng);
});

const searchManagerBtn = document.getElementById('searchManagerBtn');
const managerSearch = document.getElementById("managerSearch");
const searchResults = document.getElementById("searchResults");

let typingTimer = null;

searchManagerBtn.onclick = () => fetchEmployees(managerSearch.value);

managerSearch.addEventListener("input", () => {

    clearTimeout(typingTimer);

    if (managerSearch.value.trim().length < 1) {
        searchResults.style.display = "none";
        return;
    }

    typingTimer = setTimeout(() => {
        fetchEmployees(managerSearch.value);
    }, 300);
});

async function fetchEmployees(query) {

    const body = `name=${encodeURIComponent(query)}&rank=3&submit=submit`;

    const res = await fetch("../Employee/searchEmployeesEX.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body
    });

    const data = await res.json();
    renderEmployees(data);
}

function renderEmployees(data) {

    searchResults.innerHTML = "";

    if (!data.length) {
        searchResults.innerHTML = `<div class="p-3">No employee found</div>`;
        searchResults.style.display = "block";
        return;
    }

    data.forEach(emp => {

        const div = document.createElement("div");
        div.className = "search-item";

        div.innerHTML = `
            <div class="font-semibold">${emp.emp_name}</div>
            <div class="text-sm opacity-70">${emp.email}</div>
        `;

        div.onclick = () => {
            managerSearch.value = emp.emp_name;
            document.getElementById("manager_id").value = emp.emp_id;

            document.getElementById("selectedManagerBox").classList.remove("hidden");
            document.getElementById("selectedManagerName").innerText = emp.emp_name;

            searchResults.style.display = "none";
        };

        searchResults.appendChild(div);
    });

    searchResults.style.display = "block";
}

document.addEventListener("click", (e) => {
    if (!managerSearch.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.style.display = "none";
    }
});

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