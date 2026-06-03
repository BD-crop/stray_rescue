<?php
    include_once __DIR__ . "/../PDO/PDO.php";

    $obj = PDO_class::initializer();
    
    $offset = (int)$_GET['offset'];
    $limit = (int)$_GET['limit'];

    $res = $obj->see_rescue_posts($offset, $limit);
    
    exit(json_encode($res ,JSON_PRETTY_PRINT));
?>