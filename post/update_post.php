<?php
include_once __DIR__."/../PDO/PDO.php";

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$obj = PDO_class::initializer();

$role = $obj ->type_of_user();



if (!isset($_SESSION['id']) ||
    empty($_POST['animal_id']) ||
    empty($_POST['level_text']) ||
    empty($_POST['created_by']) ||
    empty($_POST['level_description'])||
    empty($_POST['sos_level']) ||
    empty($_POST['created_by_type']) ||
    empty($_FILES['fileToUpload']) ||
    $role == 'xnonex') {
    {
        $msg = urlencode("Invalid form");
        header("Location: ../index.php?msg=$msg");
        exit();
    }

}

$obj->update_history($_SESSION['id'] , $role);

        $msg = urlencode("Success");
        header("Location: ../index.php?msg=$msg");
        exit(); 