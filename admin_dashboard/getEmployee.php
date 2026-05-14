<?php



    include_once __DIR__."/../PDO/PDO.php";
    include_once __DIR__."/../template/admin_check.php";
    
    $obj =PDO_class::initializer();

    if(!(check_if_employee())){
        http_response_code(400);
        $msg;
        $msg['msg'] ="Not an employee";

        exit(json_encode($msg , JSON_PRETTY_PRINT));
    }  

    $level = $obj->find_employee_level();
 



    $_SESSION['level']=$level;
    $msg = $obj -> get_emplyee_info();
    



    if($level == 4){
        $msg["title"] = "Unassigned Employee";
    }else if($level == 3){
        $msg["title"] = "Assigned Employee";
    }else if($level == 2){
        $msg["title"] = "Center Manager";
    }else if($level == 1){
        $msg["title"] = "Senior Admin";
    }else{
        $msg['title'] = "Super Admin";
    }


    exit(json_encode($msg , JSON_PRETTY_PRINT));    


?>