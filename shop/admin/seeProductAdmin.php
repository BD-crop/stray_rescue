<?php

include_once __DIR__ . "/auth.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

$obj = PDO_class::initializer();

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Missing Product ID");
}



if (isset($_POST['submit'])) {

    if (
        empty($_POST['product_id']) ||
        empty($_POST['description']) ||
        empty($_POST['price']) ||
        empty($_POST['stock']) ||
        empty($_POST['past_price'])
    ) {

        $msg = urlencode("All fields must be present");

        header("Location: seeProductAdmin.php?id=$id&msg=$msg");
        exit();
    }

    $images = null;

    if (!empty($_FILES["fileToUpload"]["name"])) {
        $images = $obj->image_upload();
    }

    $images = ($images === "" || !$images) ? null : $images;

    $obj->updateProduct(
        $_POST['product_id'],
        $_POST['description'],
        $_POST['price'],
        $_POST['stock'],
        $_POST['past_price'],
        $images
    );

    $msg = urlencode("Product Updated");

    header("Location: seeProductAdmin.php?id=$id&msg=$msg");
    exit();
}



$res = $obj->get_product($id);

$json = json_encode($res);

$data = json_decode($json, true);

$product = $data['product_detail'][0] ?? null;

if (!$product) {
    die("Product not found");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Product Admin</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    background:#f5f5f5;
}


.navbar{
    width:100%;
    background:#222;
    padding:18px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logo{
    color:white;
    font-size:24px;
    font-weight:bold;
}

.nav-links{
    display:flex;
    gap:20px;
}

.nav-links a{
    text-decoration:none;
    color:white;
}


.wrapper{
    width:90%;
    max-width:1200px;
    margin:40px auto;
    display:flex;
    gap:30px;
    background:white;
    border-radius:14px;
    padding:25px;
}


.carousel{
    width:500px;
    overflow:hidden;
    border-radius:12px;
    position:relative;
    background:#000;
}

.carousel-track{
    display:flex;
    transition:transform 0.4s ease;
}

.carousel-track img{
    width:500px;
    height:400px;
    object-fit:cover;
    flex-shrink:0;
}

.carousel-btn{
    position:absolute;
    top:50%;
    transform:translateY(-50%);
    border:none;
    background:rgba(0,0,0,0.5);
    color:white;
    padding:10px 15px;
    cursor:pointer;
    font-size:20px;
    z-index:10;
}

.prev{
    left:10px;
}

.next{
    right:10px;
}

/* DETAILS */

.details{
    flex:1;
}

.title{
    font-size:32px;
    font-weight:bold;
}

.price{
    color:green;
    font-size:24px;
    margin-top:10px;
}

.stock{
    margin-top:10px;
    color:#444;
}

.description{
    margin-top:20px;
    line-height:1.6;
}

.badge{
    display:inline-block;
    margin-top:15px;
    padding:5px 10px;
    background:#222;
    color:white;
    border-radius:6px;
    font-size:12px;
}


.history{
    margin-top:25px;
    background:#fafafa;
    padding:15px;
    border-radius:10px;
}

.history-item{
    margin-bottom:8px;
    font-size:14px;
}

/* FORM */

.form-container{
    width:90%;
    max-width:1200px;
    margin:20px auto 50px auto;
    background:white;
    padding:25px;
    border-radius:14px;
}

.form-container h2{
    margin-bottom:20px;
}

input{
    width:100%;
    padding:14px;
    margin-bottom:15px;
    border:1px solid #ccc;
    border-radius:8px;
}

input:focus{
    outline:none;
    border-color:#222;
}

button{
    background:#222;
    color:white;
    border:none;
    padding:14px 20px;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#444;
}

.message{
    width:90%;
    max-width:1200px;
    margin:20px auto;
    background:#d4edda;
    color:#155724;
    padding:15px;
    border-radius:10px;
}

</style>

</head>

<body>


<div class="navbar">

    <div class="logo">
        Shop Admin
    </div>

    <div class="nav-links">
        <a href="../shop.php">Shop</a>
        <a href="./addProduct.php">Add Product</a>
        <a href="./cart.php">Cart</a>
    </div>

</div>


<?php if(isset($_GET['msg'])): ?>

<div class="message">
    <?= htmlspecialchars($_GET['msg']) ?>
</div>

<?php endif; ?>


<div class="wrapper">


    <div class="carousel">

        <button class="carousel-btn prev" onclick="moveSlide(-1)">
            ❮
        </button>

        <div class="carousel-track" id="carouselTrack">
        </div>

        <button class="carousel-btn next" onclick="moveSlide(1)">
            ❯
        </button>

    </div>


    <div class="details">

        <div class="title">
            <?= htmlspecialchars($product['name']) ?>
        </div>

        <div class="price">
            $<?= htmlspecialchars($product['price']) ?>
        </div>

        <div class="stock">
            Stock: <?= htmlspecialchars($product['stock']) ?>
        </div>

        <div class="description">
            <?= htmlspecialchars($product['description']) ?>
        </div>

        <div class="badge">
            Product ID:
            <?= htmlspecialchars($product['product_id']) ?>
        </div>


        <div class="history">

            <h3>Price History</h3>

            <?php if(!empty($data['result'])): ?>

                <?php foreach($data['result'] as $history): ?>

                    <div class="history-item">

                        Old:
                        <?= htmlspecialchars($history['old_price'] ?? 'NULL') ?>

                        →

                        New:
                        <?= htmlspecialchars($history['new_price']) ?>

                        (<?= htmlspecialchars($history['changed_at']) ?>)

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <div class="history-item">
                    No history available
                </div>

            <?php endif; ?>

        </div>

    </div>

</div>


<div class="form-container">

    <h2>Update Product</h2>

    <form method="POST" enctype="multipart/form-data">

        <input
            type="hidden"
            name="product_id"
            value="<?= htmlspecialchars($product['product_id']) ?>"
        >

        <label>Description</label>

        <input
            type="text"
            name="description"
            value="<?= htmlspecialchars($product['description']) ?>"
        >

        <label>Price</label>

        <input
            type="number"
            name="price"
            value="<?= htmlspecialchars($product['price']) ?>"
        >

        <label>Stock</label>

        <input
            type="number"
            name="stock"
            value="<?= htmlspecialchars($product['stock']) ?>"
        >

        <label>Past Price</label>

        <input
            type="number"
            name="past_price"
            value="<?= htmlspecialchars($product['price']) ?>"
        >

        <label>Upload New Image</label>

        <input
            type="file"
            name="fileToUpload"
        >

        <button type="submit" name="submit">
            Update Product
        </button>

    </form>

</div>

<script>

const data = <?= $json ?>;

const images = data.images || [];

const track = document.getElementById("carouselTrack");

let currentIndex = 0;


function renderImages() {

    if(images.length === 0){

        track.innerHTML = `
            <img src="https://via.placeholder.com/500x400?text=No+Image">
        `;

        return;
    }

    track.innerHTML = images.map(img => `
        <img src="${img.image_path}" alt="product image">
    `).join('');
}

renderImages();



function moveSlide(step){

    if(images.length === 0) return;

    currentIndex += step;

    if(currentIndex < 0){
        currentIndex = images.length - 1;
    }

    if(currentIndex >= images.length){
        currentIndex = 0;
    }

    track.style.transform =
        `translateX(-${currentIndex * 500}px)`;
}



setInterval(() => {

    moveSlide(1);

}, 4000);

</script>

</body>
</html>