<?php

    include_once __DIR__."/../PDO/PDO.php";

    if(isset($_GET['id'])){ 

        $obj =PDO_class::initializer();
        $is_admin = $obj -> is_super_admin_or_upper($_GET['id']);

        if(!$is_admin){
            $arr= ['msg' => 'no an admin id'];
            exit(json_encode($arr , JSON_PRETTY_PRINT));
        }
        
        

        exit(json_encode($res , JSON_PRETTY_PRINT));
    }
    $arr= ['msg' => 'no id found'];
    exit(json_encode($arr , JSON_PRETTY_PRINT));



?>