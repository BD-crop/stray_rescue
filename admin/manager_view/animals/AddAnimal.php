<?php
    include __DIR__ ."/../auth_all_Employee.php";
    $employee_level = $level;


    if($employee_level !== 3){
        header("Location: ../index.php");
        exit();
    }
?>

