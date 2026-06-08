<?php

session_start();

include_once __DIR__ . "/../PDO/PDO.php";

if (!isset($_SESSION['id'])) {
    exit("Please login first.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Invalid request.");
}

if (
    empty($_POST['entry_id']) ||
    empty($_POST['challenge_id'])
) {
    exit("Missing data.");
}

$obj = PDO_class::initializer();
if($obj->type_of_user() !== 'user') {
    exit("Only regular users can vote.");

}

$obj->unvote_challenge_entry();
header(
    "Location: ./entry_details.php?id=" .
    urlencode($_POST['entry_id'])
);
exit();
