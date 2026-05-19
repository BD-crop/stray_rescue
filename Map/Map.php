<?php
include_once __DIR__ . "/../PDO/PDO.php";

$obj = PDO_class::initializer();

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$res = $obj->see_rescue_posts($offset);

$res_json = json_encode($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rescue Map</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            width: 100%;
            height: 100vh;
        }

        .popup-img {
            width: 200px;
            margin-top: 10px;
            border-radius: 5px;
        }
    </style>
</head>

<body>

<div id="map"></div>

<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>
(function () {

    const data = <?php echo $res_json; ?>;

    const map = new maplibregl.Map({
        container: 'map',
        style: 'https://demotiles.maplibre.org/style.json',
        center: [90.4125, 23.8103],
        zoom: 7
    });

    map.addControl(new maplibregl.NavigationControl());

    if (!data || data.count == 0) {
        alert("No rescue posts found");
        return;
    }

    const bounds = new maplibregl.LngLatBounds();

    data.posts.forEach(datas => {

        const lat = parseFloat(datas.post_loc_latitude);
        const lng = parseFloat(datas.post_loc_longtitude);

        if (isNaN(lat) || isNaN(lng)) {
            return;
        }

        bounds.extend([lng, lat]);

        const popup = new maplibregl.Popup({
            offset: 25
        }).setHTML(`
            <div>
                <h6>${datas.rescue_post}</h6>

                <div>
                    <strong>Species:</strong>
                    ${datas.animal_species_type}
                </div>

                <div>
                    <strong>Gender:</strong>
                    ${datas.animal_gender_type}
                </div>

                <div>
                    <strong>Age:</strong>
                    ${datas.animal_age}
                </div>

                <div>
                    <strong>Time:</strong>
                    ${datas.post_time_stamp}
                </div>

                <img 
                    class="popup-img"
                    src="${datas.rescue_post_image_link}" 
                    alt="Rescue Image"
                >
            </div>
        `);

        new maplibregl.Marker()
            .setLngLat([lng, lat])
            .setPopup(popup)
            .addTo(map);
    });

    if (!bounds.isEmpty()) {
        map.fitBounds(bounds, {
            padding: 50,
            maxZoom: 15
        });
    }

})();
</script>

</body>
</html>