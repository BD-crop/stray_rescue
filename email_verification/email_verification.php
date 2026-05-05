<?php
include_once __DIR__ . "/../PDO/PDO.php";  

$obj = PDO_class::initializer();


if (isset($_GET['email']) && isset($_GET['table_name'])) {
    $email = urldecode($_GET['email']);
            echo "name is ".$_GET['email'];

    $table_name = $_GET['table_name'];

    $obj->email_verification($email, $table_name);
} else {
    echo "Error: Email or Table Name not provided.";
}
?>