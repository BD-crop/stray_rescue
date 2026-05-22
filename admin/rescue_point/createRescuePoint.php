<?php

    include_once __DIR__ . "/../auth.php";
    include_once __DIR__ . "/../template/admin_check.php";

    if (isset($_POST['submit'])) {
        $obj = PDO_class::initializer();

        if (
            empty($_POST['manager_id']) ||

            empty($_POST['lat']) ||
            empty($_POST['lang']) ||
            empty($_POST['name'])
        ) {
            $msg = urlencode('all fields must be present');
            header("Location: createRescuePoint.php?msg=$msg");
            exit();
        }

        $level = $obj->find_employee_level();

        if (($level === null) || ($level > 1)) {
            $msg = urlencode(" must be an senior employee or upper ");
            header("Location: createRescuePoint.php?msg=$msg");
            exit();
        }

        $msg = urlencode($obj->create_rescue_point());
        header("Location: seeIndividualLocation.php?id=$msg");
        exit();
    }

?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Assign Rescue Point</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">

<style>

body{
    background:#eef2f7;
}

.container{
    max-width:1200px;
}

.card{
    border:none;
    border-radius:12px;
    background:#ffffff;
}

.form-control{
    border:2px solid #d0d7e2;
    padding:10px;
    box-shadow:none;
}

.form-control:focus{
    border-color:#1a73e8;
    box-shadow:0 0 0 .2rem rgba(26,115,232,.15);
}

#map-wrapper{
    position:relative;
    width:100%;
    min-height:450px;
}

#map{
    width:100%;
    height:450px;
    border-radius:12px;
    overflow:hidden;
}

#Location_shown{
    font-size:15px;
    font-weight:600;
    color:#1a73e8;
    margin-bottom:10px;
}

.navbar{
    background:#1a73e8;
}

.location-badge{
    background:#e8f0fe;
    color:#1a73e8;
    padding:8px 12px;
    border-radius:8px;
    font-size:14px;
    margin-bottom:15px;
}

/* dropdown */
.search-results{
    position:absolute;
    top:100%;
    left:0;
    width:100%;
    margin-top:6px;
    background:#fff;
    border-radius:12px;
    border:1px solid #dbe4f0;
    overflow:hidden;
    z-index:999;
    display:none;
    max-height:300px;
    overflow-y:auto;
}

.search-item{
    padding:12px 14px;
    cursor:pointer;
    border-bottom:1px solid #eef2f7;
    transition:.2s;
}

.search-item:hover{
    background:#f3f8ff;
}

.search-item h6{
    margin:0;
    font-size:14px;
}

.search-item small{
    color:#6b7280;
}

.manager-card{
    border:1px solid #dbe4f0;
    border-radius:10px;
    padding:15px;
    background:#f8fbff;
}

</style>
</head>

<body>

<nav class="navbar navbar-dark shadow-sm">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">
            Assign Rescue Point
        </span>

        <a href="../index.php" class="btn btn-outline-light btn-sm">
            Home
        </a>
    </div>
</nav>

<div class="container mt-5">

<div class="row g-4 justify-content-center">

<!-- LEFT SIDE -->
<div class="col-lg-5">

<form class="card p-4 shadow-sm" id="rescueForm" action="" method="POST">

<h4 class="mb-4 text-center">
Assign Rescue Point
</h4>

<label class="fw-semibold">
Rescue Point Name
</label>

<input
    class="form-control mb-3"
    type="text"
    name="name"
    id="rescuePointName"
    required
>

<!-- SEARCH -->
<label class="fw-semibold">
Assign Manager
</label>

<div class="position-relative mb-3">

    <div class="d-flex gap-2">

        <input
            type="text"
            class="form-control"
            id="managerSearch"
            placeholder="Search employee..."
            autocomplete="off"
        >

        <button
            type="button"
            class="btn btn-primary"
            id="searchManagerBtn"
        >
            Search
        </button>

    </div>

    <div id="searchResults" class="search-results"></div>

</div>

<!-- SELECTED -->
<div
    class="manager-card mb-3"
    id="selectedManagerBox"
    style="display:none;"
>
    Assigned:
    <strong id="selectedManagerName"></strong>
</div>

    <input type="hidden" id="manager_id" name="manager_id">
    <input type="hidden" id="lat" name="lat" >
    <input type="hidden" id="lang" name="lang">

    <input type="submit" name="submit" class="btn btn-primary w-100"/>

</form>

</div>

<!-- RIGHT SIDE -->
<div class="col-lg-6">

<div class="card p-3 shadow-sm">

<div class="location-badge">
Click map to assign rescue point inside Bangladesh
</div>

<h5 id="Location_shown">
Dhaka, Bangladesh
</h5>

<div id="map-wrapper">
    <div id="map"></div>
