<?php
    include_once __DIR__ ."/../auth.php";
    
    if(empty($_POST['id']) || empty($_POST['name']) || empty($_POST['submit'])) {
        $msg = urlencode("not all fields are given");
        header('Location: /../index.php?msg=$msg');
        exit;
    }
        
    PDO_class::initializer()->rescue_point_remove_image($_POST['id'] , $_POST['name']);

?>