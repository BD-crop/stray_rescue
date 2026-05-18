<?php
    
    include_once __DIR__."/../PDO/PDO.php";

    if(isset($_GET['id'])){ 

        $obj =PDO_class::initializer()->see_rescue_post($_GET['id']);

        $res;

        $res['post_id'] = $obj['rescue_post_id'];
        $res['image_link']= $obj['rescue_post_image_link'];
        $res['post_text'] =$obj['rescue_post'];
        $res['species'] = $obj['animal_species_type'];
        $res['gender'] = $obj['animal_gender_type'];
        $res['age'] = $obj['animal_age'];
        $res['latitude'] = $obj['post_loc_latitude'];
        $res['longtitude']=$obj['post_loc_longtitude'];
        $res['post_time']=$obj['post_time_stamp'];
        $res['posted_by'] = $obj['user_id'];

        exit(json_encode($res , JSON_PRETTY_PRINT));
    }
    $arr= ['msg' => 'no id found'];
    exit(json_encode($arr , JSON_PRETTY_PRINT));
?>