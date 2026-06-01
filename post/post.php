<?php

include_once __DIR__ . "/../auth/user_check.php";

$obj12 = PDO_class::initializer();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$id = $_GET["post_id"] ?? null;
$ani_id = $_GET["ani_id"] ?? null;


if (!$id && !$ani_id) {
    exit("No post id provided");
}

$self = 1;

$profile = $obj12->is_poster($id, $_SESSION['id']);

if ($profile === 1) {
    $self = true;
}

$data = null;

if ($ani_id) {
    $data = $obj12->see_rescue_post_by_animal_id($ani_id);
} else if ($id) {
    $data = $obj12->see_rescue_post($id);

}

if (!$data) {
    exit("No post found");
}

$lng = (float) $data['post_loc_longtitude'];
$lat = (float) $data['post_loc_latitude'];

$imageLink = explode(';;;', $data['rescue_post_image_link']);


// Images 

$list1 = explode(';;;', $data['animal_history']);
$list2 = [];

foreach ($list1 as $indi) {
    $data_temp = explode('||', $indi);

    array_push($list2, $data_temp[count($data_temp) - 1]);

}
$images = [];

foreach ($list2 as $indi) {
    $data_temp = explode('---', $indi);

    foreach ($data_temp as $indi2) {
        array_push($images, $indi2);
    }

}


$role = $obj12->type_of_user();


// history

$historyEntries = [];

$rawHistory = $data['animal_history'] ?? '';

if (!empty($rawHistory)) {

    $blocks = explode(';;;', $rawHistory);

    foreach ($blocks as $block) {

        $block = trim($block);
        if ($block === '')
            continue;

        $parts = explode('||', $block);

        $historyEntries[] = [
            'animal_id' => $parts[0] ?? null,
            'type' => $parts[1] ?? null,
            'description' => $parts[2] ?? null,
            'level' => $parts[3] ?? null,
            'created_by_type' => $parts[4] ?? null,
            'created_by' => $parts[5] ?? null,
            'created_at' => $parts[6] ?? null,

            'images' => isset($parts[7]) && $parts[7] !== ''
                ? explode('---', $parts[7])
                : []
        ];
    }
}

// family tree parsing

$tree = [];

$raw = $data['family_tree'] ?? '';

if (!empty($raw)) {

    $nodes = explode(';;;', $raw);

    foreach ($nodes as $node) {

        $node = trim($node);
        if ($node === '')
            continue;

        $p = explode('||', $node);

        $tree[] = [
            'name' => $p[0] ?? '',
            'id' => $p[1] ?? '',
            'species' => $p[2] ?? '',
            'gender' => $p[3] ?? '',
            'level' => (int) ($p[4] ?? 0),
            'order' => (int) ($p[5] ?? 0),
            'parent_id' => $p[6] ?? null,
            'image' => $p[7] ?? ''
        ];
    }
}

// family tree hierarchy



$map = [];
$roots = [];

// index nodes
foreach ($tree as $n) {
    $n['children'] = [];
    $map[$n['id']] = $n;
}

// attach children
foreach ($map as $id => &$node) {

    if (!empty($node['parent_id']) && isset($map[$node['parent_id']])) {
        $map[$node['parent_id']]['children'][] = &$node;
    } else {
        $roots[] = &$node;
    }
}

unset($node);



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

