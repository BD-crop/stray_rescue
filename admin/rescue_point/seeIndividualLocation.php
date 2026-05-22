<?php
include_once __DIR__ . "/../auth_all_Employee.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $msg = urlencode("No rescue point ID found");
    header("Location: seeIndividualLocation.php?msg=$msg");
    exit;
}

$id = $_GET['id'];
$res = PDO_class::initializer()->get_point_by_id($id);


if (!$res) {
    $msg = urlencode("Rescue point not found");
    header("Location: seeIndividualLocation.php?msg=$msg");
    exit;
}

$images = [];

if (!empty($res['images'])) {
    $images = explode(';;;', $res['images']);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Rescue Point Dashboard</title>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        .container {
            display: flex;
            gap: 20px;
            max-width: 1400px;
            margin: auto;
        }
        #left_orient {
            flex: 2;
            background: white;
            padding: 25px;
            border-radius: 14px;
            height: 95vh;
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .right_panel {
            flex: 1;
            background: white;
            padding: 20px;
            border-radius: 14px;
            height: 95vh;
            overflow-y: auto;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }
        .main_title {
            margin-top: 0;
            margin-bottom: 10px;
        }

        .section_title {
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 22px;
        }

        /* INFO GRID */

        .info_grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 20px;
        }

        .info_card {
            background: #f9fafb;
            padding: 15px;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        .info_card p {
            margin: 0;
            word-break: break-word;
        }



        .supervisor_card {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            max-width: 400px;
            border: 1px solid #ddd;
        }

        .supervisor_card img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .supervisor_info p {
            margin: 4px 0;
        }

        /* IMAGE GRID */

        .image_grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .image_grid img {
            width: 170px;
            height: 170px;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        /* UPLOAD BOX */

        .upload_box {
            margin-top: 15px;
            padding: 15px;
            background: #f9fafb;
            border-radius: 10px;
            border: 1px solid #ddd;
        }

        input[type="submit"] {
            border: none;
            background: #2563eb;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            background: #1d4ed8;
        }

        /* EMPLOYEE CARD */

        .employee_card {
            display: flex;
            gap: 12px;
            align-items: center;
            padding: 12px;
            background: #f9fafb;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-bottom: 12px;
        }

        .employee_avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #2563eb;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
        }

        .employee_info p {
            margin: 3px 0;
            font-size: 14px;
        }

        /* QUICK GALLERY */

        .quick_gallery img {
            width: 100%;
            border-radius: 10px;
            margin-bottom: 12px;
            border: 1px solid #ddd;
        }

        /* SCROLLBAR */

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- LEFT PANEL -->

        <div id="left_orient">

            <h1 class="main_title">
                🐾 Rescue Point Details
            </h1>

            <h3>
                Point Name:
                <?php echo htmlspecialchars($res['emp_name']) ?>
            </h3>

            <!-- INFO -->

            <div class="info_grid">

                <div class="info_card">
                    <p>
                        <b>ID</b><br>
                        <?php echo htmlspecialchars($res['rescue_point_id']) ?>
                    </p>
                </div>

                <div class="info_card">
                    <p>
                        <b>Supervisor ID</b><br>
                        <?php echo htmlspecialchars($res['supervisor_id']) ?>
                    </p>
                </div>

                <div class="info_card">
                    <p>
                        <b>Latitude</b><br>
                        <?php echo htmlspecialchars($res['rescue_point_location_latitude']) ?>
                    </p>
                </div>

                <div class="info_card">
                    <p>
                        <b>Longitude</b><br>
                        <?php echo htmlspecialchars($res['rescue_point_location_longtitude']) ?>
                    </p>
                </div>

            </div>

            <!-- SUPERVISOR -->

            <h2 class="section_title">
                👨‍💼 Supervisor
            </h2>

            <div class="supervisor_card">

                <img src="<?php echo $res['supervisor_image']; ?>" alt="">

                <div class="supervisor_info">

                    <p>
                        <b><?php echo $res['supervisor_name']; ?></b>
                    </p>

                    <p>
                        <?php echo $res['supervisor_email']; ?>
                    </p>

                </div>

            </div>

            <!-- IMAGES -->

            <h2 class="section_title">
                🖼 Shelter Images
            </h2>

            <div class="image_grid">

                <?php if (!empty($images)): ?>

                    <?php foreach ($images as $img): ?>
                        <form  action="./rescuePointImageDelete.php" method="POST" action="_blank">
                            <img src="<?php echo htmlspecialchars($img); ?>">
                            <input type="hidden" name="name" value="<?php echo htmlspecialchars($img); ?>">
                            <input type="hidden" name="id" value="<?php echo $id ; ?>">
                            <input type="submit" style="background-color:red" name="submit" value="DELETE">
                        </form>
                        
                    <?php endforeach; ?>

                <?php else: ?>

                    <p>No images available</p>

                <?php endif; ?>

            </div>

            <!-- UPLOAD -->

            <h2 class="section_title">
                ⬆ Upload Images
            </h2>

            <div class="upload_box">

                <!-- FORM UNCHANGED -->

                <form action="./rescuePointImageUpload.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="file" name="fileToUpload">
                    <input type="submit" name="submit" value="submit">
                </form>

            </div>

        </div>

        <!-- RIGHT PANEL -->

        <div class="right_panel">

            <h2>
                👨‍💼 Assigned Employees
            </h2>

            <?php

            if (!empty($res['EMP_INFO'])) {

                $emps = explode(';;;', $res['EMP_INFO']);

                for ($i = 0; $i < count($emps); $i += 3) {

                    $name = $emps[$i] ?? '';
                    $email = $emps[$i + 1] ?? '';
                    $uuid = $emps[$i + 2] ?? '';

                    $avatar = strtoupper(substr($name, 0, 1));

                    echo "
                    <div class='employee_card'>

                        <div class='employee_avatar'>
                            {$avatar}
                        </div>

                        <div class='employee_info'>

                            <p>
                                <b>" . htmlspecialchars($name) . "</b>
                            </p>

                            <p>
                                " . htmlspecialchars($email) . "
                            </p>

                            <p style='font-size:12px;color:#666;'>
                                " . htmlspecialchars($uuid) . "
                            </p>

                        </div>

                    </div>
                ";
                }

            } else {

                echo "<p>No employees assigned</p>";
            }

            ?>

            <hr>

            <h2>
                🖼 Quick Image View
            </h2>

            <div class="quick_gallery">

                <?php if (!empty($images)): ?>

                    <?php foreach ($images as $img): ?>

                        <img src="<?php echo htmlspecialchars($img) ?>">

                    <?php endforeach; ?>

                <?php else: ?>

                    <p>No images</p>

                <?php endif; ?>

            </div>

        </div>

    </div>

</body>

</html>