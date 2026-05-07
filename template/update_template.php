<?php
    
    function update_(){
        if (!isset($_COOKIE[session_name()])) {
            http_response_code(400);

            $array;
            $array['msg'] ="must loging before update";

            exit(json_encode($array ,JSON_PRETTY_PRINT));
            
        }
        session_start();

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