<?php
    include_once __DIR__ ."/../auth.php";
    include_once __DIR__."/../../template/admin_check.php";

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL); 

    $obj =PDO_class::initializer();
    
    $name = $_GET['name'];
    $rank= $_GET['rank'];
    $employees =$obj->getEmployee_manager($name , $rank);
    
    exit(json_encode($employees , JSON_PRETTY_PRINT));  
?>