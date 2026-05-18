<?php
    session_start();




?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stray Rescue</title>
</head>
<body>
    
    <ul>
        <li ><a href="./Map.php">Map</a></li>
        <li><a href="./shop/shop.php">Shop</a></li>

        <?php
            if(!isset($_SESSION["id"])){
               ?>
                <li><a href="./auth/login.php" >login</a></li>
                <li><a href="./auth/signup.php">signup</a></li>

               <?php
            }else{
                ?>
                <li><a href="./admin">admin</a></li>
                <li><a href="./post/upload_post.php">upload post</a></li>
                <li><a href="./post/posts.php">see post</a></li>
                <li><a href="./profile/profile.php">profile</a></li>
                <li><a href="./auth/logout.php">logout</a></li>
                <?php
            }
        ?>
    </ul>

</body>
</html>