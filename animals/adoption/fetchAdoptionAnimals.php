<?php
    include_once __DIR__ . "/../../PDO/PDO.php";
 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL); 


    $obj = PDO_class::initializer();

    $data = json_decode(file_get_contents("php://input"), true);

    $filters = $data['filters'] ?? [];

    $data1 = $obj -> getFilteredAdoptionListing($filters);
exit(json_encode($data1 ,JSON_PRETTY_PRINT));


?>