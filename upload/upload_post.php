<?php
session_start();

include_once __DIR__ . "/../PDO/PDO.php";
include_once __DIR__."/../header.php";


if (isset($_SESSION['id'])) {


    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $arr = ['msg' => 'Incorrect method, only POST allowed'];
        exit(json_encode($arr, JSON_PRETTY_PRINT));
    }

    if ($_SESSION['type'] === "Users") {
        $res = PDO_class::initializer()->upload_rescue_post();
        $arr = ['msg' => $res];
        exit(json_encode($arr, JSON_PRETTY_PRINT));
    } else{
        $arr = ['msg' => 'Only Users are allowed to post'];
        exit(json_encode($arr, JSON_PRETTY_PRINT));
    }
}else{
    $msg = ['msg' => 'No session id'];
    exit(json_encode($msg, JSON_PRETTY_PRINT));
}






?>