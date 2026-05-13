<?php

include_once __DIR__ . "/../header.php";
include_once __DIR__ . "/../PDO/PDO.php";

function login_template( $POST, $SERVER)
{

    if (isset($_COOKIE[session_name()])) {
        session_start();

        $_SESSION = [];

        // Delete the cookie
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

    if (! isset($POST["submit"]) || $SERVER['REQUEST_METHOD'] != "POST") {
        http_response_code(400);

        $array;
        $array['msg'] = "invalid request";

        exit(json_encode($array, JSON_PRETTY_PRINT));
    }

    if (! isset($POST['email']) || ! isset($POST['password'])) {
        http_response_code(400);

        $array;
        $array['msg'] = "all field are required";

        exit(json_encode($array, JSON_PRETTY_PRINT));

    }

    $user_data;

    $user_data["email"]    = $POST['email'];
    $user_data['password'] = $POST['password'];
    $user_data['type']     = $POST['type'];
    $table_name            = $user_data['type'];

    if (! ($user_data['type'] === 'Users' || $user_data['type'] === 'Employee' || $user_data['type'] === 'volunteers')) {

        http_response_code(400);
        $array;
        $array['msg'] = "wrong type of users";
        $array['type'] =$table_name ;

        exit(json_encode($array, JSON_PRETTY_PRINT));

    }

    $obj = PDO_class::initializer();

    if (! ($obj->login_email_checker($user_data["email"], $table_name))) {

        http_response_code(400);
        $array;
        $array['msg'] = "invalid-user request";
        exit(json_encode($array, JSON_PRETTY_PRINT));
    }

    if (! ($obj->password_checker($user_data["email"], $user_data['password'], $table_name))) {
        http_response_code(400);

        $array;
        $array['msg'] = "wrong password for email";

        exit(json_encode($array, JSON_PRETTY_PRINT));
    }

    session_start();

    session_regenerate_id(true);

    http_response_code(200);
    return $user_data;

}
