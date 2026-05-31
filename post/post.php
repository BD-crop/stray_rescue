<?php

include_once __DIR__ . "/../auth/user_check.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id = $_GET["post_id"] ?? null;

if (!$id) {
    exit("No post id provided");
}

$self = 1;

$profile = PDO_class::initializer()->is_poster($id, $_SESSION['id']);

if ($profile === 1) {
    $self = true;
}

$obj = PDO_class::initializer();

$data = $obj->see_rescue_post($id);

if (!$data) {
    exit("No post found");
}

$lng = (float) $data['post_loc_longtitude'];
$lat = (float) $data['post_loc_latitude'];

$imageLink = explode(';;;', $data['rescue_post_image_link']);

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Animal Data</title>

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
};
</script>

<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">

</head>

<body class="bg-gray-100 dark:bg-slate-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

<div class="flex justify-between items-center px-6 py-4 bg-white dark:bg-slate-800 shadow-md sticky top-0 z-50">



    <div class="flex gap-3 items-center">
        <a href="#image" class="px-4 py-2 rounded-lg bg-purple-500 text-white">Image Section</a>
        <a href="#tree" class="px-4 py-2 rounded-lg bg-purple-500 text-white" >Family Tree</a>
        <a href="#history_section" class="px-4 py-2 rounded-lg bg-purple-500 text-white" >history Section</a>
        <?php if(!isset($_SESSION["id"])): ?>

        <?php else: ?>

            <?php if(PDO_class::initializer()->find_employee_level() >= 0): ?>
                <a href="./admin" class="px-4 py-2 rounded-lg bg-purple-500 text-white">Admin</a>
            <?php endif; ?>

            
            <a href="" class="px-4 py-2 rounded-lg bg-purple-500 text-white">ADD UPDATE</a>
        <?php endif; ?>

        <button id="themeToggle"
            class="px-4 py-2 rounded-lg bg-black text-white dark:bg-white dark:text-black">
            Theme
        </button>

    </div>
</div>

<div class="max-w-6xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden">

        <!-- IMAGE SLIDER -->
        <div class="relative w-full h-80 overflow-hidden bg-black">

            <?php foreach ($imageLink as $index => $img) { ?>
                <img
                    src="<?php echo htmlspecialchars($img); ?>"
                    class="slide-img absolute w-full h-full object-cover transition-opacity duration-500 <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?>"
                >
            <?php } ?>

        </div>

        <!-- CONTENT -->
        <div class="p-5 space-y-3 row-span-2">

            <h1 class="text-2xl font-bold">
                <?php echo htmlspecialchars($data['rescue_post']); ?>
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-300">
                ID: <?php echo $data['rescue_post_id']; ?>
            </p>

            <div class="flex flex-wrap gap-2">

                <span class="px-3 py-1 rounded-full bg-gray-200 dark:bg-slate-700 text-sm">
                    Species: <?php echo $data['animal_species_type']; ?>
                </span>

                <span class="px-3 py-1 rounded-full bg-gray-200 dark:bg-slate-700 text-sm">
                    Gender: <?php echo $data['animal_gender_type']; ?>
                </span>

                <span class="px-3 py-1 rounded-full bg-gray-200 dark:bg-slate-700 text-sm">
                    Age: <?php echo $data['animal_age']; ?>
                </span>

            </div>

        </div>

        <div class="flex justify-between ">
            <p>Qr Code Image</p>
            <img src="<?= $data['qr_image']; ?>" alt="">
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 space-y-3">

        <p class="text-sm text-gray-600 dark:text-gray-300">
            <?php
            $ob = json_decode(
                file_get_contents("http://localhost:80/dashboard/proxy/proxy.php?lat=$lat&lng=$lng"),
                true
            );
            echo $ob['display_name'];
            ?>
        </p>

        <div id="map" class="w-full h-[500px] rounded-xl overflow-hidden"></div>

    </div>

</div>

<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>

console.log(<?php echo(json_encode($data));?>);

const lng = <?php echo $lng; ?>;
const lat = <?php echo $lat; ?>;

const map = new maplibregl.Map({
    container: 'map',
    style: 'https://tiles.openfreemap.org/styles/liberty',
    center: [lng, lat],
    zoom: 12
});

map.addControl(new maplibregl.NavigationControl());

new maplibregl.Marker()
    .setLngLat([lng, lat])
    .addTo(map);
</script>

<script>
let index = 0;
const images = document.querySelectorAll(".slide-img");

if (images.length > 0) {
    setInterval(() => {
        images[index].classList.remove("opacity-100");
        images[index].classList.add("opacity-0");

        index = (index + 1) % images.length;

        images[index].classList.remove("opacity-0");
        images[index].classList.add("opacity-100");
    }, 3000);
}
</script>

<script>
const btn = document.getElementById("themeToggle");

function ThemeChecker() {
    let obj = localStorage.getItem('theme');

    if (!obj) {
        localStorage.setItem('theme', 'light');
        return 'light';
    }
    return obj;
}

function applyTheme() {
    let theme = ThemeChecker();

    document.documentElement.classList.toggle("dark", theme === 'dark');
}

applyTheme();

btn.onclick = () => {
    let newTheme =
        (localStorage.getItem('theme') === 'light') ? 'dark' : 'light';

    localStorage.setItem('theme', newTheme);
    applyTheme();
};

window.addEventListener("storage", (event) => {
    if (event.key === "theme") {
        applyTheme();
    }
});
</script>

</body>
</html>