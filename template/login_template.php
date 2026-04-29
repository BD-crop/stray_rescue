<?php


    INCLUDE_ONCE __DIR__."/../header.php";
    INCLUDE_ONCE __DIR__."/../PDO/PDO.php";

    function login_template($table_name ,$POST ,$SERVER ){

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

        $user_data["email"] =$POST['email'];
        $user_data['password']=$POST['password'];


        $obj = PDO_class::initializer();

        if(! ($obj -> email_checker($user_data["email"] , $table_name ))){
            http_response_code(400);
            
            $array;
            $array['msg'] ="invalid-user request";

            exit(json_encode($array ,JSON_PRETTY_PRINT));
        }

        if(!($obj-> password_checker($user_data["email"] ,$user_data['password'] , $table_name))){
            http_response_code(400);
            
            $array;
            $array['msg'] ="wrong password for email";

            exit(json_encode($array ,JSON_PRETTY_PRINT));
        }



        http_response_code(200);
        exit(json_encode($user_data,JSON_PRETTY_PRINT));



    }






?>