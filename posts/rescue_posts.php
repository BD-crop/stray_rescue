<?php
 


    include_once __DIR__ . "/../PDO/PDO.php";
    
    $obj = PDO_class::initializer();

    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

    $res = $obj->see_rescue_posts(0);

    echo json_encode($res, JSON_PRETTY_PRINT);
    exit;


?>