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
                <div id="navigation" class="flex gap-2 mb-2">
                    <button id="prev" class="px-2 py-1 bg-gray-200 dark:bg-slate-800 rounded">prev</button>
                    <button id="next" class="px-2 py-1 bg-gray-200 dark:bg-slate-800 rounded">next</button>
                </div>

                <div id="parent_node">
                    ${group.map(d => {
                        const images = (d.images || "").split(";;;").filter(Boolean);

                        return `
                            <div class="space-y-2">

                                <h6 class="font-semibold">${d.Name}</h6>

                                <div class="text-sm text-gray-600 dark:text-gray-300"><b>lat:</b> ${d.lat}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300"><b>lng:</b> ${d.lng}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300"><b>Email:</b> ${d.email}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-300"><b>Number:</b> ${d.number}</div>

                                <hr class="border-gray-300 dark:border-slate-700">

                                <div class="space-y-2">
                                    ${images.map(img => {
                                        const parts = img.split("---");
                                        const path = parts[1];

                                        return `
                                            <img src="${path}"
                                                class="w-full h-40 object-cover rounded-lg border border-gray-200 dark:border-slate-700">
                                        `;
                                    }).join('')}
                                </div>

                                <a class="text-blue-600 dark:text-blue-400 hover:underline"
                                   href="./SeePetCenter.php?id=${d.id}">
                                   see this center
                                </a>

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

    <script src="../js/themetoggle.js"></script>
</body>
</html>