<?php    
    include_once __DIR__ ."/../auth.php";
    include_once __DIR__."/../../template/admin_check.php";

    $obj =PDO_class::initializer();

    $level = $obj->find_employee_level();
    
    $name = $_POST['name'];
    $rank= $_POST['rank'];
    $employees =$obj->get_all_employeeEX($rank , $name );
    
    exit(json_encode($employees , JSON_PRETTY_PRINT));  
?>