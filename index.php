<?php


    session_start();
    session_set_cookie_params([
    'secure' => true,
    'httponly' => true, 
    'samesite' => 'Strict', 
    ]);
    echo $_SESSION['id'] . " " .$_SESSION['type'];


?>