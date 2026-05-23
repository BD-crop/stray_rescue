<?php
session_start();


include_once __DIR__ . "/../auth.php";
include_once __DIR__ . "/../../template/admin_check.php";

$obj = PDO_class::initializer();

if (!check_if_employee()) {
    $msg = urlencode("You need to be a Senior Employee or upper to access page");
    header("Location:http://localhost:80/dashboard/index.php?msg=$msg");
    exit();
}

$level = $obj->find_employee_level();

if ($level > 1) {
    $msg = urlencode("You need to be a Senior Employee or upper to access page");
    header("Location:http://localhost:80/dashboard/index.php?msg=$msg");
    exit();
}

$emp = $obj->get_employee_info($_GET['id']);

if (!$emp) {
    die("Employee not found");
}

$targetRank = (int) $emp['emp_rank'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $level < $targetRank) {

    $empId = $_POST['emp_id'];
    $reason = $_POST['reason'] ;

    try {

        if (isset($_POST['salary']) && $_POST['salary'] !== '' && $_POST['salary'] != $emp['salary']) {
            $obj->change_employee_salary(
                $empId,
                $_POST['salary'],
                $_SESSION['id'],
                $reason ?? "Salary Change"
            );
        }

        if (isset($_POST['point']) && $_POST['point'] !== '' ) {

            $name = $_POST['point'];
            $point_id = $this->get_rescue_point_id_by_name($name);
            

            $obj->assign_at_a_point(
                $empId,
                7,
                $_SESSION['id'],
                $point_id,
                $reason ?? "Assigned at a point"
            );
        }
        $id = $obj->getEmployee_id($_POST['manager']);

        if (isset($_POST['manager']) && $_POST['manager'] !== '') {
            $id = $obj->getEmployee_id($_POST['manager']);
            if ($id !== $emp['immediate_supervisor_id']) {

                $obj->assign_employee_supervisor($empId, $id, $_SESSION['id'],$reason ?? 'Supervisor Updated'  );
            }
        
        }

        header("Location:" . $_SERVER['PHP_SELF'] . "?id=" . $empId);
        exit();

    } catch (Exception $e) {

        if ($obj->pdo->inTransaction()) {
            $obj->pdo->rollBack();
        }

        die("ERROR:" . $e->getMessage());
    }
}

$msg = json_encode($emp);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Employee Admin Panel</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box
        }

        body {
            font-family: Arial;
            background: #f4f4f4;
            padding: 40px
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1)
        }

        h1 {
            text-align: center;
            margin-bottom: 20px
        }

        .profile {
            display: flex;
            gap: 30px;
            align-items: center;
            margin-bottom: 30px
        }

        .profile img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #ddd
        }

        .info p {
            margin: 8px 0;
            font-size: 16px
        }

        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px
        }

        .card {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1)
        }

        .card h3 {
            margin-bottom: 10px
        }

        .card input,
        .card select,
        .card textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px
        }

        button {
            width: 100%;
            padding: 12px;
            background: green;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Employee Admin Panel</h1>
        <div id="content">Loading...</div>
    </div>

    <script>
        const emp = <?php echo $msg; ?>;

        const content = document.getElementById("content");

        content.innerHTML = `
            <div class="profile">
                <img src="${emp.emp_profile_picture_link}">
                <div class="info">
                <p><b>ID:</b>${emp.emp_id}</p>
                    <p><b>Name:</b>${emp.emp_name}</p>
                    <p><b>Email:</b>${emp.email}</p>
                    <p><b>Rank:</b>${emp.emp_rank}</p>
                    <p><b>Salary:</b>${emp.salary}</p>
                    <p><b>Supervisor:</b>${emp.supervisor_id || ""}</p>
                </div>
            </div>

            <form method="POST">
            <input type="hidden" name="emp_id" value="${emp.emp_id}">

            <div class="actions">

            <div class="card">
                <h3>Salary</h3>
                <input type="number" name="salary" min="0" value="${emp.salary}">
            </div>



            <div class="card">
                <h3>Manager</h3>
                <input type="text" name="manager" placeholder="Manager Email">
            </div>

             <div class="card">
                <h3>Assign Point</h3>
                <input type="text" name="point" placeholder="Write a point name">
            </div>           

            <div class="card">
                <h3>Reason</h3>
                <textarea name="reason"></textarea>
            </div>

            </div>

            <button type="submit">Submit</button>
            </form>
            `;



        console.log(<?= $msg?>);
    </script>

</body>

</html>