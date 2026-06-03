<?php

session_start();

include_once __DIR__ . "/../PDO/PDO.php";

if (isset($_SESSION['id'])) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $obj12 = PDO_class::initializer();
        $role = $obj12->type_of_user();
        
        if($role !== 'user'){
            $msg = urlencode("Only Users can Post");
            header("Location: upload_post.php?msg=$msg");
            exit();
        }
            
        if (!isset($_POST['submit'])) {
            $msg = urlencode("Not correct Submission");
            header("Location: upload_post.php?msg=$msg");
            exit();
        }

        if (!isset($_POST['name']) ||
            !isset($_FILES['fileToUpload']) ||
            !isset($_POST['post']) ||
            !isset($_POST['species_type']) ||
            !isset($_POST['gender']) ||
            !isset($_POST['age']) ||
            !isset($_POST['latitude']) ||
            !isset($_POST['longitude']) ||
            !isset($_POST['address']) ||
            !isset($_POST['sos_level'])
        ) {
            $msg = urlencode("All fields must be present");
            header("Location: upload_post.php?msg=$msg");
            exit();
        }

        if ($_SESSION['type'] === "Users") {

            $res = urlencode(PDO_class::initializer()->upload_rescue_post());

            header("Location: post.php?post_id=$res");
            exit();

        } else {

            $msg = urlencode("Only users are allowed to post");
            header("Location: upload_post.php?msg=$msg");
            exit();
        }
    }

} else {

    echo "
        <h1>Need to log in before uploading any post</h1>
        <h3>
            <a href='./../auth/login.php'>
                go here for login
            </a>
        </h3>
    ";

    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Upload Rescue Post</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">

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

</head>

<body class="bg-gray-100 dark:bg-slate-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">

<div class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md bg-white/70 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

    <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
        href="../index.php">
        Home
    </a>

    <button id="themeToggle"
        class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg">
        Theme
    </button>

</div>

<div class="wrapper flex flex-col lg:flex-row gap-6 p-4">

    <div class="form-box w-full lg:w-1/2">

        <?php
            if (isset($_GET['msg'])) {
                echo "<div class='alert alert-danger dark:bg-red-900 dark:text-red-200 dark:border-red-700'>"
                    . htmlspecialchars($_GET['msg']) .
                "</div>";
            }
        ?>

        <form id="rescueForm" enctype="multipart/form-data" method="POST">

            <div id="imageContainer">
                <div class="image-input-group mb-2">
                    <input type="file" name="fileToUpload[]" required
                        class="form-control dark:bg-slate-800 dark:text-white dark:border-slate-600">
                </div>
            </div>

            <button type="button" id="addImageBtn"
                class="btn btn-secondary mb-3 dark:bg-slate-700 dark:text-white dark:border-slate-600">
                + Add Another Image
            </button>

            <input type="text" name="post"
                placeholder="Post description"
                class="form-control mb-2 dark:bg-slate-800 dark:text-white dark:border-slate-600">

            <input type="hidden" name="address" id="address">

            <input type="text" name="name" required
                class="form-control mb-2 dark:bg-slate-800 dark:text-white dark:border-slate-600"
                placeholder="Name">

            <select name="species_type"
                class="form-control mb-2 dark:bg-slate-800 dark:text-white dark:border-slate-600">
                <option value="cat">Cat</option>
                <option value="dog">Dog</option>
                <option value="bird">Bird</option>
                <option value="other">Other</option>
            </select>

            <select name="sos_level"
                class="form-control mb-2 dark:bg-slate-800 dark:text-white dark:border-slate-600">
                <option value="1">Normal/Healthy</option>
                <option value="2">Attention Needed Soon</option>
                <option value="3">Emergency</option>
            </select>

            <select name="gender"
                class="form-control mb-2 dark:bg-slate-800 dark:text-white dark:border-slate-600">
                <option value="M">Male</option>
                <option value="F">Female</option>
                <option value="O">Other</option>
            </select>

            <input type="number" name="age"
                placeholder="Age"
                class="form-control mb-2 dark:bg-slate-800 dark:text-white dark:border-slate-600">

            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <input type="text" name="animal_parent_id"
                placeholder="Parent UUID"
                class="form-control dark:bg-slate-800 dark:text-white dark:border-slate-600">

            <button type="submit" name="submit" class="btn btn-primary w-100">
                Submit
            </button>

        </form>

<script>
const imageContainer = document.getElementById("imageContainer");
const addImageBtn = document.getElementById("addImageBtn");

addImageBtn.onclick = () => {
    const wrapper = document.createElement("div");
    wrapper.className = "image-input-group mb-2";

    wrapper.innerHTML = `
        <div class="d-flex gap-2">
            <input type="file" name="fileToUpload[]" class="form-control dark:bg-slate-800 dark:text-white dark:border-slate-600" required>
            <button type="button" class="btn btn-danger remove-btn">✕</button>
        </div>
    `;

    wrapper.querySelector(".remove-btn").onclick = () => wrapper.remove();
    imageContainer.appendChild(wrapper);
};
</script>

    </div>

    <div class="map-box w-full lg:w-1/2 dark:bg-slate-900 dark:text-white">

        <h3 id="c1" class="mb-2"></h3>

        <div id="map"
            class="w-full h-[500px] rounded-lg overflow-hidden border border-gray-300 dark:border-slate-700">
        </div>

    </div>

</div>

<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>

const defaultLng = 90.4125;
const defaultLat = 23.8103;

const c1 = document.getElementById("c1");

get23(defaultLat, defaultLng);

async function get23(lat, lng) {

    const url = `http://localhost:80/dashboard/proxy/proxy.php?lat=${lat}&lng=${lng}`;

    const res = await fetch(url);
    const data = await res.json();

    if (!data.address || data.address.country_code !== 'bd') {
        marker.setLngLat([90.4125, 23.8103]);
        get23(23.8103, 90.4125);
        return;
    }

    c1.textContent = data.display_name;
    document.getElementById("address").value = data.display_name || "";
}

const map = new maplibregl.Map({
    container: 'map',
    style: 'https://tiles.openfreemap.org/styles/liberty',
    center: [defaultLng, defaultLat],
    zoom: 9
});

map.addControl(new maplibregl.NavigationControl());

const marker = new maplibregl.Marker({ draggable: true })
    .setLngLat([defaultLng, defaultLat])
    .addTo(map);

document.getElementById("latitude").value = defaultLat;
document.getElementById("longitude").value = defaultLng;

marker.on('dragend', () => {
    const lngLat = marker.getLngLat();
    document.getElementById("latitude").value = lngLat.lat;
    document.getElementById("longitude").value = lngLat.lng;
    get23(lngLat.lat, lngLat.lng);
});

map.on('click', (e) => {
    marker.setLngLat(e.lngLat);
    document.getElementById("latitude").value = e.lngLat.lat;
    document.getElementById("longitude").value = e.lngLat.lng;
    get23(e.lngLat.lat, e.lngLat.lng);
});

</script>

<script src="../js/themetoggle.js"></script>

</body>
</html>