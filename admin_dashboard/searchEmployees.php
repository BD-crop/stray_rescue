<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);

    error_reporting(E_ALL);


    include_once __DIR__."/../PDO/PDO.php";
    include_once __DIR__."/../template/admin_check.php";
    
    $obj =PDO_class::initializer();

    if(!(check_if_employee()))
    {
        http_response_code(400);
        $msg;
        $msg['msg'] ="Not an employee";

        exit(json_encode($msg , JSON_PRETTY_PRINT));
    }  

    $level = $obj->find_employee_level();
    
    if(!($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name']))){
        http_response_code(400);
        $msg1;           
        $msg['msg'] = 'wrong format please correct it';
    }



    $_SESSION['level']=$level;
    $msg = $obj -> get_emplyee_info();
    

    if($level >1){
        $msg1;
        $msg1['msg'] = 'Not an senior-admin or upper';
        $msg['level'] = $level;
        echo $level ." <-- this is the level";
        exit(json_encode($msg1 , JSON_PRETTY_PRINT));
    }

    $name = $_POST['name']; 
    $employees =$obj->get_all_employee(1 , $name);
    
    
    foreach($employees as $employee){

        $employee['emp_id'] = "12" ;
    }


    exit(json_encode($employees , JSON_PRETTY_PRINT));  

?>