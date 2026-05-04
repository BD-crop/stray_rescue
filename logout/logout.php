<?php
        if(!isset($_POST["submit"]) || $_SERVER['REQUEST_METHOD']!="POST"){
            http_response_code(400);

            $array;
            $array['msg'] ="invalid request";

            exit(json_encode($array ,JSON_PRETTY_PRINT));
        }   


        if (isset($_COOKIE[session_name()])) {
            session_start();
            session_set_cookie_params([
                'secure' => true, // Only for HTTPS connections
                'httponly' => true, // Cannot be accessed by JavaScript
                'samesite' => 'Strict', // Mitigate CSRF risks
            ]);
            $_SESSION = [];

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



        $user_data['msg'] ="logout successful";
        http_response_code(200);
        exit(json_encode($user_data,JSON_PRETTY_PRINT));   


    
?>