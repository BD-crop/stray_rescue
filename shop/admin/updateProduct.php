<?php
include_once __DIR__ ."/auth.php";


if(isset($_POST['submit'])){
    if(empty($_POST['product_id']) || empty($_POST['description']) || empty($_POST['price'])
        || empty($_POST['stock']) || empty($_POST['past_price'])){
      $msg = urlencode("All fields must be present");
      header("Location: seeProductAdmin.php?msg=$msg");
      exit();
    }
    $images= null;

    if(!empty($_POST["fileToUpload"])){
        $images = $obj->image_upload();

    }

    $images= $images === "" ? null : $images;
    echo $_POST['product_id'];
    $obj -> updateProduct($_POST['product_id'] ,$_POST['description'] , $_POST['price'] , $_POST['stock']
                        ,$_POST['past_price'] ,$images );
    
    $id =urlencode($_POST['product_id']);
    header("Location: seeProductAdmin.php?id=$id");
    exit();

    
}
    


?>