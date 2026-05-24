<?php
    include_once __DIR__.'/../../PDO/PDO.php';
    session_start();

    if(!isset($_SESSION['id']) || empty($_SESSION['id'])){
        header("Location: ./../shop.php");     
        exit();
    }


    $obj = PDO_class::initializer();
    
    $level = $obj->find_employee_level();
    
    if( $level == -1){
        header("Location: ./../shop.php");     
        exit();
    }
    if(isset($level) && $level > 1 ){
        
        header("Location: ./../shop.php");
        exit();
    }


    


?>