<?php
<<<<<<< HEAD
include_once __DIR__ . "/../template/login_template.php";


$userdata = login_template("Employee", $_POST, $_SERVER);

$obj = PDO_class::initializer();

session_start();
session_set_cookie_params([
    'secure'   => true,     // Only for HTTPS connections
    'httponly' => true,     // Cannot be accessed by JavaScript
    'samesite' => 'Strict', // Mitigate CSRF risks
]);
$_SESSION['type'] = "Employee";
$_SESSION['id']   = $obj->get_id('emp_id', $_POST['email'], 'Employee');

$userdata['id'] = $_SESSION['id'];
exit(json_encode($userdata, JSON_PRETTY_PRINT));
=======
    INCLUDE_ONCE __DIR__."/../template/login_template.php";


    login_template("Employee" , $_POST ,$_SERVER);



?>
>>>>>>> e81b3bc5cbe7f7d54c2b9a94dec315f42bfed30f
