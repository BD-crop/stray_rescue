<?php
include_once __DIR__."/PDO/PDO.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stray Rescue</title>

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

<body class="bg-gray-100 dark:bg-slate-900 text-gray-900 transition">



<div id="overlay"
     class="fixed inset-0 bg-black/40 hidden sm:hidden z-40"></div>

<div id="hamburger_container"
     class="fixed top-0 left-0 h-full w-64
            bg-white text-gray-900
            dark:bg-slate-800 dark:text-gray-100
            shadow-xl
            transform -translate-x-full transition-transform duration-300 z-50
            flex flex-col gap-2 p-6">

    <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition"
       href="./Map/index.php">
        Map
    </a>

    <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition"
       href="./shop/shop.php">
        Shop
    </a>

    <?php if(!isset($_SESSION["id"])): ?>
        <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition"
           href="./auth/login.php">
            Login
        </a>

        <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition"
           href="./auth/signup.php">
            Signup
        </a>

    <?php else: ?>

        <?php
            $data = PDO_class::initializer()->find_employee_level();
            if($data >= 0):
        ?>
            <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition"
               href="./admin">
                Admin
            </a>
        <?php endif; ?>
        <?php if($data < 0): ?>
        <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition" href="./post/upload_post.php">
            Upload
        </a>
        <?php endif;?>

        <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition"
           href="./animals/index.php">
            Animals
        </a>

        <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition"
           href="./profile/profile.php">
            Profile
        </a>

        <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition"
           href="./auth/logout.php">
            Logout
        </a>

    <?php endif; ?>

        <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition" 
        href="./community/community_challenges.php">
            Community Challenges
        </a>

    <a class="px-3 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition"
       href="#about">
        About
    </a>

</div>

<div class="flex justify-between items-center px-6 py-4 bg-white dark:bg-slate-800 shadow-md sticky top-0">

    <div class="flex gap-3 ml-10 sm:ml-0">
        <button id="menuBtn"
            class="p-2 rounded-lg bg-gray-200 sm:hidden fixed top-4 left-4 z-50">
            ☰
        </button>
        <a class="hidden sm:block px-4 py-2 rounded-xl bg-indigo-200 dark:bg-indigo-200 hover:scale-105 transition" href="./Map/index.php">Map</a>
        <a class="hidden sm:block px-4 py-2 rounded-xl bg-red-300 dark:bg-red-300 hover:scale-105 transition" href="./shop/shop.php">Shop</a>
    </div>

    <div class="flex gap-3 items-center">

        <?php if(!isset($_SESSION["id"])): ?>
            <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 text-white" href="./auth/login.php">Login</a>
            <a class="hidden sm:block px-4 py-2 rounded-xl bg-green-700 text-white" href="./auth/signup.php">Signup</a>
        <?php else: ?>

            <?php
                $data = PDO_class::initializer()->find_employee_level();
                if($data >= 0):
            ?>
                <a class="hidden sm:block px-4 py-2 rounded-xl bg-purple-500 text-white" href="./admin">Admin</a>
            <?php endif; ?>
            <?php $role12 = PDO_class::initializer()->type_of_user();
            if($role12 === 'user'):
             ?>
            <a class="hidden sm:block px-4 py-2 rounded-xl bg-yellow-300" href="./post/upload_post.php">Upload Post</a>

            <?php endif;?>
            <a class="hidden sm:block px-4 py-2 rounded-xl bg-indigo-300" href="./animals/index.php">Animals</a>
            <a class="hidden sm:block px-4 py-2 rounded-xl bg-green-300" href="./profile/profile.php">Profile</a>
            <a class="hidden sm:block px-4 py-2 rounded-xl bg-red-500 text-white" href="./auth/logout.php">Logout</a>

        <?php endif; ?>
        <a
            href="./community/community_challenges.php"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl
                bg-indigo-500 text-white
                hover:bg-indigo-600
                shadow-md hover:shadow-lg
                transition-all duration-300">
            Community Challenges
        </a>
        <a href="#about"
        class="hidden sm:block px-4 py-2 rounded-xl font-medium transition bg-gray-500 text-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-slate-300">
        About
        </a>


        <button id="themeToggle"
            class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-white dark:text-black">
            Theme
        </button>

    </div>
</div>

