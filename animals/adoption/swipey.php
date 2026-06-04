<?php
include_once __DIR__ . "/../../PDO/PDO.php";

$obj = PDO_class::initializer();
session_start();

$role = $obj->type_of_user();

if ($role != 'user') {
    header("Location: ../index.php");
    exit();
}

$properties = $obj->getAllProperties();

$assos = [
    'vaccination' => "bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-full m-1",
    'gender'      => "bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-full m-1",
    'activity'    => "bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full m-1",
    'loves'       => "bg-purple-700 hover:bg-purple-800 text-white font-bold py-2 px-4 rounded-full m-1",
    'other'       => "bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 px-4 rounded-full m-1"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Swipey</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-slate-950 text-gray-900 dark:text-gray-100 min-h-screen">

<div class="flex h-screen gap-4 p-4">

    <div class="w-1/3 bg-blue-50 dark:bg-slate-900 rounded-2xl p-4 overflow-y-auto border border-blue-200 dark:border-slate-700">

        <h2 class="text-xl font-bold mb-4">Filters</h2>

        <div id="already_choosen" class="flex flex-wrap gap-2 mb-4 bg-green-50 dark:bg-slate-800 p-2 rounded-xl min-h-12"></div>

        <div id="choices" class="flex flex-wrap gap-2 bg-blue-50 dark:bg-slate-800 p-2 rounded-xl">

            <?php foreach ($properties as $prop): ?>
                <?php
                    $raw = $prop['property'];
                    $array = explode('--', $raw);

                    $key = $array[0] ?? '';
                    $value = $array[1] ?? '';

                    $let = $assos[$key] ?? $assos['other'];
                ?>

                <button
                    class="property_button <?= $let ?>"
                    id="<?= htmlspecialchars($value) ?>">
                    <?= htmlspecialchars($key) ?> -> <?= htmlspecialchars($value) ?>
                </button>

            <?php endforeach; ?>

        </div>
    </div>

    <div class="w-2/3 bg-white dark:bg-slate-900 rounded-2xl relative overflow-hidden flex items-center justify-center">

        <div id="swipe_body" class="w-full h-full relative"></div>

        <button id="prevBtn"
            class="absolute left-4 top-1/2 -translate-y-1/2 bg-black/50 text-white px-4 py-2 rounded-xl">
            ←
        </button>

        <button id="nextBtn"
            class="absolute right-4 top-1/2 -translate-y-1/2 bg-black/50 text-white px-4 py-2 rounded-xl">
            →
        </button>

    </div>

</div>

<script>

const swipe_body = document.getElementById("swipe_body");

let globalData = [];
let index = 0;

async function fetchFrom() {

    let filters = [];

    document.querySelectorAll('.choosen_property').forEach(e => {
        filters.push(e.id);
    });

    const res = await fetch("fetchAdoptionAnimals.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ filters }),
        cache: "no-store"
    });

    globalData = await res.json();
    console.log(globalData);
    index = 0;

    render();
}

function getImages(str) {
    if (!str) return [];
    return str.split(";;;");
}

function render() {

    swipe_body.innerHTML = "";

    if (globalData.length === 0) {
                return;
    };

    if (index < 0) index = 0;
    if (index >= globalData.length) index = globalData.length - 1;

    const item = globalData[index];

    const images = getImages(item.image_path);

    let props = "";

    if (item.animal_properties) {
        item.animal_properties.split(";;;").forEach(e => {
            const p = e.split("--");
            props += `<span class="text-xs px-2 py-1 bg-blue-500 text-white rounded-full m-1">${p[0]} -> ${p[1]}</span>`;
        });
    }

    swipe_body.innerHTML = `
        <div class="absolute inset-0 flex flex-col bg-white dark:bg-slate-900">

            <div class="flex-1">
                <img class="w-full h-full object-cover" src="${images[0] || ''}">
            </div>

            <div class="p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>${item.animal_name}</span>
                    <span>Age ${item.animal_age}</span>
                </div>

                <div class="text-xs text-gray-500">
                    SOS ${item.health_status}
                </div>

                <div class="flex flex-wrap">
                    ${props}
                </div>

                <a href="./individualListing.php?id=${item.animal_id}"
                   class="block text-center mt-3 py-2 bg-purple-600 text-white rounded-xl">
                    View Details ->
                </a>
            </div>

        </div>
    `;
}

document.getElementById("nextBtn").onclick = () => {
    if (index < globalData.length - 1) {
        index++;
        render();
    }
};

document.getElementById("prevBtn").onclick = () => {
    if (index > 0) {
        index--;
        render();
    }
};

function bindPropertyButtons() {
    fetchFrom();

    document.querySelectorAll('.property_button').forEach(e => {
        e.onclick = function () {

            document.getElementById('already_choosen').innerHTML += `
                <button id="${e.id}" class="choosen_property ${e.className}">
                    ${e.innerText}
                </button>
            `;
            e.style.display="none";

            e.remove();
            bindChoosenButtons();
        };
    });
}

function bindChoosenButtons() {
    fetchFrom();

    document.querySelectorAll('.choosen_property').forEach(e => {
        e.onclick = function () {

            document.getElementById('choices').innerHTML += `
                <button id="${e.id}" class="property_button ${e.className}">
                    ${e.innerText}
                </button>
            `;
            e.style.display="none";

            e.remove();
            bindPropertyButtons();
        };
    });
}

bindPropertyButtons();
bindChoosenButtons();
fetchFrom();

</script>

</body>
</html>