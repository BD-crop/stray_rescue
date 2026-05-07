<?php

    include_once __DIR__ . "/../template/login_template.php";

    

    $userdata = login_template( $_POST, $_SERVER);

    $obj = PDO_class::initializer();
    session_start();

    $table_name = $_POST['type'];
    $_type;

    if ($_POST['type'] === "Users") {
        $_SESSION['type'] = "Users";
        $_type= 'user_id';

    } else if ($table_name === "Employee") {
        $_SESSION['type'] = "Employee";
        $_type= 'emp_id';

    } else if($table_name === 'volunteers' ){
        $_SESSION['type'] = "volunteers";
        $_type= 'volunteer_id';
    }else{
        $arr;
        $arr['msg']="unknown type";
        
        http_response_code(400);
        exit(json_encode($arr,JSON_PRETTY_PRINT));
    }
    
    $_SESSION['id'] = $obj->get_id($_type, $_POST['email'], $_POST['type']);
    $userdata['id'] = $_SESSION['id'];


    exit(json_encode($userdata, JSON_PRETTY_PRINT));

?>