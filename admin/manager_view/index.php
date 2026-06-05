<?php
    include __DIR__ ."/../auth_all_Employee.php";
    include __DIR__ ."/../../template/card_template.php";
    $employee_level = $level;


    if($employee_level != 2){
        header("Location: ../index.php");
        exit();
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Manager View</title>

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

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white transition-all duration-500">

    <div class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md bg-dark/30 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

        <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition" href="../index.php">
            Go Back
        </a>

        <button id="themeToggle"
            class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg">
            Theme
        </button>

    </div>

    <div class="max-w-6xl mx-auto px-6 pt-8">
        <h1 class="text-4xl font-bold">Manager View</h1>

        <p class="mt-2 text-gray-600 dark:text-slate-400">
            Manage employees, shelter animals and day to day operations of a location.
        </p>
    </div>

    <div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6">   
        <?php  card_returner("./animals/AddAnimal.php", "Add Animals", "Add New Animal to your rescue point"); ?>
        <?php card_returner("./animals/seeAllAnimals.php", "See All Animals", "See all the animals that are currently residing at your shelter."); ?>
        <?php card_returner("./employees/seeEmployees.php", "See All Employees", "See all the employees under you ."); ?>
        <?php card_returner("./adoption_management/seeAllAdoptionRequest.php", "Adoption Requests", "See all adoption requests."); ?>
    </div>

<script src="../../js/themetoggle.js"></script>

</body>
</html>