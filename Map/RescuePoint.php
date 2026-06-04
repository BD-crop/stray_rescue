<?php

    include_once __DIR__ . "/../PDO/PDO.php";

    $obj = PDO_class::initializer();

    $res = $obj->get_all_points(0, 100000);

    $res_json = json_encode($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rescue Map</title>

<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.8.1/nouislider.min.css">
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
        }
</script>

</head>

<body class="min-h-screen bg-gray-100 dark:bg-slate-950 text-gray-900 dark:text-gray-100 transition-all duration-500">

    <div class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md bg-white/70 dark:bg-slate-900/70 border-b border-gray-200 dark:border-slate-700">

        <a class="hidden sm:block px-4 py-2 rounded-xl bg-indigo-200 text-gray-700 dark:bg-slate-800 dark:text-gray-200 hover:scale-105 transition"
            href="/index.php">
            Go Back
        </a>

        <button id="themeToggle"
            class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-200 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg">
            Theme
        </button>

    </div>

    <div id="container"
        class="flex flex-col lg:flex-row gap-4 p-3">

        <div id="map"
            class="w-full lg:w-2/3 h-[600px] rounded-xl overflow-hidden border border-gray-300 dark:border-slate-700">
        </div>

        <div id="popup_shower"
            class="w-full lg:w-1/3 bg-gray-50 dark:bg-slate-900/70 border border-gray-200 dark:border-slate-700 rounded-xl p-4 overflow-auto min-h-[200px]">

            <div class="text-gray-500 dark:text-gray-400 text-center pt-10">
                Click a marker to view rescue details
            </div>

        </div>

    </div>

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

const popups12=[];
const markers=[];

function map_updater(res) {

    const data = res;

    popups12.forEach(p => p.remove());
    markers.forEach(p => p.remove());

    if (!data || data.count == 0) {
        alert("No rescue posts found");
        return;
    }

    const posts = {};

    data.result.forEach(d => {
        const key = `${d.rescue_point_location_latitude},${d.rescue_point_location_longtitude}`;
        if (!posts[key]) posts[key] = [];
        posts[key].push(d);
    });

    const bounds = new maplibregl.LngLatBounds();
    let index = 0;

    for (const key in posts) {

        const group = posts[key];

        const lat = parseFloat(group[0].rescue_point_location_latitude);
        const lng = parseFloat(group[0].rescue_point_location_longtitude);

        if (isNaN(lat) || isNaN(lng)) continue;

        bounds.extend([lng, lat]);

        let popup = new maplibregl.Popup({ offset: 25 });

        popups12.push(popup);

        popup._html = `
            <div id="navigation" class="flex gap-2 mb-2">
                <button id="prev" class="px-2 py-1 bg-gray-200 dark:bg-slate-800 rounded">prev</button>
                <button id="next" class="px-2 py-1 bg-gray-200 dark:bg-slate-800 rounded">next</button>
            </div>

            <div id="parent_node">

                ${group.map(datas => `
                    <div class="space-y-1">

                        <h6 class="font-semibold">${datas.rescue_point_name}</h6>

                        <div class="text-sm text-gray-600 dark:text-gray-300"><b>latitude:</b> ${datas.rescue_point_location_latitude}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300"><b>longitude:</b> ${datas.rescue_point_location_longtitude}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300"><b>SuperVisor :</b> ${datas.emp_name}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-300"><b>SuperVisor email:</b> ${datas.email}</div>

                        <hr class="border-gray-300 dark:border-slate-700">

                        <a class="text-blue-600 dark:text-blue-400 hover:underline"
                           href="./point.php?point_id=${datas.rescue_point_id}">
                           see this point
                        </a>

                    </div>
                `).join('')}

            </div>
        `;

        popup.index = index++;

        let newMarker = new maplibregl.Marker();

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

};

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

async function fetchPosts(x, y){
    let data = await fetch(`../RescuePoint/getAllRescuePoint.php?offset=${x-1}&limit=${y-x+1}`);
    let res = await data.json();
    map_updater(res);
}

slider.noUiSlider.on('change', function (values) {
    popLink.innerText = "min: "+(Math.trunc(values[0])) +" max: "+ (Math.trunc(values[1]));
    fetchPosts(Math.trunc(values[0]), Math.trunc(values[1]));
});

</script>

<script src="../js/themetoggle.js"></script>

</body>
</html>