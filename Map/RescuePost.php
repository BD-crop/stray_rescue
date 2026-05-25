<?php

    include_once __DIR__ . "/../PDO/PDO.php";

    $obj = PDO_class::initializer();

    $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;

    $res = $obj->see_rescue_posts(0, 100000);

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.8.1/nouislider.min.css">

<style>

body {
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #eef2f7, #e6f0ff);
    font-family: Inter, Arial, Helvetica, sans-serif;
}

#control_controller {
    width: 75vw;
    margin: 20px auto 10px auto;
    padding: 16px;
    background: white;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}

#slider {
    margin: 10px 0 20px 0;
}

#control_controller input[type="range"] {
    width: 100%;
    accent-color: #2563eb;
    cursor: pointer;
}

#container {
    display: flex;
    gap: 20px;
    padding: 20px;
    align-items: flex-start;
}

#map {
    flex: 1;
    height: 90vh;
    border-radius: 26px;
    overflow: hidden;
    box-shadow: 0 18px 50px rgba(0,0,0,0.15);
    border: 1px solid rgba(255,255,255,0.3);
}

#popup_shower {
    width: 380px;
    height: 90vh;
    overflow-y: auto;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(12px);
    border-radius: 26px;
    padding: 22px;
    box-shadow: 0 18px 45px rgba(0,0,0,0.15);
    border: 1px solid rgba(255,255,255,0.5);
}

#navigation {
    display: flex;
    gap: 12px;
    margin-bottom: 18px;
}

#navigation button {
    flex: 1;
    border: none;
    padding: 12px;
    border-radius: 14px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 6px 15px rgba(37,99,235,0.25);
}

#navigation button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(37,99,235,0.35);
}

#navigation button:active {
    transform: scale(0.97);
}

#parent_node > div {
    animation: slideFade 0.25s ease;
}

@keyframes slideFade {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0px);
    }
}

h6 {
    font-size: 1.1rem;
    font-weight: 700;
    margin-bottom: 12px;
    color: #111827;
}

p, div {
    color: #374151;
}

.popup-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
    margin-top: 14px;
    border-radius: 18px;
    box-shadow: 0 8px 18px rgba(0,0,0,0.12);
}

a {
    display: inline-block;
    margin-top: 10px;
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

#popup_shower::-webkit-scrollbar {
    width: 8px;
}

#popup_shower::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

@media (max-width: 1000px) {
    #container {
        flex-direction: column;
    }

    #popup_shower {
        width: 100%;
        height: auto;
    }

    #map {
        height: 60vh;
    }

    #control_controller {
        width: 95vw;
    }
}

.marker {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 8px rgba(0,0,0,0.35);
}

.customLow svg g{
    fill: #22c55e !important;
}

.customMedium svg  g {
    fill: #f59e0b !important;
}

.customHigh svg g {
    fill: #ef4444 !important;


}


@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6);
    }
    70% {
        transform: scale(1.25);
        box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
    }
}

.noUi-connect {
    background-color: #2563eb !important;
}

