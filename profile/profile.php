<?php
//  ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL);

include_once __DIR__ . "/../PDO/PDO.php";


session_start();

if (isset($_SESSION['id'])) {

    if(isset($_POST['submit'])){
        echo $_POST['id'];
        PDO_class::initializer()->update_user();
    }

    $arr;
    $arr['id']   = $_SESSION['id'];
    $arr['type'] = $_SESSION['type'];
    $res;

    if ($_SESSION['type'] === "Users") {
        $res = PDO_class::initializer()->get_user_profile($_SESSION['id']);
    } else if ($_SESSION['type'] === 'Employee') {
        $res = PDO_class::initializer()->get_admin_profile($_SESSION['id']);
    } else {
        $res = PDO_class::initializer()->get_volunteer_profile($_SESSION['id']);
    }

    if (! $res) {
        $msg = urlencode("No Data found");
        header("Location: ../index.php?msg=$msg");
        exit();
    }

    $data;
    $data['msg']   = 'found';
    $data['id']    = $_SESSION['id'];
    $data['type']  = $_SESSION['type'];
    $data['name']  = $res['user_name'] ?? $res['emp_name'] ?? $res['volunteer_name'] ?? null;
    $data['email'] = $res['email'];
    $data['image'] = $res['emp_profile_picture_link'] ?? $res['user_profile_picture_link'] ?? $res['volunteer_image_link'] ?? null;
    $data['bio']   = $res['user_bio'] ?? $res['emp_bio'] ?? res['volunteer_bio'] ?? null;


    

} else {
    $msg = urlencode("No session id found");
    header("Location: ../index.php?msg=$msg");
    exit();
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;

        }

        .body_main{
            display:flex;
            justify-content:space-between;
        }

        .card {
            border: 1px solid #ccc;
            padding: 15px;
            width: 300px;
            border-radius: 10px;
        }

        img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>
<body>

<h2>My Profile</h2>

    <div class="body_main">
        <div >
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="id_" name ="id" >  
                <label for="add_bio"> Add bio</label>
                <input type="text" name="add_bio" id="add_bio">
                <br>
                <label for="fileToUpload"> Upload a file to Upload</label>
                <input type="file" name="fileToUpload" id="fileToUpload">
                <br>
                <input type="submit" name="submit">
            </form>

        </div>


        <div id="output"></div>



    </div>

<script>
( function() {
    const output = document.getElementById("output");
    output.innerHTML = "Loading...";


        

         // safer than res.json() for debugging
        

        const data = <?php echo json_encode($data); ?>;

        console.log(data);


        output.innerHTML = `
            <div class="card">
                <p><b>ID:</b> ${data.id}</p>
                <p><b>Name:</b> ${data.name}</p>
                <p><b>Email:</b> ${data.email}</p>
                <p><b>Type:</b> ${data.type}</p>
                <p><b>Bio:</b> ${data.bio || "N/A"}</p>
                <img src="${data.image}" alt="profile image">
            </div>
        `;
        id_.value=data.id;
    
})();

console.log(<?php echo json_encode($res); ?>);
</script>

</body>
</html>