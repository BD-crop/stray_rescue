<?php
    
    function update_(){
        if (!isset($_COOKIE[session_name()])) {
            http_response_code(400);

            $array;
            $array['msg'] ="must loging before update";

            exit(json_encode($array ,JSON_PRETTY_PRINT));
            
        }
        session_start();
    session_set_cookie_params([
        'secure' => true, // Only for HTTPS connections
        'httponly' => true, // Cannot be accessed by JavaScript
        'samesite' => 'Strict', // Mitigate CSRF risks
    ]);
        if($_SESSION['type']!== $type){
        
            http_response_code(400);

            $array;
            $array['msg'] ="wrong type endpoint";

            exit(json_encode($array ,JSON_PRETTY_PRINT));
            
        }


        if($_SERVER['REQUEST_METHOD'] === 'POST'){
        
            http_response_code(400);

            $array;
            $array['msg'] ="invalid request";

            exit(json_encode($array ,JSON_PRETTY_PRINT));
        }

    }



?>