<?php
    include_once __DIR__ ."./auth.php";
    session_start();




?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin panel</title>
</head>
<body>
    <ul>

        <li><a href="./createEmployee.php">Create Employee</a></li>
        <li><a href="./createRescuePoint.php">Create Rescue Point</a></li>
        <li><a href="./getAllEmployee.php">Get All Employee</a></li>
        <li><a href="./mangerView.php">Get All Manager</a></li>
        <li><a href="./superVisorView.php">Super Visor View</a></li>
        <li><a href="./seeRescueLocations.php">See Rescue Locations</a></li>
    </ul>
</body>
</html>