</div>

</div>

</div>

</div>

</div>

<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>


const defaultLng = 90.4125;
const defaultLat = 23.8103;

const bangladeshBounds = [
    [88.0, 20.5],
    [92.8, 26.8]
];

const map = new maplibregl.Map({
    container: 'map',
    style: 'https://tiles.openfreemap.org/styles/liberty',
    center: [defaultLng, defaultLat],
    zoom: 6.5,
    maxBounds: bangladeshBounds
});

map.addControl(new maplibregl.NavigationControl());

const marker = new maplibregl.Marker({
    draggable:true,
    color:"#1a73e8"
})
.setLngLat([defaultLng, defaultLat])
.addTo(map);

function resetMarker(){

    marker.setLngLat([defaultLng, defaultLat]);

    map.flyTo({
        center:[defaultLng, defaultLat],
        zoom:6.5
    });

    updateLocation(defaultLat, defaultLng);
}

function updateLocation(lat,lng){

    document.getElementById("lat").value = lat;
    document.getElementById("lang").value = lng;
}

async function checkLocation(lat,lng){

    try{

        const res = await fetch(
            `http://localhost:80/dashboard/proxy/proxy.php?lat=${lat}&lng=${lng}`
        );

        const data = await res.json();
        console.log(data);
        if(
            !data.address ||
            data.address.country_code !== 'bd'
        ){
            alert("Only Bangladesh rescue points are allowed.");
            resetMarker();
            return false;
        }

        let locationText = "";

        if(data.address?.suburb)
            locationText += data.address.suburb + ", ";

        if(data.address?.city)
            locationText += data.address.city + ", ";

        else if(data.address?.state_district)
            locationText += data.address.state_district + ", ";

        if(data.address?.country)
            locationText += data.address.country;

        document.getElementById("Location_shown")
            .innerText = locationText;

        return true;

    }catch(err){

        console.error(err);
        return false;
    }
}


updateLocation(defaultLat, defaultLng);


map.on('click', async (e) => {

    const ok = await checkLocation(
        e.lngLat.lat,
        e.lngLat.lng
    );

    if(!ok) return;

    marker.setLngLat(e.lngLat);

    updateLocation(
        e.lngLat.lat,
        e.lngLat.lng
    );
});

marker.on('dragend', async () => {

    const pos = marker.getLngLat();

    const ok = await checkLocation(
        pos.lat,
        pos.lng
    );

    if(!ok) return;

    updateLocation(pos.lat, pos.lng);
});



const searchManagerBtn =
    document.getElementById('searchManagerBtn');

const managerSearch =
    document.getElementById("managerSearch");

const searchResults =
    document.getElementById("searchResults");

let typingTimer = null;

searchManagerBtn.onclick = async () => {
    fetchEmployees(managerSearch.value);
};

managerSearch.addEventListener("input", () => {

    clearTimeout(typingTimer);

    const query = managerSearch.value.trim();

    if(query.length < 1){

        searchResults.style.display = "none";

        return;
    }

    typingTimer = setTimeout(() => {

        fetchEmployees(query);

    }, 300);
});

async function fetchEmployees(query){

    const body =
        `name=${encodeURIComponent(query)}&rank=${encodeURIComponent(3)}&submit=submit`;

    try {

        const res = await fetch(
            "../Employee/searchEmployeesEX.php",
            {
                method: "POST",

                headers: {
                    "Content-Type":
                        "application/x-www-form-urlencoded"
                },

                body: body
            }
        );

        const data = await res.json();
        console.log(data);
        renderEmployees(data);

    }
    catch (error) {

        console.error(error);
    }
}


function renderEmployees(data) {

    searchResults.innerHTML = "";

    if (!data || data.length === 0) {

        searchResults.innerHTML = `
            <div class="search-item">
                <h6>No employee found</h6>
            </div>
        `;

        searchResults.style.display = "block";

        return;
    }

    data.forEach(emp => {

        const div = document.createElement("div");

        div.className = "search-item";

        div.innerHTML = `
            <h6>${emp.emp_name}</h6>
            <small>${emp.email}</small>
        `;

        div.onclick = () => {

            managerSearch.value = emp.emp_name;

            document.getElementById("manager_id").value =
                emp.emp_id;

            document.getElementById("selectedManagerBox")
                .style.display = "block";

            document.getElementById("selectedManagerName")
                .innerText = emp.emp_name;

            searchResults.style.display = "none";
        };

        searchResults.appendChild(div);
    });

    searchResults.style.display = "block";
}

document.addEventListener("click", (e) => {

    if(
        !managerSearch.contains(e.target) &&
        !searchResults.contains(e.target)
    ){
        searchResults.style.display = "none";
    }

});





</script>

</body>
</html>