<body class="bg-gray-100 dark:bg-slate-900 text-gray-900 dark:text-gray-100 transition">

    <div class="flex justify-between items-center px-6 py-4 bg-white dark:bg-slate-800 shadow-md sticky top-0 z-50">



        <div class="flex gap-3 items-center">
            <a href="../index.php" class="px-4 py-2 rounded-lg bg-red-500 text-white transition">Home</a>
            <a href="#image" class="px-4 py-2 rounded-lg bg-purple-500 text-white transition">Image Section</a>
            <a href="#tree" class="px-4 py-2 rounded-lg bg-purple-500 text-white transition">Family Tree</a>
            <a href="#history" class="px-4 py-2 rounded-lg bg-purple-500 text-white transition">history Section</a>
            <?php if (!isset($_SESSION["id"])): ?>

            <?php elseif ($role !== 'xnonex'): ?>

                <a href="#Update" class="px-4 py-2 rounded-lg bg-purple-500 text-white">ADD UPDATE</a>
            <?php endif; ?>

            <button id="themeToggle" class="px-4 py-2 rounded-lg bg-black text-white dark:bg-white dark:text-black">
                Theme
            </button>

        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg overflow-hidden">

            <div class="relative w-full h-80 overflow-hidden bg-black">

                <?php foreach ($imageLink as $index => $img) { ?>
                    <img src="<?php echo htmlspecialchars($img); ?>"
                        class="slide-img absolute w-full h-full object-cover transition-opacity duration-500 <?php echo $index === 0 ? 'opacity-100' : 'opacity-0'; ?>">
                <?php } ?>

            </div>

            <div class="p-5 space-y-3 row-span-2">

                <h1 class="text-2xl font-bold">
                    <?= htmlspecialchars($data['name']); ?>
                </h1>
                <h1 class="text-sm">
                    <?php echo htmlspecialchars($data['rescue_post']); ?>
                </h1>

                <p class="text-sm text-gray-500 dark:text-gray-300">
                    ID: <?php echo $data['animal_id']; ?>
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


        </div>


        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-4 space-y-3 row-span-2">

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

        <div class="flex justify-around items-center dark:bg-slate-800 bg-white rounded-2xl shadow-lg p-4 space-y-3">
            <h1 class="text-2xl">Qr Code Image</h1>
            <img src="<?= $data['qr_image']; ?>" alt="">
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <label class="block text-2xl text-center mb-4">
            Images
        </label>

        <div id="image" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($images as $image): ?>
                <div
                    class="group overflow-hidden rounded-2xl bg-white shadow-md hover:shadow-xl transition-all duration-300">
                    <img src="<?= htmlspecialchars($image) ?>" alt="Animal Image"
                        class="w-full h-64 object-cover transition-transform duration-500 group-hover:scale-105">
                </div>
            <?php endforeach; ?>
        </div>
    </div>




    <?php if ($role !== 'xnonex'): ?>
        <div id="Update" class="max-w-3xl mx-auto mt-10 bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-6">

            <h2 class="text-2xl font-bold mb-4">Add Update</h2>

            <form action="update_post.php" method="POST" target="_blank" enctype="multipart/form-data" class="space-y-4">

                <input type="hidden" name="animal_id" value="<?= $data['animal_id']; ?>">

                <div>
                    <label class="block text-sm font-medium mb-1">Update Type</label>
                    <select name="level_text" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 
                            bg-white dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="sos_update">SOS Update</option>
                        <option value="vaccination_update">Vaccination Update</option>
                        <option value="general_update">General Update</option>
                    </select>
                    <select name="sos_level" class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-slate-600 
                            bg-white dark:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="1">Normal Animal</option>
                        <option value="2">Attention Needed</option>
                        <option value="3">Emergency</option>
                    </select>
                </div>
                <input type="text" name="level_description" placeholder="write description">
                <input type="hidden" name="created_by" value="<?= $_SESSION['id']; ?>">
                <input type="hidden" name="created_by_type" value="<?= $role; ?>">

                <div>
                    <label class="block text-sm font-medium mb-2">Upload Images</label>

                    <div id="imageContainer" class="space-y-2">

                        <div class="image-input-group flex items-center gap-2">
                            <input type="file" name="fileToUpload[]" required class="w-full text-sm file:mr-4 file:py-2 file:px-4 
                                    file:rounded-lg file:border-0 
                                    file:bg-purple-500 file:text-white 
                                    hover:file:bg-purple-600" />

                        </div>

                    </div>

                    <button type="button" id="addImageBtn"
                        class="mt-3 px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                        + Add Another Image
                    </button>
                </div>

                <button type="submit"
                    class="w-full py-2 rounded-lg bg-purple-600 text-white font-semibold hover:bg-purple-700 transition">
                    Submit Update
                </button>

            </form>
        </div>
        <script>
            const imageContainer = document.getElementById("imageContainer");
            const addImageBtn = document.getElementById("addImageBtn");

            addImageBtn.onclick = () => {
                const wrapper = document.createElement("div");
                wrapper.className = "image-input-group mb-2";

                wrapper.innerHTML = `
                    <div class="d-flex gap-2">
                        <input type="file" name="fileToUpload[]" class="form-control" required>
                        <button type="button" class="btn btn-danger remove-btn">✕</button>
                    </div>
                `;

                wrapper.querySelector(".remove-btn").onclick = () => wrapper.remove();

                imageContainer.appendChild(wrapper);
            };
        </script>
    <?php endif; ?>
    <div id="history" class="max-w-4xl mx-auto mt-12 space-y-6">

        <h2 class="text-2xl font-bold text-center">History Timeline</h2>

        <?php foreach ($historyEntries as $h): ?>

            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-lg p-5 border-l-4 border-purple-500">

                <div class="flex justify-between items-center text-sm text-gray-500 mb-2">
                    <span class="px-2 py-1 bg-gray-200 dark:bg-slate-700 rounded">
                        <?= htmlspecialchars($h['type']) ?>
                    </span>

                    <span>
                        <?= htmlspecialchars($h['created_at']) ?>
                    </span>
                </div>

                <p class="text-gray-800 dark:text-gray-200 mb-2">
                    <?= htmlspecialchars($h['description']) ?>
                </p>

                <div class="text-xs text-gray-400 mb-3">
                    Level: <?= htmlspecialchars($h['level']) ?> |
                    <!-- By: <?= htmlspecialchars($h['created_by_type']) ?> -->
                </div>

                <?php if (!empty($h['images'])): ?>
                    <div class="grid grid-cols-2 gap-2">
                        <?php foreach ($h['images'] as $img): ?>
                            <img src="<?= htmlspecialchars($img) ?>"
                                class="h-40 w-full object-cover rounded-lg hover:scale-105 transition">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </div>

    <div class="w-full flex justify-center overflow-x-auto py-10">

        <div class="flex flex-col items-center min-w-max">

            <label for="tree" class="block text-2xl font-bold text-center mb-8">
                Family Tree
            </label>

            <div id="tree" class="flex flex-col items-center">

                <?php

                function draw($nodes)
                {
                    foreach ($nodes as $n) {
                        ?>

                        <div class="flex flex-col items-center mb-10 relative">

                            <!-- NODE -->
                            <div
                                class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-lg w-56 text-center
                            hover:scale-105 transition-transform duration-300 border border-gray-200 dark:border-slate-700">

                                <img src="<?= htmlspecialchars($n['image']) ?>"
                                    class="w-16 h-16 mx-auto rounded-full object-cover mb-3 ring-2 ring-purple-500">

                                <div class="font-bold text-lg">
                                    <?= htmlspecialchars($n['name']) ?>
                                </div>

                                <div class="text-xs text-gray-500 mt-1">
                                    <?= htmlspecialchars($n['species']) ?> • <?= htmlspecialchars($n['gender']) ?>
                                </div>
                            </div>

                            <?php if (!empty($n['children'])): ?>

                                <!-- vertical line -->
                                <div class="w-px h-8 bg-gradient-to-b from-purple-500 to-gray-400"></div>

                                <!-- children row -->
                                <div class="flex gap-10 justify-center relative">

                                    <div class="absolute top-0 left-0 right-0 h-px bg-gray-300 dark:bg-slate-600"></div>

                                    <?php draw($n['children']); ?>

                                </div>

                            <?php endif; ?>

                        </div>

                        <?php
                    }
                }

                draw($roots);

                ?>

            </div>

        </div>

    </div>

    <script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

    <script>

        console.log(<?php echo (json_encode($data)); ?>);

        const lng = <?php echo $lng; ?>;
        const lat = <?php echo $lat; ?>;

        const map = new maplibregl.Map({
            container: 'map',
            style: 'https://tiles.openfreemap.org/styles/liberty',
            center: [lng, lat],
            zoom: 9
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