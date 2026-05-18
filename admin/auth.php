<?php
    include_once __DIR__.'/../PDO/PDO.php';
    session_start();

    if(!isset($_SESSION['id'])){
        header("Location: ./../index.php");     
        exit();
    }


    $obj = PDO_class::initializer();
    
    $level = $obj->find_employee_level();

    if($level && $level <= 1 ){
        header("Location: ./../index.php");
        exit();
    }

    


?>