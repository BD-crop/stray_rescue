<?php

session_start();

include_once __DIR__ . "/../PDO/PDO.php";

if (isset($_SESSION['id'])) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (!isset($_POST['submit'])) {
            $msg = urlencode("Not correct Submission");
            header("Location: upload_post.php?msg=$msg");
            exit();
        }

        if (
            !isset($_FILES['fileToUpload']) ||
            !isset($_POST['post']) ||
            !isset($_POST['species_type']) ||
            !isset($_POST['gender']) ||
            !isset($_POST['age']) ||
            !isset($_POST['latitude']) ||
            !isset($_POST['longitude'])
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

<style>

body {
    background:#0f172a;
    color:white;
}

/* MAIN LAYOUT */
.wrapper {
    display:flex;
    gap:20px;
    max-width:1200px;
    margin:40px auto;
    padding:20px;
}

/* FORM BOX */
.form-box {
    flex:1;
    background:rgba(255,255,255,0.05);
    border:1px solid rgba(255,255,255,0.1);
    padding:20px;
    border-radius:15px;
}

/* MAP BOX */
.map-box {
    flex:1;
    border-radius:15px;
    overflow:hidden;
    min-height:500px;
}

#map {
    width:100%;
    height:100%;
}

/* RESPONSIVE */
@media(max-width: 900px) {
    .wrapper {
        flex-direction:column;
    }

    .map-box {
        height:400px;
    }
}

</style>

</head>

<body>

<div class="wrapper">

    <!-- FORM -->
    <div class="form-box">

        <?php
            if (isset($_GET['msg'])) {
                echo "<div class='alert alert-danger'>"
                    . htmlspecialchars($_GET['msg']) .
                "</div>";
            }
        ?>

        <form id="rescueForm" enctype="multipart/form-data" method="POST">

            <input type="file" name="fileToUpload" required class="form-control mb-2">

            <input type="text" name="post" placeholder="Post description" class="form-control mb-2">

            <select name="species_type" class="form-control mb-2">
                <option value="cat">Cat</option>
                <option value="dog">Dog</option>
                <option value="bird">Bird</option>
                <option value="other">Other</option>
            </select>

            <select name="gender" class="form-control mb-2">
                <option value="M">Male</option>
                <option value="F">Female</option>
                <option value="O">Other</option>
            </select>

            <input type="number" name="age" placeholder="Age" class="form-control mb-2">

            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <button type="submit" name="submit" class="btn btn-primary w-100">
                Submit
            </button>

        </form>

    </div>

    <!-- MAP -->
    <div class="map-box">
        <h3 id="c1"></h3>
        <div id="map"></div>
    </div>

</div>

<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>

const defaultLng = 90.4125;
const defaultLat = 23.8103;



const c1 = document.getElementById("c1");

get23(defaultLat ,defaultLng);


async function get23(lat, lng) {

    const url = `http://localhost:80/dashboard/proxy/proxy.php?lat=${lat}&lng=${lng}`;

    const res = await fetch(url);

    const data = await res.json(); 
    console.log(data);
    if(data.address === undefined || data.address.country_code !== 'bd' ){
        marker.setLngLat([90.4125 ,23.8103 ]);
        get23(23.8103, 90.4125);
        return;
    }
    c1.textContent = data.display_name;
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
    get23(lngLat.lat , lngLat.lng);
});

map.on('click', (e) => {
    marker.setLngLat(e.lngLat);
    document.getElementById("latitude").value = e.lngLat.lat;
    document.getElementById("longitude").value = e.lngLat.lng;
        get23(e.lngLat.lat, e.lngLat.lng);
});

</script>

</body>
</html>