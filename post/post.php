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

$profile = PDO_class::initializer()->is_poster($id,$_SESSION['id']);

if ($profile === 1) {

    $self = true ;
} 


if($var && $self){
}


$obj = PDO_class::initializer();

$data = $obj->see_rescue_post($id);

if (!$data) {
    exit("No post found");
}

$lng = (float)$data['post_loc_longtitude'];
$lat = (float)$data['post_loc_latitude'];

$imageLink = explode(';;;', $data['rescue_post_image_link']);

?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rescue Post</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">

<style>

body {
    background:#0f172a;
    color:white;
    font-family:Arial;
}

.wrapper {
    display:flex;
    gap:20px;
    max-width:1200px;
    margin:50px auto;
    padding:20px;
}

.card-custom {
    flex:1;
    background:rgba(255,255,255,0.05);
    border:1px solid rgba(255,255,255,0.1);
    border-radius:20px;
    overflow:hidden;
}

.card-img {
    width:100%;
    height:350px;
    object-fit:cover;
}

.card-body {
    padding:20px;
}

.badge-custom {
    background:#f97316;
    padding:6px 12px;
    border-radius:12px;
    font-size:12px;
    margin-right:5px;
}

.title {
    font-size:1.5rem;
    font-weight:700;
    margin-bottom:10px;
}

#map {
    flex:1;
    height:500px;
    border-radius:20px;
    overflow:hidden;
}

@media(max-width: 900px) {
    .wrapper {
        flex-direction:column;
    }

    #map {
        height:400px;
    }
}

</style>

</head>

<body>

<div class="wrapper">

    <div class="card-custom">

        <!-- CAROUSEL START -->
        <?php if (!empty($imageLink)) { ?>

        <div id="rescueCarousel" class="carousel slide" data-bs-ride="carousel">

            <div class="carousel-inner">

                <?php foreach ($imageLink as $index => $img) { ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                        <img src="<?php echo htmlspecialchars($img); ?>" class="d-block w-100 card-img">
                    </div>
                <?php } ?>

            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#rescueCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>

            <button class="carousel-control-next" type="button" data-bs-target="#rescueCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

        </div>

        <?php } ?>

        <div class="card-body">

            <div class="title">
                <?php echo htmlspecialchars($data['rescue_post']); ?>
            </div>

            <p>ID: <?php echo $data['rescue_post_id']; ?></p>

            <span class="badge-custom">Species: <?php echo $data['animal_species_type']; ?></span>
            <span class="badge-custom">Gender: <?php echo $data['animal_gender_type']; ?></span>
            <span class="badge-custom">Age: <?php echo $data['animal_age']; ?></span>

        </div>

    </div>

    <!-- MAP -->
    <div id="map1">

        <p>
            <?php
                $ob = json_decode(
                    file_get_contents("http://localhost:80/dashboard/proxy/proxy.php?lat=$lat&lng=$lng"),
                    true
                );

                echo $ob['display_name'];
            ?>
        </p>

        <div id="map"></div>

    </div>

</div>

<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>