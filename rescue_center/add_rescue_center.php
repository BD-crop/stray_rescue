<?php
    session_start();

    include_once __DIR__ . "/../PDO/PDO.php";
    include_once __DIR__."/../header.php";


    if (isset($_SESSION['id']) && $_SESSION['type'] ==='Employee') {

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $arr = ['msg' => 'Incorrect method, only POST allowed'];
            exit(json_encode($arr, JSON_PRETTY_PRINT));
        }


        
    }else if($_SESSION['type']!=='Employee'){
        $msg = ['msg' => 'not an employee'];
        exit(json_encode($msg, JSON_PRETTY_PRINT));
    }else{
        $msg = ['msg' => 'No session id'];
        exit(json_encode($msg, JSON_PRETTY_PRINT));
    }




?>