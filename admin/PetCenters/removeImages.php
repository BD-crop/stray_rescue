<?php

include_once __DIR__ . "/../auth_all_Employee.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['imagePath']) || empty($_POST['imagePath'])) {
        echo json_encode([
            "status" => "error",
            "message" => "imagePath missing"
        ]);
        exit();
    }

    $imagePath = $_POST['imagePath'];

    try {
        $obj = PDO_class::initializer()->removeRescueCenterImages($imagePath);

        echo json_encode([
            "status" => "success"
        ]);

    } catch (Exception $e) {

        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ]);
    }

    exit();
}