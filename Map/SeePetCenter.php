<?php
    include __DIR__.'/../PDO/PDO.php';
    session_start();


    $obj = PDO_class::initializer();

    $level = $obj->find_employee_level();

    $id = $_GET['id'] ?? null;

    if (isset($_POST['submit'])) {
        $obj->insertRescueCenterImages($id);

        $id = urlencode($id);

        header("Location: viewPetCenter.php?id=$id");
        exit();
    }


    $obj = $obj->ReadRescueCenter($_GET['id']);


    $images = [];

    if (!empty($obj['images'])) {
        $images = array_filter(explode(';;;', $obj['images']));
    }
?>


<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rescue Center</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://unpkg.com/maplibre-gl/dist/maplibre-gl.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:
        linear-gradient(rgba(15,23,42,0.92), rgba(15,23,42,0.95)),
        url('https://images.unsplash.com/photo-1517849845537-4d257902454a?q=80&w=1400&auto=format&fit=crop');
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    min-height:100vh;
    font-family:Arial;
    color:white;
    padding:30px;
}

.wrapper{
    max-width:1400px;
    margin:auto;
    display:grid;
    grid-template-columns:1.1fr 0.9fr;
    gap:25px;
}

.left-side{
    display:flex;
    flex-direction:column;
    gap:20px;
}


.carousel{
    position:relative;
    width:100%;
    height:500px;
    overflow:hidden;
    border-radius:24px;
}

.carousel-track{
    display:flex;
    height:100%;
    transition:transform 0.5s ease;
}

.carousel-track img{
    width:100%;
    min-width:100%;
    height:100%;
    object-fit:cover;
}

.carousel-btn{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    width:52px;
    height:52px;
    border-radius:50%;
    border:none;
    background:rgba(0,0,0,0.55);
    color:white;
    font-size:24px;
    cursor:pointer;
    z-index:10;
}

.prev{ left:15px; }
.next{ right:15px; }


.card-custom{
    background:rgba(255,255,255,0.06);
    border:1px solid rgba(255,255,255,0.08);
    border-radius:24px;
}

.card-body{
    padding:28px;
}

.title{
    font-size:2rem;
    font-weight:bold;
    margin-bottom:20px;
}


.upload-form{
    display:flex;
    gap:12px;
    align-items:center;
}

.upload-form input[type="file"]{
    flex:1;
}


#map1{
    background:rgba(255,255,255,0.06);
    border-radius:24px;
}

.location-box{
    padding:22px;
    border-bottom:1px solid rgba(255,255,255,0.1);
}

#map{
    width:100%;
    height:620px;
}

.image-slide{
    position:relative;
    width:100%;
    min-width:100%;
    height:100%;
}

.image-slide img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.delete-btn{
    position:absolute;
    top:15px;
    right:15px;
    width:42px;
    height:42px;
    border-radius:50%;
    border:none;
    background:red;
    color:white;
    cursor:pointer;
}
</style>

</head>

<body>

<div class="wrapper">

<div class="left-side">

<div class="carousel">

<button class="carousel-btn prev" onclick="moveSlide(-1)">❮</button>

<div class="carousel-track" id="carouselTrack"></div>

<button class="carousel-btn next" onclick="moveSlide(1)">❯</button>

</div>
<?php

    if(isset($level) && $level <= 4  && $level  >= 0){
        ?>

        <form method="POST" enctype="multipart/form-data" class="upload-form">
        <input name="fileToUpload" type="file">
        <input type="submit" name="submit" value="Upload">
        </form>
    <?php
    }

?>


<div class="card-custom">
<div class="card-body">

<div class="title"><?= htmlspecialchars($obj['Name']); ?></div>

<div>ID: <?= $obj['id']; ?></div>
<div>Email: <?= $obj['email']; ?></div>
<div>Number: <?= $obj['number']; ?></div>

</div>
</div>

</div>

<div id="map1">

<div class="location-box">
<?php
$lat = $obj['lat'];
$lng = $obj['lng'];

$ob = json_decode(file_get_contents(
    "http://localhost:80/dashboard/proxy/proxy.php?lat=$lat&lng=$lng"
), true);

echo $ob['display_name'];
?>
</div>

<div id="map"></div>

</div>

</div>

<script src="https://unpkg.com/maplibre-gl/dist/maplibre-gl.js"></script>

<script>

const lng = <?= $obj['lng']; ?>;
const lat = <?= $obj['lat']; ?>;

const map = new maplibregl.Map({
    container:'map',
    style:'https://tiles.openfreemap.org/styles/liberty',
    center:[lng,lat],
    zoom:9
});

map.addControl(new maplibregl.NavigationControl());

new maplibregl.Marker()
.setLngLat([lng,lat])
.addTo(map);

</script>

<script>

const images = <?= json_encode($images) ?>;

const track = document.getElementById("carouselTrack");

let currentIndex = 0;

async function removeImages(imageId){
    try{
        let body = new URLSearchParams();
        body.append("imagePath", imageId);
        console.log(imageId);
        let res = await fetch("removeImages.php",{
            method:"POST",
            headers:{
                "Content-Type":"application/x-www-form-urlencoded"
            },
            body:body
        });

        let data = await res.json();
        location.reload();

    }catch(e){
        console.error(e);
    }
}

function renderImages(){

if(images.length===0){
    track.innerHTML = `<img src="https://via.placeholder.com/1200x700?text=No+Image">`;
    return;
}

track.innerHTML = images.map(img=>{
    let arr = img.split('---');

    return `
    <div class="image-slide">

    <?php

        if(isset($level) && $level <= 4  && $level  >= 0){
            ?>

        
                <button class="delete-btn"
                    onclick="removeImages('${arr[0]}')">
                    ✕
             </button>
        
        <?php
        }

    ?>

        <img src="${arr[1]}" />
    </div>
    `;
}).join('');

}

renderImages();

function moveSlide(step){
    if(images.length===0) return;

    currentIndex += step;

    if(currentIndex<0) currentIndex = images.length-1;
    if(currentIndex>=images.length) currentIndex = 0;

    track.style.transform = `translateX(-${currentIndex*100}%)`;
}

setInterval(()=>moveSlide(1),4000);

</script>

</body>
</html>