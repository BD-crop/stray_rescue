<?php

include_once __DIR__ . "/../PDO/PDO.php";
include_once __DIR__."/../header.php";

session_start();

if (isset($_SESSION['id'])) {
    $arr;
    $arr['id']   = $_SESSION['id'];
    $arr['type'] = $_SESSION['type'];
    $res;
    if ($_SESSION['type'] === "Users") {
        $res = PDO_class::initializer()->get_user_profile($_SESSION['id']);
    } else if ($_SESSION['type'] === 'Employee') {
        $res = PDO_class::initializer()->get_admin_profile($_SESSION['id']);
    } else {
        $res = PDO_class::initializer()->get_volunteer_profile($_SESSION['id']);
    }

    if (! $res) {
        http_response_code(400);
        $arr;
        $arr['msg'] = "no user found";
        exit(json_encode($arr, JSON_PRETTY_PRINT));
    }
    http_response_code(200);
    $data;
    $data['msg']   = 'found';
    $data['id']    = $res['user_id'] ?? $res['emp_id'] ?? $res['volunteer_id'] ?? null;
    $data['type']  = $_SESSION['type'];
    $data['name']  = $res['user_name'] ?? $res['emp_name'] ?? $res['volunteer_name'] ?? null;
    $data['email'] = $res['email'];
    $data['image'] = $res['emp_profile_picture_link'] ?? $res['user_profile_picture_link'] ?? $res['volunteer_image_link'] ?? null;
    $data['bio']   = $res['user_bio'] ?? $res['emp_bio'] ?? res['volunteer_bio'] ?? null;

    exit(json_encode($data, JSON_PRETTY_PRINT));

} else {
    http_response_code(400);
    $arr;
    $arr['msg'] = "no session_id found";
    exit(json_encode($arr, JSON_PRETTY_PRINT));
}
