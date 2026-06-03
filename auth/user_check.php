<?php

    include_once __DIR__."/../PDO/PDO.php";
    session_start();
    
    $profile = PDO_class::initializer()->get_user_profile($_SESSION['id']);
    $var = 0;

    if ($profile && isset($profile['user_id'])) {
        $var = 1 ;
    } 

?>