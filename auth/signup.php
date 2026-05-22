<?php
    session_start();

    INCLUDE_ONCE __DIR__."/../PDO/PDO.php";

    function signup_template(){

        if (isset($_COOKIE[session_name()])) {

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




        if(!isset($_POST['email'] ) || !isset($_POST['password']) || !isset($_POST['table'])){
            $msg=urlencode("All fields are required");

            header("Location: signup.php?msg=$msg");
            exit();
        }

        
        $user_data;
        $user_data["name"] =$_POST['name'];
        $user_data["email"] =$_POST['email'];
        $user_data['password']=$_POST['password'];
        $user_data['type'] =$_POST['table'];
        $table_name = $user_data['type'];

        if(!($user_data['type']==='Users' || $user_data['type'] ==='Employee' || $user_data['type'] === 'volunteers')){

            $msg=urlencode("wrong type of user");
            header("Location: signup.php?msg=$msg");
            exit();

        }

        $obj = PDO_class::initializer();

        if(($obj -> email_checker($user_data["email"] , $table_name ))){
            $msg=urlencode("email already exists");
            header("Location: signup.php?msg=$msg");
            exit();
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


        session_start();
        session_regenerate_id(true);


        $email = $user_data["email"];
        $msg = urlencode("mail sent to $email");
        header("Location: signup.php?msg=$msg");
        exit();


    }

    if(isset($_POST['submit'])){
        signup_template();
        
    }


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Poppins', sans-serif;
        }

        body{
            background:#f4f7fb;
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            padding:20px;
        }

        .signup-card{
            width:100%;
            max-width:450px;
            background:white;
            padding:40px;
            border-radius:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.08);
        }

        .logo{
            font-size:48px;
            text-align:center;
            margin-bottom:10px;
        }

        .title{
            text-align:center;
            font-size:30px;
            font-weight:700;
            color:#222;
        }

        .subtitle{
            text-align:center;
            color:#777;
            margin-bottom:30px;
            font-size:14px;
        }

        .form-label{
            font-weight:500;
            color:#333;
            margin-bottom:8px;
        }

        .form-control,
        .form-select{
            border-radius:12px;
            padding:12px;
            border:1px solid #dcdcdc;
        }

        .form-control:focus,
        .form-select:focus{
            box-shadow:none;
            border-color:#4f8cff;
        }

        .signup-btn{
            width:100%;
            border:none;
            background:#4f8cff;
            color:white;
            padding:14px;
            border-radius:12px;
            font-weight:600;
            transition:0.3s;
        }

        .signup-btn:hover{
            background:#3c76e0;
        }

        .bottom-link{
            text-align:center;
            margin-top:20px;
            color:#666;
            font-size:14px;
        }

        .bottom-link a{
            text-decoration:none;
            font-weight:600;
            color:#4f8cff;
        }

        .message-box{
            padding:12px;
            border-radius:10px;
            margin-bottom:20px;
            font-size:14px;
            font-weight:500;
        }

        .success{
            background:#d1f7df;
            color:#0f7a38;
        }

        .error{
            background:#ffe0e0;
            color:#b42323;
        }

    </style>

</head>
<body>

<div class="signup-card">

    <div class="logo">
        🐾
    </div>

    <div class="title">
        Create Account
    </div>

    <div class="subtitle">
        Join the Stray Rescue Platform
    </div>

    <?php
        if(isset($_GET['msg'])){

            $msg = htmlspecialchars($_GET['msg']);

            $class = (stripos($msg, 'successful') !== false)
                ? 'success'
                : 'error';

            echo "
                <div class='message-box $class'>
                    $msg
                </div>
            ";
        }
    ?>

    <form action="" method="POST">

        <div class="mb-4">

            <label class="form-label">
                Full Name
            </label>

            <input
                type="text"
                class="form-control"
                name="name"
                placeholder="Enter your full name"
            >

        </div>

        <div class="mb-4">

            <label class="form-label">
                Email Address
            </label>

            <input
                type="email"
                class="form-control"
                name="email"
                placeholder="Enter your email"
            >

        </div>

        <div class="mb-4">

            <label class="form-label">
                Password
            </label>

            <input
                type="password"
                class="form-control"
                name="password"
                placeholder="Enter your password"
            >

        </div>

        <div class="mb-4">

            <label class="form-label">
                Account Type
            </label>

            <select
                class="form-select"
                name="table"
            >

                <option value="Users">
                    User
                </option>

                <option value="Employee">
                    Employee
                </option>

                <option value="volunteers">
                    Volunteer
                </option>

            </select>

        </div>

        <input
            type="submit"
            name="submit"
            value="Create Account"
            class="signup-btn"
        >

    </form>

    <div class="bottom-link">

        Already have an account?

        <a href="login.php">
            Login
        </a>

    </div>

</div>

</body>
</html>