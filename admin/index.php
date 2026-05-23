<?php
    include __DIR__ ."./auth.php";




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

        <li><a href="./Employee/createEmployee.php">Create Employee</a></li>
        <li><a href="./rescue_point/createRescuePoint.php">Create Rescue Point</a></li>
        <li><a href="./Employee/getAllEmployee.php">Get All Employee</a></li>
        <li><a href="./Employee/managerView.php">Get All Manager</a></li>
        <li><a href="./Employee/ManagerView.php">Super Visor View</a></li>
        <li><a href="./rescue_point/seeRescueLocations.php">See Rescue Locations</a></li>
    </ul>
</body>
</html>