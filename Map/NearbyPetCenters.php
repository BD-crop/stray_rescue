<?php

include_once __DIR__ . "/../PDO/PDO.php";

$obj = PDO_class::initializer();

$res = $obj->getRescuePoints();

$res_json = json_encode($res, JSON_UNESCAPED_SLASHES);

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Rescue Map</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.8.1/nouislider.min.css">

<style>
body {
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #eef2f7, #e6f0ff);
    font-family: Arial, Helvetica, sans-serif;
}

#control_controller {
    width: 75vw;
    margin: 20px auto 10px auto;
    padding: 16px;
    background: white;
    border-radius: 18px;
}

#slider {
    margin: 10px 0 20px 0;
}

#container {
    display: flex;
    gap: 20px;
    padding: 20px;
}

#map {
    flex: 1;
    height: 90vh;
    border-radius: 26px;
}

#popup_shower {
    width: 380px;
    height: 90vh;
    overflow-y: auto;
    background: rgba(255,255,255,0.9);
    border-radius: 26px;
    padding: 22px;
}

#navigation {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
}

#navigation button {
    flex: 1;
    border: none;
    padding: 10px;
    border-radius: 10px;
    background: #2563eb;
    color: white;
}

/* =========================
IMAGE CAROUSEL LOGIC (ONLY COMMENTED SECTION AS REQUESTED)
========================= */

.image-slide{
    position:relative;
    width:100%;
    min-width:100%;
    height:220px;
    overflow:hidden;
    border-radius:16px;
    margin-top:10px;
}

.image-slide img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.delete-btn{
    position:absolute;
    top:10px;
    right:10px;
    width:36px;
    height:36px;
    border:none;
    border-radius:50%;
    background:rgba(239,68,68,0.9);
    color:white;
    font-size:18px;
    cursor:pointer;
    z-index:10;
}

.carousel-track{
    display:flex;
    transition:transform 0.4s ease;
    width:100%;
}
</style>

</head>

<body>

<div id="control_controller">
    <div id="slider"></div>
    <h3 id="popLink"></h3>
</div>

<div id="container">
    <div id="map"></div>
    <div id="popup_shower">Click a marker to view rescue details</div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.8.1/nouislider.min.js"></script>
<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>

let state = -1;

const map = new maplibregl.Map({
    container: 'map',
    style: 'https://tiles.openfreemap.org/styles/liberty',
    center: [90.4125, 23.8103],
    zoom: 7
});

map.addControl(new maplibregl.NavigationControl());

let markers = [];
let popups = [];

function map_updater(data){

    const dataset = Array.isArray(data) ? data : [];

    markers.forEach(m => m.remove());
    popups.forEach(p => p.remove());
    markers = [];
    popups = [];

    if(dataset.length === 0) return;

    const grouped = {};

    dataset.forEach(d => {
        const key = `${d.lat},${d.lng}`;
        if(!grouped[key]) grouped[key] = [];
        grouped[key].push(d);
    });

    const bounds = new maplibregl.LngLatBounds();
    let index = 0;

    for(const key in grouped){

        const group = grouped[key];

        const lat = parseFloat(group[0].lat);
        const lng = parseFloat(group[0].lng);

        bounds.extend([lng, lat]);

        const popup = new maplibregl.Popup({ offset: 25 });

        popups.push(popup);

        popup._html = `
            <div id="navigation">
                <button id="prev">prev</button>
                <button id="next">next</button>
            </div>

            <div id="parent_node">

                ${group.map(d => {

                    const images = (d.images || "").split(";;;").filter(Boolean);

                    return `
                        <div class="card-view">

                            <h6>${d.Name}</h6>

                            <div><b>lat:</b> ${d.lat}</div>
                            <div><b>lng:</b> ${d.lng}</div>
                            <div><b>Email:</b> ${d.email}</div>
                            <div><b>Number:</b> ${d.number}</div>

                            <hr>

                            <div class="carousel-track" data-index="0">

                                ${images.map(img => {
                                    const parts = img.split("---");

                                    const id = parts[0];
                                    const path = parts[1];

                                    return `
                                        <div class="image-slide">

                                            <img src="${path}">
                                        </div>
                                    `;
                                }).join('')}

                            </div>

                            <a href="./SeePetCenter.php?id=${d.id}">see this center</a>
                        </div>
                    `;
                }).join('')}

            </div>
        `;

        popup.index = index++;

        const marker = new maplibregl.Marker()
            .setLngLat([lng, lat])
            .setPopup(popup)
            .addTo(map);

        markers.push(marker);

        popup.on('open', () => {

            document.getElementById("popup_shower").innerHTML = popup._html;

            const parent = document.getElementById("parent_node");

            const view = {
                index: 0,
                elems: parent.children,

                init(){
                    for(let e of this.elems) e.style.display = "none";
                    if(this.elems.length > 0) this.elems[0].style.display = "block";
                }
            };

            document.getElementById("prev").onclick = () => {
                if(view.index === 0) return;
                view.elems[view.index].style.display = "none";
                view.index--;
                view.elems[view.index].style.display = "block";
            };

            document.getElementById("next").onclick = () => {
                if(view.index + 1 >= view.elems.length) return;
                view.elems[view.index].style.display = "none";
                view.index++;
                view.elems[view.index].style.display = "block";
            };

            view.init();
        });
    }

    if(!bounds.isEmpty()){
        map.fitBounds(bounds, { padding: 60, maxZoom: 6 });
    }
}

map_updater(<?php echo $res_json; ?>);

function deleteImage(id){
    console.log("delete image id:", id);
}

</script>

</body>
</html>