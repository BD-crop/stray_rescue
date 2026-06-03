<?php
    include __DIR__.'/../PDO/PDO.php';

    session_start();
    
    
    if(!isset($_SESSION['id']) || empty($_SESSION['id']) || strlen($_SESSION['id'])===0){
        header("Location: ../index.php");     
        exit();
    }


    $obj = PDO_class::initializer();
    
    $level = $obj->find_employee_level();
    
    if( $level == -1){
        header("Location: ../index.php");     
        exit();
    }
    if(isset($level) && $level > 1 ){
        
        header("Location: ../index.php");
        exit();
    }
?>