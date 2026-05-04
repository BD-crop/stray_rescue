<?php

    INCLUDE_ONCE __DIR__."/../template/login_template.php";


    $userdata = login_template("volunteers" , $_POST, $_SERVER);

     $obj = PDO_class::initializer();
     session_start();
session_set_cookie_params([
    'secure' => true, // Only for HTTPS connections
    'httponly' => true, // Cannot be accessed by JavaScript
    'samesite' => 'Strict', // Mitigate CSRF risks
]);
    $_SESSION['type'] = "volunteers";
    $_SESSION['id']=$obj -> get_id('volunteer_id' , $_POST['email']  , 'volunteers');

    $userdata['id']=$_SESSION['id'];
    exit (json_encode($userdata, JSON_PRETTY_PRINT));

?>