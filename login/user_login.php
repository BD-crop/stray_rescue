<?php

    include_once __DIR__ . "/../template/login_template.php";

    $headers = "Users";

    $userdata = login_template("Users", $_POST, $_SERVER);

    $obj = PDO_class::initializer();
    session_start();
    session_set_cookie_params([
    'secure'   => true,     // Only for HTTPS connections
    'httponly' => true,     // Cannot be accessed by JavaScript
    'samesite' => 'Strict', // Mitigate CSRF risks
    ]);
    $_SESSION['type'] = "Users";
    $_SESSION['id']   = $obj->get_id('user_id', $_POST['email'], 'Users');
    $userdata['id']   = $_SESSION['id'];
exit(json_encode($userdata, JSON_PRETTY_PRINT));
