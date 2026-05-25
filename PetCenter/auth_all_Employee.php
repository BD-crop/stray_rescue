<?php
    include __DIR__.'/../PDO/PDO.php';
    session_start();

    if(!isset($_SESSION['id'])){
        header("Location: ./../index.php");     
        exit();
    }

    $obj = PDO_class::initializer();
    
    $level = $obj->find_employee_level();
    if(!isset($level) || $level > 4  || $level  < 0){
        header("Location: ./../index.php");
        exit();
    }
?>