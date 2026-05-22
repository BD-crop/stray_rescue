<?php
    include_once __DIR__."/../auth.php";
    
    if(!isset($_GET['name']) && !isset($_GET['rankBy']) && !isset($_GET['order'])){
        $msg;    
        $msg['msg'] = 'all fields are required';
        exit(json_encode($msg , JSON_PRETTY_PRINT));
    }


    $obj = PDO_class::initializer();
    $result= $obj->get_points_by_name($_GET['name'] , $_GET['rankBy'] ,$_GET['order']);

    exit(json_encode($result , JSON_PRETTY_PRINT));



?>