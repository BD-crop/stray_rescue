<?php
    include __DIR__.'/../PDO/PDO.php';
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL); 

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