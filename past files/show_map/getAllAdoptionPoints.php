<?php
    include_once __DIR__."/PDO/PDO.php";


    if(!isset($_COOKIE["id"])) {
        $msg;
        $msg['msg'] ="login before seeing the map";
        exit(json_encode($msg , JSON_PRETTY_PRINT));
    }


    


?>