<?php
    include_once __DIR__ . "/../auth.php";

    if (!isset($_POST['submit']) || empty($_POST['submit'])) {
        $msg = urlencode("No rescue point ID found");
        header("Location: seeIndividualLocation.php?msg=$msg");
        exit;
    }   

    $obj = PDO_class::initializer();

    $obj->rescue_point_image_upload($_POST['id']);
?>