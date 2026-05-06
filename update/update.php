<?php
    include_once __DIR__ . "/../PDO/PDO.php";

    session_start();
    
    if (!isset($_SESSION['id'])) {
        $msg = ['msg' => 'No session id'];
        exit(json_encode($msg, JSON_PRETTY_PRINT));
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $arr = ['msg' => 'Incorrect method, only POST allowed'];
        exit(json_encode($arr, JSON_PRETTY_PRINT));
    }
    

    if($_SESSION['type']==="User"){
        $res = PDO_class::initializer()->update_bio_user($_SESSION['id']); 
    }else if($_SESSION['type'] === 'Employee'){
        $res= PDO_class::initializer()->update_bio_employee($_SESSION['id']);
    }else{
        $res =PDO_class::initializer() ->update_bio_volunteer($_SESSION['id']);
    }



?>