<?php
    include __DIR__ ."/../auth_all_Employee.php";
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

        
        <a href="./animals/AddAnimal.php"
        class="block p-6 rounded-2xl bg-gray-300 dark:bg-slate-800 shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add Animals</h2>
            <p class="text-gray-600 dark:text-slate-300 mt-2">
                Add New Animal to your rescue point
            </p>
        </a>


        <a href="./animals/seeAllAnimals.php"
        class="block p-6 rounded-2xl bg-gray-300 dark:bg-slate-800 shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">See All Animals</h2>
            <p class="text-gray-600 dark:text-slate-300 mt-2">
                See all the animals that are currently residing at your shelter.
            </p>
        </a>

        <a href="./employees/seeEmployees.php"
        class="block p-6 rounded-2xl bg-gray-300 dark:bg-slate-800 shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">See All Employees</h2>
            <p class="text-gray-600 dark:text-slate-300 mt-2">
                See all the employees under you .
            </p>
        </a>

        <a href="./adoption_management/seeAllAdoptionRequest.php"
        class="block p-6 rounded-2xl bg-gray-300 dark:bg-slate-800 shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Adoption Requests</h2>
            <p class="text-gray-600 dark:text-slate-300 mt-2">
                See All Adoption Requests .
            </p>
        </a>


    </div>



<script>
    const btn = document.getElementById("themeToggle");

    let str = ThemeChecker();

    if(str !== 'light'){
        document.documentElement.classList.add("dark");
    }

    btn.onclick = () => {
        localStorage.setItem('theme',
            (localStorage.getItem('theme') === 'light') ? 'dark' : 'light'
        );
        document.documentElement.classList.toggle("dark");
    };

    function ThemeChecker(){
        let obj = localStorage.getItem('theme');

        if(!obj){
            localStorage.setItem('theme','light');
            return 'light';
        }
        return obj;
    }

    function eventListenerToggle(){
        let theme = ThemeChecker();

        document.documentElement.classList.toggle("dark" ,theme === 'dark');
    }

    window.addEventListener("storage", (event) => {
        if (event.key === "theme") {
            eventListenerToggle();
        }
    });
</script>

</body>
</html>