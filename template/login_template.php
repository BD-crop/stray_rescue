<?php





    INCLUDE_ONCE __DIR__."/../header.php";
    INCLUDE_ONCE __DIR__."/../PDO/PDO.php";

    function login_template($table_name ,$POST ,$SERVER ){

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

        session_start();
        session_regenerate_id(true);

        http_response_code(200);
        exit(json_encode($user_data,JSON_PRETTY_PRINT));



    }






?>