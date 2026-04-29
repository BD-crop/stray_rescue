<?php

    INCLUDE_ONCE __DIR__."/../header.php";
    INCLUDE_ONCE __DIR__."/../PDO/PDO.php";

    function signup_template($table_name ,$POST ,$SERVER ){

        if(!isset($POST["submit"]) || $SERVER['REQUEST_METHOD']!="POST"){
            http_response_code(400);

            $array;
            $array['msg'] ="invalid request";

            exit(json_encode($array ,JSON_PRETTY_PRINT));
        }   

        if(!isset($POST['email'] ) || !isset($POST['password'])){
            http_response_code(400);
            
            $array;
            $array['msg'] ="all field are required";

            exit(json_encode($array ,JSON_PRETTY_PRINT));

        }

        
        $user_data;
        $user_data["name"] =$POST['name'];
        $user_data["email"] =$POST['email'];
        $user_data['password']=$POST['password'];


        $obj = PDO_class::initializer();

        if(($obj -> email_checker($user_data["email"] , $table_name ))){
            http_response_code(400);
            
            $array;
            $array['msg'] ="email already exists";

            exit(json_encode($array ,JSON_PRETTY_PRINT));
        }

        if($table_name=="Users"){
            $obj-> user_insert($user_data['name'],$user_data["email"] ,$user_data['password']);
        }
        else if($table_name==="Employee"){
            $obj-> admin_insert($user_data['name'],$user_data["email"] ,$user_data['password']);
        }
        else{
            $obj -> volunteer_insert($user_data['name'] ,$user_data["email"] , $user_data["password"]);
        }





        http_response_code(200);

        exit(json_encode($user_data,JSON_PRETTY_PRINT));



    }





?>