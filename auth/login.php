<?php
session_start();
include_once __DIR__ . "/../PDO/PDO.php";

function login_template()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    if ($_SERVER['REQUEST_METHOD'] !== "POST" || !isset($_POST["submit"])) {
        header("Location: login.php");
        exit();
    }

    if (
        !isset($_POST['email']) ||
        !isset($_POST['password']) ||
        !isset($_POST['type'])
    ) {
        $msg = urlencode("All fields are required");
        header("Location: login.php?msg=$msg");
        exit();
    }

    $user_data = [
        "email" => $_POST['email'],
        "password" => $_POST['password'],
        "type" => $_POST['type']
    ];

    $allowed_types = [
        "Users" => "user_id",
        "Employee" => "emp_id",
        "volunteers" => "volunteer_id"
    ];

    if (!isset($allowed_types[$user_data['type']])) {
        $msg = urlencode("Unknown user type");
        header("Location: login.php?msg=$msg");
        exit();
    }

    $table_name = $user_data['type'];

    $obj = PDO_class::initializer();

    if (!$obj->login_email_checker($user_data["email"], $table_name)) {
        $msg = urlencode("Email not verified");
        header("Location: login.php?msg=$msg");
        exit();
    }

    if (!$obj->password_checker(
        $user_data["email"],
        $user_data['password'],
        $table_name
    )) {
        $msg = urlencode("Wrong password");
        header("Location: login.php?msg=$msg");
        exit();
    }

    session_start();
    session_regenerate_id(true);

    return $user_data;
}

if (isset($_POST['submit'])) {

    $userdata = login_template();

    $obj = PDO_class::initializer();

    session_start();

    $type_map = [
        "Users" => "user_id",
        "Employee" => "emp_id",
        "volunteers" => "volunteer_id"
    ];

    $table = $_POST['type'];
    $_type = $type_map[$table];

    $_SESSION['type'] = $table;

    $_SESSION['id'] = $obj->get_id(
        $_type,
        $_POST['email'],
        $_POST['type']
    );


    $msg = urlencode("Login successful");
    header("Location: login.php?msg=$msg");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>

<form id="login" action="" method="POST">

    <select name="type" id="type">
        <option value="Users">User</option>
        <option value="Employee">Employee</option>
        <option value="volunteers">Volunteer</option>
    </select>

    <input type="text" name="email" id="email" placeholder="Email">

    <input type="password" name="password" id="password" placeholder="Password">

    <input type="submit" name="submit" value="Login">

</form>

<div id="result">

    <?php
        if (isset($_GET['msg'])) {
            $msg = htmlspecialchars($_GET['msg']);
            $color = (stripos($msg, 'success') !== false) ? 'green' : 'red';
            echo "<p style='color:$color;font-weight:bold;'>$msg</p>";
        }
    ?>

</div>

</body>
</html>