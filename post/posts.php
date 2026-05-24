<?php
//      ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL); 
include_once __DIR__ . "/../PDO/PDO.php";

$obj = PDO_class::initializer();

$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$res = $obj->see_rescue_posts($offset);


$res_json = json_encode($res);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>See Rescue Posts</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .post {
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            max-width: 500px;
        }

        .post img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-top: 10px;
        }

        .post div {
            margin: 5px 0;
        }

        .label {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <h1>See Rescue Posts</h1>

    <div id="rescuePosts"></div>

    <script>
        (function () {

            const container = document.getElementById("rescuePosts");

            const data = <?php echo $res_json; ?>;

            console.log(data);

            if (!data || data.count == 0) {
                container.innerText = "NO Posts to show";
                return;
            }

            container.innerHTML = "";

            
            data.posts.forEach(datas => {

                container.innerHTML += `
                    <div class="post">

                        <div>
                            <span class="label">Post ID:</span>
                            ${datas.rescue_point_id}
                        </div>

                        <div>
                            <span class="label">Text:</span>
                            ${datas.rescue_post}
                        </div>

                        <div>
                            <span class="label">Species:</span>
                            ${datas.animal_species_type}
                        </div>

                        <div>
                            <span class="label">Gender:</span>
                            ${datas.animal_gender_type}
                        </div>

                        <div>
                            <span class="label">Age:</span>
                            ${datas.animal_age}
                        </div>

                        <div>
                            <span class="label">Latitude:</span>
                            ${datas.post_loc_latitude}
                        </div>

                        <div>
                            <span class="label">Longitude:</span>
                            ${datas.post_loc_longtitude}
                        </div>

                        <div>
                            <span class="label">Post Time:</span>
                            ${datas.post_time_stamp}
                        </div>

                        <div>
                            <img src="${datas.rescue_post_image_link}" alt="Rescue Image">
                        </div>

                    </div>
                `;
            });

        })();
    </script>

</body>
</html>