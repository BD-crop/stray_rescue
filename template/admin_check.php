<?php
    session_start();

    if (!isset($_SESSION["id"])){
        http_response_code(400);
        $msg ;
        $msg['msg'] ="No session found"; 

        exit(json_encode($msg , JSON_PRETTY_PRINT));    
    }

    if($_SESSION['type'] !== 'Employee'){
        http_response_code(400);
        $msg['msg'] = 'Not an employee';
        exit(json_encode($msg , JSON_PRETTY_PRINT));
    }
    
    function check_if_employee(){
        $obj = PDO_class::initializer()->find_employee_level();

        if($obj<= 4)
        {
            return true;
        }
        return false;

    }



?>