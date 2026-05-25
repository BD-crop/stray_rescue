<?php

    include_once __DIR__ . "/../PDO/PDO.php";

    $obj = PDO_class::initializer();
    $page= $_GET['id'];
    $result = $obj->getProductsPagination($page);

    $result = json_encode($result);
    exit($result);
?>