<div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="items-start flex flex-col gap-4 row-span-2 p-6 rounded-2xl bg-amber-200 shadow-2xl shadow-black/30">
        <?php $obj = PDO_class::initializer()->count_total_RescuePOST(); ?>
        <h1 class="flex items-center gap-2 text-xl font-bold"><img class="w-9 h-9" src="https://www.svgrepo.com/show/454509/animal-cat-domestic.svg" alt="Registered Animals"> Registered Animals</h1>

        <div>
            <p>Total: <?= $obj['Emergency'] + $obj['Attention Needed'] + $obj['Healty Animal']; ?></p>
            <p>Emergency: <?= $obj['Emergency']; ?></p>
        </div>

        <p class="leading-relaxed">
            Stray Rescue tracks reports of stray and injured animals submitted by the community. Cases are categorized by urgency, helping volunteers, organizations, and local residents identify animals that may require assistance and coordinate rescue efforts more effectively.
        </p>

        <a class="px-4 py-2 rounded-xl bg-red-800 text-gray-100 hover:scale-105 transition mt-2 inline-block"
           href="./Map/RegisteredAnimals.php">
            View Animals
        </a>
    </div>

    <div class="items-start flex flex-col gap-4 p-6 rounded-2xl bg-green-300 shadow-2xl shadow-black/30">
        <?php $obj = PDO_class::initializer()->count_total_user(); ?>
        <h1 class="flex items-center gap-2 text-xl font-bold"><img class="w-9 h-9" src="https://www.svgrepo.com/show/265357/teamwork-team.svg" alt="Community Members"> Community Members</h1>

        <div>
            <p>Total: <?= $obj['Inactive'] + $obj['Active']; ?></p>
            <p>Active: <?= $obj['Active']; ?></p>
        </div>

        <p class="leading-relaxed">
            Community Members are at the heart of Stray Rescue. These members help the platform keep track of stray animals, ensuring a safe life for them.
        </p>
    </div>

    <div class="items-start row-span-2 flex flex-col gap-7 p-6 rounded-2xl bg-red-200 shadow-2xl shadow-black/30">
        <?php $obj = PDO_class::initializer()->count_total_volunteers(); ?>
        <h1 class="flex items-center gap-2 text-xl font-bold"><img class="w-9 h-9" src="https://www.svgrepo.com/show/380334/volunteer-kindness-care-heart-love.svg" alt="Volunteers"> Volunteers</h1>

        <div>
            <p>Total: <?= $obj['Inactive'] + $obj['Active']; ?></p>
            <p>Active: <?= $obj['Active']; ?></p>
        </div>

        <p class="leading-relaxed">
            Volunteers are the core support system of Stray Rescue. They respond to reports, assist with rescues, and help ensure that injured or stray animals receive timely care. Their coordination and action play a key role in improving animal welfare across the community.
        </p>

        <a class="px-4 py-2 rounded-xl bg-sky-800 text-gray-100 hover:scale-105 transition mt-2 inline-block"
           href="./volunteer/LeaderBoard.php">
            Volunteer Leaderboard
        </a>
    </div>

    <div class="items-start flex flex-col gap-4 p-6 rounded-2xl bg-indigo-300 shadow-2xl shadow-black/30">
        <?php $obj = PDO_class::initializer()->count_total_RescuePoint(); ?>
        <h1 class="flex items-center gap-2 text-xl font-bold"><img class="w-9 h-9" src="https://www.svgrepo.com/show/513317/location-pin.svg" alt="Rescue Points"> Rescue Points</h1>

        <div>
            <p>Total: <?= $obj['Inactive'] + $obj['Active']; ?></p>
            <p>Active: <?= $obj['Active']; ?></p>
        </div>

        <p class="leading-relaxed">
            Stray Rescue hosts animal shelters throughout the country. These rescue shelters are dedicated to providing safe shelter for animals.
        </p>

        <a class="px-4 py-2 rounded-xl bg-amber-400 text-gray-800 hover:scale-105 transition mt-2 inline-block"
           href="./Map/RescuePoint.php">
            View Map
        </a>
    </div>

</div>

<div id="about"
     class="max-w-4xl mx-auto mt-10 p-6 rounded-2xl shadow-2xl shadow-black/30 bg-cyan-300 text-gray-800">

<h2 class="flex items-center gap-2 text-2xl font-bold mb-4">
    <img
        class="w-6 h-6"
        src="https://www.svgrepo.com/show/451662/help-about.svg"
        alt=""
    >
    About Us
</h2>

    <p class="text-base leading-relaxed text-gray-900">
        <strong>Stray Rescue</strong> is a community-based street animal registry, vaccination, and rescue platform focused on Bangladesh. It fosters community participation and coordinated rescue efforts to reduce animal suffering.
    </p>

</div>

<div id="contact"
     class="max-w-4xl mx-auto mt-10 p-6 rounded-2xl shadow-2xl shadow-black/60 bg-red-300 text-gray-800">
    
<h2 class="flex items-center gap-2 text-2xl font-bold mb-4">
    <img
        class="w-7 h-7"
        src="https://www.svgrepo.com/show/526083/phone-calling-rounded.svg"
        alt=""
    >
    Contact Us
</h2>

    <p>017xxxxxxxx</p>
    <p>016xxxxxxxx</p>
    <p>stray_rescue@stray_rescue.com</p>

</div>

<script src="./js/themetoggle.js"></script>

<script>
const menuBtn = document.getElementById("menuBtn");
const sidebar = document.getElementById("hamburger_container");
const overlay = document.getElementById("overlay");

menuBtn.onclick = () => {
    sidebar.classList.toggle("-translate-x-full");
    overlay.classList.toggle("hidden");
};

overlay.onclick = () => {
    sidebar.classList.add("-translate-x-full");
    overlay.classList.add("hidden");
};
</script>

</body>
</html>