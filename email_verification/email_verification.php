<?php
    include_once __DIR__ . "/../PDO/PDO.php";  

    $obj = PDO_class::initializer();


    if (isset($_GET['email_verification_id'])) {
        $email_verification_id = urldecode($_GET['email_verification_id']);


        $obj->email_verification($email_verification_id);
    } else {
        echo "verification id not provided.";
    }
?>