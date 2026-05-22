<?php
    include_once __DIR__ ."/../auth.php";
    include_once __DIR__."/../../template/admin_check.php";


    $obj =PDO_class::initializer();


    $level = $obj->find_employee_level();
    
    $name = $_GET['name'];
    $rank= $_GET['rank'];
    $employees =$obj->get_all_employee($rank , $name ,$_GET['rank_by'] ?? null);
    
    
    


    exit(json_encode($employees , JSON_PRETTY_PRINT));  

?>