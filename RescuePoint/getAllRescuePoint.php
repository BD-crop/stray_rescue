<?php

    include_once __DIR__ ."/../header.php";
    include_once __DIR__."/../PDO/PDO.php";
    session_start();

    if(!isset($_SESSION["id"])){
        $msg;
        $msg['msg'] ="login before accessing";
        exit(json_encode($msg , JSON_PRETTY_PRINT));
    }

    

    
    $obj = PDO_class::initializer();

    exit(json_encode( $obj -> get_all_points((int) $_GET['offset'] , (int) $_GET['limit']), JSON_PRETTY_PRINT));

    



?>