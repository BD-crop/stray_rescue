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

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>

<body class="bg-gray-100 dark:bg-slate-900 text-gray-900 dark:text-gray-100 transition">

<div class="flex justify-between items-center px-6 py-4 bg-white dark:bg-slate-800 shadow-md sticky top-0">

    <div class="flex gap-3">
        <a class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-slate-700 hover:scale-105 transition" href="./Map/index.php">Map</a>
        <a class="px-4 py-2 rounded-xl bg-gray-100 dark:bg-slate-700 hover:scale-105 transition" href="./shop/shop.php">Shop</a>
    </div>

    <div class="flex gap-3 items-center">

        <?php if(!isset($_SESSION["id"])): ?>
            <a class="px-4 py-2 rounded-xl bg-blue-500 text-white" href="./auth/login.php">Login</a>
            <a class="px-4 py-2 rounded-xl bg-green-500 text-white" href="./auth/signup.php">Signup</a>
        <?php else: ?>

            <?php
                $data = PDO_class::initializer()->find_employee_level();
                if($data >= 0):
            ?>
                <a class="px-4 py-2 rounded-xl bg-purple-500 text-white" href="./admin">Admin</a>
            <?php endif; ?>

            <a class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-slate-700" href="./post/upload_post.php">Upload</a>
            <a class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-slate-700" href="./post/posts.php">Posts</a>
            <a class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-slate-700" href="./profile/profile.php">Profile</a>
            <a class="px-4 py-2 rounded-xl bg-red-500 text-white" href="./auth/logout.php">Logout</a>

        <?php endif; ?>

        <button id="themeToggle"
            class="px-4 py-2 rounded-xl bg-black text-white dark:bg-white dark:text-black">
            Theme
        </button>

    </div>
</div>

<div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

    <div class="p-6 rounded-2xl bg-white dark:bg-slate-800 shadow">
        <?php $obj = PDO_class::initializer()->count_total_volunteers(); ?>
        <h1 class="text-xl font-bold">Volunteers</h1>
        <p>Total: <?= $obj['Inactive'] + $obj['Active']; ?></p>
        <p>Active: <?= $obj['Active']; ?></p>
        <a class="text-blue-500 mt-2 inline-block" href="./volunteer/LeaderBoard.php">Leaderboard</a>
    </div>

    <div class="p-6 rounded-2xl bg-white dark:bg-slate-800 shadow">
        <?php $obj = PDO_class::initializer()->count_total_user(); ?>
        <h1 class="text-xl font-bold">Users</h1>
        <p>Total: <?= $obj['Inactive'] + $obj['Active']; ?></p>
        <p>Active: <?= $obj['Active']; ?></p>
    </div>

    <div class="p-6 rounded-2xl bg-white dark:bg-slate-800 shadow">
        <?php $obj = PDO_class::initializer()->count_total_RescuePoint(); ?>
        <h1 class="text-xl font-bold">Rescue Points</h1>
        <p>Total: <?= $obj['Inactive'] + $obj['Active']; ?></p>
        <p>Active: <?= $obj['Active']; ?></p>
        <a class="text-blue-500 mt-2 inline-block" href="./Map/RescuePoint.php">View Map</a>
    </div>

    <div class="p-6 rounded-2xl bg-white dark:bg-slate-800 shadow">
        <?php $obj = PDO_class::initializer()->count_total_RescuePOST(); ?>
        <h1 class="text-xl font-bold">Rescue Posts</h1>
        <p>Total: <?= $obj['Emergency'] + $obj['Attention Needed'] + $obj['Healty Animal']; ?></p>
        <p>Emergency: <?= $obj['Emergency']; ?></p>
        <a class="text-blue-500 mt-2 inline-block" href="./Map/RescuePost.php">View Posts</a>
    </div>

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