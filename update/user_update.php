<?php


include_once __DIR__ . "/../PDO/PDO.php";

session_start();
session_set_cookie_params([
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

if (!isset($_SESSION['id'])) {
    $msg = ['msg' => 'No session id'];
    exit(json_encode($msg, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $arr = ['msg' => 'Incorrect method, only POST allowed'];
    exit(json_encode($arr, JSON_PRETTY_PRINT));
}

$obj = PDO_class::initializer();

try {
    $obj->update_bio_employee($_SESSION['id']);
} catch (Exception $e) {
    $error_msg = ['msg' => 'Error updating bio', 'error' => $e->getMessage()];
    exit(json_encode($error_msg, JSON_PRETTY_PRINT));
}
?>