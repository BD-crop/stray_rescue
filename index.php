<?php
include_once __DIR__."/PDO/PDO.php";
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stray Rescue</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>

<body class="bg-gray-100 dark:bg-slate-900 text-gray-900 transition">

<div class="flex justify-between items-center px-6 py-4 bg-white dark:bg-slate-800 shadow-md sticky top-0">

    <div class="flex gap-3">
        <a class="px-4 py-2 rounded-xl bg-indigo-200 dark:bg-indigo-200 hover:scale-105 transition" href="./Map/index.php">Map</a>
        <a class="px-4 py-2 rounded-xl bg-red-300 dark:bg-red-300 hover:scale-105 transition" href="./shop/shop.php">Shop</a>
    </div>

    <div class="flex gap-3 items-center">

        <?php if(!isset($_SESSION["id"])): ?>
            <a class="px-4 py-2 rounded-xl bg-blue-500 text-white" href="./auth/login.php">Login</a>
            <a class="px-4 py-2 rounded-xl bg-green-700 text-white" href="./auth/signup.php">Signup</a>
        <?php else: ?>

            <?php
                $data = PDO_class::initializer()->find_employee_level();
                if($data >= 0):
            ?>
                <a class="px-4 py-2 rounded-xl bg-purple-500 text-white" href="./admin">Admin</a>
            <?php endif; ?>

            <a class="px-4 py-2 rounded-xl bg-amber-200" href="./post/upload_post.php">Upload</a>
            <a class="px-4 py-2 rounded-xl bg-indigo-300" href="./post/posts.php">Posts</a>
            <a class="px-4 py-2 rounded-xl bg-green-300" href="./profile/profile.php">Profile</a>
            <a class="px-4 py-2 rounded-xl bg-red-500 text-white" href="./auth/logout.php">Logout</a>

        <?php endif; ?>
        <a href="#about"
        class="px-4 py-2 rounded-xl font-medium transition
                bg-gray-500 text-gray-200 hover:bg-gray-300
                dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-slate-300">
        About Stray Rescue
        </a>        
        <a href="#contact"
        class="px-4 py-2 rounded-xl font-medium transition
                bg-gray-500 text-gray-200 hover:bg-gray-300
                dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-slate-300">
        Contact US
        </a> 
<button id="themeToggle"
            class="px-4 py-2 rounded-xl bg-black text-white dark:bg-white dark:text-black">
            Theme
        </button>

    </div>
</div>

<div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="items-start flex flex-col gap-4 row-span-2 p-6 rounded-2xl bg-amber-200 shadow-2xl shadow-black/30">
        <?php $obj = PDO_class::initializer()->count_total_RescuePOST(); ?>
        <h1 class="text-xl font-bold">Registered Animals</h1>
        <div>
        <p>Total: <?= $obj['Emergency'] + $obj['Attention Needed'] + $obj['Healty Animal']; ?></p>
        <p>Emergency: <?= $obj['Emergency']; ?></p>
        </div>

        <br>
        <p class="leading-relaxed">
        Stray Rescue tracks reports of stray and injured animals submitted by the community. Cases are categorized by urgency, helping volunteers, organizations, and local residents identify animals that may require assistance and coordinate rescue efforts more effectively.
        </p>
        <a class="px-4 py-2 rounded-xl bg-red-800 text-gray-100  hover:scale-105 transition mt-2 inline-block" href="./Map/RegisteredAnimals.php">View Animals</a>
    </div>
    <div class="items-start flex flex-col gap-4  p-6 rounded-2xl bg-green-300 shadow-2xl shadow-black/30">
        <?php $obj = PDO_class::initializer()->count_total_user(); ?>
        <h1 class="text-xl font-bold">Community Members</h1>
        <div>
                    <p>Total: <?= $obj['Inactive'] + $obj['Active']; ?></p>
        <p>Active: <?= $obj['Active']; ?></p>
        </div>

        <p class="leading-relaxed">Community Members are at the heart of Stray Rescue . These members
            helps the platform keep track of stray animals , ensuring a safe life for that animal.
        </p>
    </div>

    <div class="items-start row-span-2 flex flex-col gap-7 p-6 align-center rounded-2xl bg-red-200 shadow-2xl shadow-black/30 ">
        <?php $obj = PDO_class::initializer()->count_total_volunteers(); ?>
        <h1 class="text-xl font-bold">Volunteers</h1>
        <div>
        <p>Total: <?= $obj['Inactive'] + $obj['Active']; ?></p>
        <p>Active: <?= $obj['Active']; ?></p>
        </div>

        <p class="leading-relaxed">
        Volunteers are the core support system of Stray Rescue. They respond to reports, assist with rescues, and help ensure that injured or stray animals receive timely care. Their coordination and action play a key role in improving animal welfare across the community.
        </p>
        <a class="px-4 py-2 rounded-xl bg-sky-800 text-gray-100 mt-2 inline-block" href="./volunteer/LeaderBoard.php">Volunteer Leaderboard</a>

    </div>



    <div class="items-start flex flex-col gap-4 p-6 rounded-2xl bg-indigo-300 shadow-2xl shadow-black/30">
        <?php $obj = PDO_class::initializer()->count_total_RescuePoint(); ?>
        <h1 class="text-xl font-bold">Rescue Points</h1>
        <div>
                    <p>Total: <?= $obj['Inactive'] + $obj['Active']; ?></p>
        <p>Active: <?= $obj['Active']; ?></p>
        </div>

        <p class="leading-relaxed">
        Stray Rescue hosts animal shelters through out the country. These rescue shelters are dedicated to providing 
        safe shelters for animals.
        </p>
        <a class="px-4 py-2 rounded-xl bg-amber-400 text-gray-800  text-blue-500 mt-2 inline-block" href="./Map/RescuePoint.php">View Map</a>
    </div>


</div>





<div id="about"
     class="max-w-4xl mx-auto mt-10 p-6 rounded-2xl shadow-2xl shadow-black/30
            bg-cyan-300 text-gray-800
            dark:bg-cyan-300
            border border-gray-200 transition">

    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        About Stray Rescue
    </h2>

    <p class="text-base leading-relaxed text-gray-900">
        <strong>Stray Rescue</strong> is a community-based street animal registry, vaccination, and rescue platform focused on Bangladesh. Stray Rescue is dedicated to reducing the suffering of street animals by creating a community-oriented rescue system that fosters community participation, animal welfare, and stronger human–animal connections.
    </p>

</div>


<div id="contact"
     class="max-w-4xl mx-auto mt-10 p-6 rounded-2xl shadow-lg shadow-2xl shadow-black/60
            bg-red-300 text-gray-800
            dark:bg-red-300
            border border-gray-200 dark:border-slate-700 transition">

    <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        Contact US
    </h2>

    <p id="number1">
        017xxxxxxxx
    </p>
    <p id="number1">
        016xxxxxxxx
    </p>
    <p id="email">
        stray_rescue@stray_rescue.com
    </p>
</div>


<script>
const btn = document.getElementById("themeToggle");

let str = ThemeChecker();

if(str!=='light'){
        document.documentElement.classList.toggle("dark");
}

btn.onclick = () => {
    localStorage.setItem('theme' , (localStorage.getItem('theme') === 'light') ?'dark' :'light' );
    console.log(localStorage.getItem('theme'));
    document.documentElement.classList.toggle("dark");
};

function ThemeChecker(){
  let obj = localStorage.getItem('theme');
  
  if(!obj){
                localStorage.setItem('theme' , 'light');
                return 'light';
  }
  return obj;

};
</script>

</body>
</html>