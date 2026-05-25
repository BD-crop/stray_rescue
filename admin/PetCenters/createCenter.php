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
</head>

<body>

<h2>Create Pet Center</h2>

<form method="POST">

    <label>Name</label><br>
    <input type="text" name="name" required><br><br>

    <label>Type</label><br>
    <select name="type" required>
        <option value="gromming center">Grooming Center</option>
        <option value="veterenarian hospital">Veterinarian Hospital</option>
        <option value="park">Park</option>
        <option value="other">Other</option>
    </select><br><br>

    <label>Email</label><br>
    <input type="email" name="email"><br><br>

    <label>Contact</label><br>
    <input type="text" name="contact"><br><br>

    <input type="hidden" name="lat" id="lat">
    <input type="hidden" name="lng" id="lng">

    <button type="submit" name="submit">Create</button>

</form>

<br>

<h3>Pick Location on Map</h3>
<div id="map" style="width:100%; height:400px;"></div>

<p id="locationText">Click map to select location</p>

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

</body>
</html>