.noUi-touch-area {
    background-color: #2563eb !important;
    border-radius: 25%;
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

    <div id="popup_shower">
        <div style="color:#6b7280;text-align:center;padding-top:40px;">
            Click a marker to view rescue details
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.8.1/nouislider.min.js"></script>
<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>

let state = -1;
popLink.innerText = "min: " + Math.trunc(1) + " max: " + Math.trunc(<?php echo $res['count']; ?>);

const map = new maplibregl.Map({
    container: 'map',
    style: 'https://tiles.openfreemap.org/styles/liberty',
    center: [90.4125, 23.8103],
    zoom: 7
});

map.addControl(new maplibregl.NavigationControl());

const popups12 = [];
const markers = [];

function map_updater(res) {

    const data = res;

    popups12.forEach(p => p.remove());
    markers.forEach(m => m.remove());

    if (!data || data.count == 0) {
        alert("No rescue posts found");
        return;
    }

    const posts = {};

    data.posts.forEach(d => {
        const key = `${d.post_loc_latitude},${d.post_loc_longtitude}`;
        if (!posts[key]) posts[key] = [];
        posts[key].push(d);
    });

    const bounds = new maplibregl.LngLatBounds();
    let index = 0;

    for (const key in posts) {

        const group = posts[key];

        const lat = parseFloat(group[0].post_loc_latitude);
        const lng = parseFloat(group[0].post_loc_longtitude);

        if (isNaN(lat) || isNaN(lng)) continue;

        bounds.extend([lng, lat]);

        let popup = new maplibregl.Popup({ offset: 25 });

        popups12.push(popup);

        let max = -1;

        popup._html = `
            <div id="navigation">
                <button id="prev">prev</button>
                <button id="next">next</button>
            </div>

            <div id="parent_node">

                ${group.map(datas => {
                    max = Math.max(max, datas.sos_level);
                    return `
                        <div>
                            <h6>${datas.rescue_post}</h6>

                            <div><b>Species:</b> ${datas.animal_species_type}</div>
                            <div><b>Gender:</b> ${datas.animal_gender_type}</div>
                            <div><b>Age:</b> ${datas.animal_age}</div>
                            <div><b>Time:</b> ${datas.post_time_stamp}</div>

                            <img class="popup-img" src="${datas.rescue_post_image_link}">

                            <hr>
                            <a href="../post/post.php?post_id=${datas.rescue_point_id}">see this post</a>
                        </div>
                    `;
                }).join('')}

            </div>
        `;

        const el = document.createElement("div");
        console.log( max);
        if (max == 3) el.className = "customHigh";
        else if (max == 2) el.className = "customMedium";
        else el.className = "customLow";
        popup.index = index++;

        let newMarker = new maplibregl.Marker(el);

        markers.push(newMarker);

        newMarker
            .setLngLat([lng, lat])
            .setPopup(popup)
            .addTo(map);

                    popup.on('open', () => {    

            state = popup.index;

            document.getElementById("popup_shower").innerHTML = popup._html;

            const view = {
                index: 0,
                elems: document.getElementById("parent_node").children,
                init() {
                    for (let e of this.elems) e.style.display = "none";
                    this.elems[this.index].style.display = "block";
                }
            };

            const prev = document.querySelector("#prev");
            const next = document.querySelector("#next");

            prev.onclick = () => {
                if (view.index === 0) return;
                view.elems[view.index].style.display = "none";
                view.index--;
                view.elems[view.index].style.display = "block";
            };

            next.onclick = () => {
                if (view.index + 1 === view.elems.length) return;
                view.elems[view.index].style.display = "none";
                view.index++;
                view.elems[view.index].style.display = "block";
            };

            view.init();
        });

        popup.on('close', () => {
            if (state !== popup.index) return;
            document.getElementById("popup_shower").innerHTML = "";
        });
}

    

    if (!bounds.isEmpty()) {
        map.fitBounds(bounds, {
            padding: 60,
            maxZoom: 6
        });
    }
}

map_updater(<?php echo $res_json; ?>);

const slider = document.getElementById('slider');

noUiSlider.create(slider, {
    start: [1, <?php echo $res['count']; ?>],
    connect: true,
    step: 1,
    range: {
        min: 1,
        max: <?php echo $res['count']; ?>
    }
});

async function fetchPosts(x, y) {
    let data = await fetch(`http://localhost:80/dashboard/Map/fetchRes.php?offset=${x-1}&limit=${y-x+1}`);
    let res = await data.json();
    map_updater(res);
}

slider.noUiSlider.on('change', function (values) {
    popLink.innerText = "min: " + Math.trunc(values[0]) + " max: " + Math.trunc(values[1]);
    fetchPosts(Math.trunc(values[0]), Math.trunc(values[1]));
});

</script>

</body>
</html>