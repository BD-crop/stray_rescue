<?php
    include_once __DIR__.'/auth.php';


    if (isset($_POST["submit"])) {
        if (
            empty($_POST['product_name']) ||
            empty($_POST['production_description']) ||
            empty($_POST['product_price']) ||
            empty($_POST['product_stock']) ||
            empty($_POST['product_category']) ||
            empty($_FILES['fileToUpload']['name'])
        ) {
            $msg = urlencode("All Fields must be Present");
            header("Location: addProduct.php?msg=$msg");
            exit;
        }

        $type_arr = explode(' ', trim($_POST['product_category']));

        $valid = ['toy', 'food', 'styling', 'health'];

        foreach ($type_arr as $type) {
            $type = strtolower(trim($type));

            if (!in_array($type, $valid)) {
                $msg = urlencode("Unknown Category: $type");
                header("Location: addProduct.php?msg=$msg");
                exit;
            }
        }

        $obj = PDO_class::initializer();

        $price = (int) trim($_POST['product_price']);
        $stock = (int) trim($_POST['product_stock']);

        if ($price < 0 || $stock < 0) {
            $msg = urlencode("Numeric Value Cannot be Negative");
            header("Location: addProduct.php?msg=$msg");
            exit;
        }

        $images = $obj->image_upload();
        
        
        if ($images === '') {
            $msg = urlencode("Problem Uploading Image");
            header("Location: addProduct.php?msg=$msg");
            exit;
        }

        $product_id = urlencode($obj->create_product(
            $_POST['product_name'],
            $_POST['production_description'],
            $price,
            $stock,
            $type_arr,
            $images
        ));
        header("Location: seeProductAdmin.php?id=$product_id");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add a New Product</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f5f5;
        }

        .navbar {
            width: 100%;
            background: #222;
            padding: 18px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            font-size: 16px;
        }

        .container {
            width: 90%;
            max-width: 700px;
            margin: 60px auto;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .form-box h2 {
            margin-bottom: 20px;
        }

        .form-box input {
            width: 100%;
            padding: 14px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            outline: none;
        }

        .form-box input:focus {
            border-color: #222;
        }

        .form-box input[type="submit"] {
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s;
        }

        .form-box input[type="submit"]:hover {
            background: #444;
        }

        .hint {
            font-size: 13px;
            color: #777;
            margin-top: -10px;
            margin-bottom: 15px;
        }
    </style>

</head>

<body>

    <div class="navbar">
        <div class="logo">Shop Admin</div>
        <div class="nav-links">
            <a href="./shop.php">Shop</a>
            <a href="./add_product.php">Add Product</a>
            <a href="./cart.php">Cart</a>
        </div>
    </div>

    <div class="container">

        <div class="form-box" >
            <h2>Add New Product</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="text" name="product_name" placeholder="Product Name" required>
                <input type="text" name="production_description" placeholder="Product Description" required>
                <input type="number" name="product_price" placeholder="Price" min="0" required>
                <input type="number" name="product_stock" placeholder="Stock" min="0" required>
                <input type="text" name="product_category"
                    placeholder="Categories (toy food styling health - space separated)" required>
                <input type="file" name="fileToUpload">
                    <div class="hint">
                    Allowed: toy, food, styling, health
                </div>
                <input type="submit" name="submit" value="Create Product">
            </form>
        </div>
    </div>
</body>

</html>