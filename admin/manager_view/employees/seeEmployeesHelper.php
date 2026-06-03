<?php
    
include_once __DIR__ . "/../../auth_all_Employee.php";

$obj = PDO_class::initializer();

$level = $obj->find_employee_level();

$name = $_GET['name'] ?? "";
$point_id = $_GET['point'] ?? "";


$employees = $obj->get_all_employee_by_point($point_id , $name);

exit(json_encode(  $employees, JSON_PRETTY_PRINT));

?>