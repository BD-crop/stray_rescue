<?php

include_once __DIR__ . "/../auth.php";

if (isset($_POST['submit'])) {

    $obj = PDO_class::initializer();

    if (
        empty($_POST['name']) ||
        empty($_POST['lat']) ||
        empty($_POST['lng']) ||
        empty($_POST['type'])
    ) {
        header("Location: createPetCenter.php?msg=Missing fields");
        exit();
    }

    try {
        $id = $obj->createRescueCenter(
            $_POST['name'],
            $_POST['lat'],
            $_POST['lng'],
            $_POST['type'],
            $_POST['email'] ?? null,
            $_POST['contact'] ?? null
        );

        header("Location: ../../Map/SeePetCenter.php?id=" . $id);
        exit();

    } catch (Exception $e) {
        header("Location: createPetCenter.php?msg=" . urlencode($e->getMessage()));
        exit();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<title>Create Pet Center</title>

<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">
<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

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

</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white transition-all duration-500">
    <div class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md bg-dark/30 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

        <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition" href="../../index.php">
            Home
        </a>

        <button id="themeToggle"
            class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg">
            Theme
        </button>

    </div>

<div class="max-w-6xl mx-auto px-6 pt-8">
    <h1 class="text-4xl font-bold">Create Pet Centers</h1>

    <p class="mt-2 text-gray-600 dark:text-slate-400">
        Create a pet center to inform pet owners about nearby pet friendly places.
    </p>
</div>

<div class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6">

        <form method="POST" class="space-y-5">

            <div>
                <label class="block mb-2 font-medium">Name</label>
                <input type="text" name="name" required
                    class="w-full p-3 rounded-xl border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                    placeholder="Name">
            </div>

            <div>
                <label class="block mb-2 font-medium">Type</label>
                <select name="type" required
                    class="w-full p-3 rounded-xl border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white">
                    <option value="gromming center">Grooming Center</option>
                    <option value="veterenarian hospital">Veterinarian Hospital</option>
                    <option value="park">Park</option>
                    <option value="other">Other</option>
                </select>
            </div>

            <div>
                <label class="block mb-2 font-medium">Email</label>
                <input type="email" name="email"
                    class="w-full p-3 rounded-xl border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                    placeholder="email">
            </div>

            <div>
                <label class="block mb-2 font-medium">Contact</label>
                <input type="text" name="contact"
                    class="w-full p-3 rounded-xl border border-gray-300 dark:border-slate-600 dark:bg-slate-900 dark:text-white"
                    placeholder="contact number">
            </div>

            <input type="hidden" name="lat" id="lat">
            <input type="hidden" name="lng" id="lng">

            <button type="submit" name="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl transition">
                Create Pet Center
            </button>

        </form>

    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6">

        <h3 class="text-xl font-semibold mb-4">Pick Location on Map</h3>

        <div id="map" class="w-full rounded-xl overflow-hidden" style="height:500px;"></div>

        <p id="locationText" class="mt-4 text-gray-600 dark:text-slate-300">
            Click map to select location
        </p>

    </div>

</div>

<script>

const defaultLat = 23.8103;
const defaultLng = 90.4125;

const map = new maplibregl.Map({
    container: 'map',
    style: 'https://tiles.openfreemap.org/styles/liberty',
    center: [defaultLng, defaultLat],
    zoom: 6.5
});

const marker = new maplibregl.Marker({
    draggable: true
})
.setLngLat([defaultLng, defaultLat])
.addTo(map);

function setLocation(lat, lng) {
    document.getElementById("lat").value = lat;
    document.getElementById("lng").value = lng;
}

setLocation(defaultLat, defaultLng);

map.on('click', (e) => {
    marker.setLngLat(e.lngLat);
    setLocation(e.lngLat.lat, e.lngLat.lng);

    document.getElementById("locationText").innerText =
        e.lngLat.lat + ", " + e.lngLat.lng;
});

marker.on('dragend', () => {
    const pos = marker.getLngLat();
    setLocation(pos.lat, pos.lng);

    document.getElementById("locationText").innerText =
        pos.lat + ", " + pos.lng;
});

</script>

<script>
    const btn = document.getElementById("themeToggle");

    let str = ThemeChecker();

    if(str !== 'light'){
        document.documentElement.classList.add("dark");
    }

    btn.onclick = () => {
        localStorage.setItem('theme',
            (localStorage.getItem('theme') === 'light') ? 'dark' : 'light'
        );
        document.documentElement.classList.toggle("dark");
    };

    function ThemeChecker(){
        let obj = localStorage.getItem('theme');

        if(!obj){
            localStorage.setItem('theme','light');
            return 'light';
        }
        return obj;
    }

    function eventListenerToggle(){
        let theme = ThemeChecker();

        document.documentElement.classList.toggle("dark" ,theme === 'dark');
    }

    window.addEventListener("storage", (event) => {
        if (event.key === "theme") {
            eventListenerToggle();
        }
    });
</script>

</body>
</html>