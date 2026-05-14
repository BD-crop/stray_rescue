<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    error_reporting(E_ALL);
    include_once __DIR__."/../PDO/PDO.php";
    include_once __DIR__."/../template/admin_check.php";

    $obj =PDO_class::initializer();

    if(!isset($_POST['name'])){
        $msg;
        $msg['msg'] ="no name given";

        exit(json_encode($msg , JSON_PRETTY_PRINT));
    }

    if(!isset($_POST['manager_id'])){
        $msg;
        $msg['msg'] ="no name given";

        exit(json_encode($msg , JSON_PRETTY_PRINT));

    }

    if(!(isset($_POST["lat"]) && isset($_POST['lang']))){
        $msg;
        $msg['msg'] = "no location is given";

        exit(json_encode($msg , JSON_PRETTY_PRINT));
    }

    if(!(check_if_employee()))
    {
        http_response_code(400);
        $msg;
        $msg['msg'] ="Not an employee";

        exit(json_encode($msg , JSON_PRETTY_PRINT));
    }  


    $level = $obj->find_employee_level();
    
    if($level > 1 ){
        http_response_code(400);    
        $msg1;
        $msg1['msg'] ="Not an senior-admin or upper";
        exit(json_encode($msg1 , JSON_PRETTY_PRINT));
    }

    
    if(!($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']))){
        http_response_code(400);
        $msg1;           
        $msg['msg'] = 'wrong format please correct it';
    }

    $obj -> create_rescue_point();








?>