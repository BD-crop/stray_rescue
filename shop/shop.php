<?php

    include_once __DIR__ ."/../PDO/PDO.php";

    $obj = PDO_class::initializer();

    $result = $obj -> getProductsPagination(0 );

    $result = json_encode($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pet Shop</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:#f5f5f5;
}

/* NAVBAR */
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
    font-size:16px;
}

/* CONTAINER */
.container{
    width:90%;
    max-width:1200px;
    margin:40px auto;
}

/* SEARCH */
.search-container{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    margin-bottom:40px;
}

.search-form{
    display:flex;
    gap:15px;
    flex-wrap:wrap;
}

.search-form select,
.search-form input{
    padding:14px;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:16px;
}

.search-form input{
    flex:1;
    min-width:250px;
}

.search-form button{
    padding:14px 24px;
    border:none;
    border-radius:8px;
    background:#222;
    color:white;
    cursor:pointer;
    font-size:16px;
}

/* PRODUCTS */
.products{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:25px;
}

/* CARD */
.card{
    background:white;
    border-radius:14px;
    overflow:hidden;
    box-shadow:0 2px 10px rgba(0,0,0,0.08);
    transition:0.2s;
}

.card:hover{
    transform:translateY(-5px);
}

.card img{
    width:100%;
    height:220px;
    object-fit:cover;
}

.card-content{
    padding:18px;
}

.card-content h3{
    margin-bottom:10px;
}

.description{
    color:#666;
    margin-bottom:14px;
}

.price{
    font-size:22px;
    font-weight:bold;
    margin-bottom:8px;
}

.stock{
    color:green;
    margin-bottom:10px;
}

.category{
    font-size:12px;
    color:#555;
    margin-bottom:12px;
}

.add-btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:8px;
    background:#222;
    color:white;
    cursor:pointer;
    font-size:15px;
}

.add-btn:hover{
    background:#444;
}

</style>

</head>
<body>

<!-- NAVBAR -->
<div class="navbar">

    <div class="logo">
        Pet Shop
    </div>

    <div class="nav-links">
        <a href="#">Shop</a>
        <a href="#">Cart</a>
        <a href="#">Orders</a>
    </div>

</div>

<!-- MAIN -->
<div class="container">

    <!-- SEARCH -->
    <div class="search-container">

        <form class="search-form">

            <select>
                <option>Food</option>
                <option>Toy</option>
                <option>Grooming</option>
                <option>Health</option>
            </select>

            <input type="text" placeholder="Search products...">

            <button type="submit">Search</button>

        </form>

    </div>

    <!-- PRODUCTS -->
    <div class="products" id="products"></div>

</div>

<script>

const data = <?= $result ?>;

console.log(data);

const products = data.products || [];

const container = document.getElementById("products");


function renderProducts(){

    container.innerHTML = products.map(p => {

        const images = p.images ? p.images.split(";;") : [];

        return `
        <div class="card">

            <img src="${images[0] || 'https://via.placeholder.com/300'}">

            <div class="card-content">

                <h3>${p.name}</h3>

                <p class="description">
                    ${p.description}
                </p>

                <div class="price">
                    ৳${p.price}
                </div>

                <div class="stock">
                    ${p.stock > 0 ? "In Stock" : "Out of Stock"}
                </div>

                <div class="category">
                    ${p.categories ? p.categories.split(";;").join(", ") : ""}
                </div>

                <button class="add-btn">
                    Add To Cart
                </button>

            </div>

        </div>
        `;
    }).join('');
}

renderProducts();

</script>

</body>
</html>