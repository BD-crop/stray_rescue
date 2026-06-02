<?php
    
include_once __DIR__ . "/../../auth_all_Employee.php";

$obj = PDO_class::initializer();

$level = $obj->find_employee_level();

$name = $_GET['name'] ?? "";
$rank_by = $_GET['rank_by'] ?? "added_at";
$order = $_GET['order'] ?? "asc";

$animals = $obj->getAllShelteredAnimals($name, $rank_by, $order);

exit(json_encode($animals, JSON_PRETTY_PRINT));

?>