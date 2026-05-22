<?php

    session_start();

    include_once __DIR__ . "/../auth.php";
    include_once __DIR__ . "/../../template/admin_check.php";

    $obj = PDO_class::initializer();

    if (! (check_if_employee())) {
    $msg = urlencode("You need to be a Senior Employee or upper to access page");
    header("Location: http://localhost:80/dashboard/index.php?msg=$msg");
    exit();
    }

    $level = $obj->find_employee_level();

    if ($level > 1) {
    $msg = urlencode("You need to be a Senior Employee or upper to access page");
    header("Location: http://localhost:80/dashboard/index.php?msg=$msg");
    exit();

    }

    $msg = $obj->get_employee_info($_GET['id']);
    $msg = json_encode($msg, true);

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>

    <style>

        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
        }

        body{
            font-family: Arial, Helvetica, sans-serif;
            background:#f4f4f4;
            padding:40px;
        }

        .container{
            max-width:800px;
            margin:auto;
            background:white;
            padding:30px;
            border-radius:12px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        h1{
            margin-bottom:20px;
            text-align:center;
        }

        .profile{
            display:flex;
            gap:30px;
            align-items:center;
            margin-bottom:30px;
        }

        .profile img{
            width:180px;
            height:180px;
            object-fit:cover;
            border-radius:50%;
            border:4px solid #ddd;
        }

        .info{
            flex:1;
        }

        .info p{
            margin:10px 0;
            font-size:18px;
        }

        .loading{
            text-align:center;
            font-size:20px;
        }

        .error{
            color:red;
            text-align:center;
            font-size:20px;
        }

    </style>
</head>
<body>

    <div class="container">

        <h1>Employee Details</h1>

        <div id="content" class="loading">
            Loading...
        </div>

    </div>

<script>

    const params = new URLSearchParams(window.location.search);

    const id = params.get("id");

    const content = document.getElementById("content");

    if(!id){
        content.innerHTML = `<div class="error">No employee id provided</div>`;
    }
    else{
        let data = <?php echo $msg ?>;

        if(!data || data.length === 0 || data.success === false){

            content.innerHTML = `
            <div class="error">
                    Employee not found
            </div>
            `;


        }

            const emp = data;

            content.innerHTML = `

                <div class="profile">

                    <img src="${emp.emp_profile_picture_link}" alt="profile">

                    <div class="info">

                        <p><strong>ID:</strong> ${emp.emp_id}</p>

                        <p><strong>Name:</strong> ${emp.emp_name}</p>

                        <p><strong>Email:</strong> ${emp.email}</p>

                        <p><strong>Rank:</strong> ${emp.emp_rank}</p>

                        <p><strong>Salary:</strong> ${emp.salary}</p>

                        <p><strong>Supervisor ID:</strong> ${emp.immediate_supervisor_id}</p>

                    </div>

                </div>

            `;

        }


</script>

</body>
</html>