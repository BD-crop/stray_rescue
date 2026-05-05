<?php



    INCLUDE_ONCE __DIR__."/../header.php";
    INCLUDE_ONCE __DIR__."/../PDO/PDO.php";

    function signup_template($table_name ,$POST ,$SERVER ){

        if (isset($_COOKIE[session_name()])) {
            session_start();
session_set_cookie_params([
    'secure' => true, // Only for HTTPS connections
    'httponly' => true, // Cannot be accessed by JavaScript
    'samesite' => 'Strict', // Mitigate CSRF risks
]);
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


        // session_start();
        // session_regenerate_id(true);


        http_response_code(200);

        exit(json_encode($user_data,JSON_PRETTY_PRINT));



    }





?>