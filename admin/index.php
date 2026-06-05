<?php
include __DIR__ . "/auth_all_Employee.php";
include_once __DIR__ .'/../template/card_template.php';
$employee_level = $level;


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                screens: {
                    sm: "680px",
                    md: "768px",
                    lg: "1024px"
                }
            }
        }
    </script>
</head>

<body
    class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white transition-all duration-500">

    <div
        class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md bg-dark/30 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

        <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
            href="../index.php">
            Home
        </a>

        <button id="themeToggle"
            class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg">
            Theme
        </button>

    </div>

    <div class="max-w-6xl mx-auto px-6 pt-8">
        <h1 class="text-4xl font-bold">Admin Dashboard</h1>

        <p class="mt-2 text-gray-600 dark:text-slate-400">
            Manage employees, rescue locations, pet centers and system operations.
        </p>
    </div>

    <div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php if ($employee_level <= 1): ?>
            <?php  card_returner("./Employee/createEmployee.php", "Create Employee", "Add a new employee to the system with role and details."); ?>
            <?php card_returner("./rescue_point/createRescuePoint.php", "Create Rescue Point", "Register a new rescue location for emergency operations."); ?>
            <?php card_returner("./Employee/getAllEmployee.php", "All Employees", "View the full list of employees in the system."); ?>
            <?php card_returner("./rescue_point/seeRescueLocations.php", "Rescue Locations", "View all registered rescue points on the system."); ?>
            <?php card_returner("./PetCenters/createCenter.php" ,"Create Pet Center" , "Add a new pet care or shelter center to the platform.");?>
        <?php elseif ($employee_level == 2): ?>
            <?php card_returner("./manager_view/index.php", "Manager View", "Manage a rescue point"); ?>

            <?php if ($employee_level == 2 || $employee_level == 3): ?>
                <?php card_returner("./EmployeeView/seeAssignedAnimals.php", "Assigned Animals", "Manage Assigned Animals"); ?>
            <?php endif; ?>

        <?php endif; ?>
            <?php card_returner("./Employee/notification.php", "Notifications", "See Notifications"); ?>

    </div>

    <script src="../js/themetoggle.js"></script>

</body>

</html>