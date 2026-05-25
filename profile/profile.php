<?php
include_once __DIR__ . "/../PDO/PDO.php";
session_start();

if (isset($_SESSION['id'])) {

    if(isset($_POST['submit'])){
        PDO_class::initializer()->update_user();
        
        header("Location: profile.php");
        exit();
    }

    if ($_SESSION['type'] === "Users") {
        $res = PDO_class::initializer()->get_user_profile($_SESSION['id']);
    } else if ($_SESSION['type'] === 'Employee') {
        $res = PDO_class::initializer()->get_admin_profile($_SESSION['id']);
    } else {
        $res = PDO_class::initializer()->get_volunteer_profile($_SESSION['id']);
    }

    if (! $res) {
        $msg = urlencode("No Data found");
        header("Location: ../index.php?msg=$msg");
        exit();
    }

    $data = [
        'id' => $_SESSION['id'],
        'type' => $_SESSION['type'],
        'name' => $res['user_name'] ?? $res['emp_name'] ?? $res['volunteer_name'] ?? null,
        'email' => $res['email'],
        'image' => $res['emp_profile_picture_link'] ?? $res['user_profile_picture_link'] ?? $res['volunteer_image_link'] ?? null,
        'bio' => $res['user_bio'] ?? $res['emp_bio'] ?? $res['volunteer_bio'] ?? null
    ];



} else {
    header("Location: ../index.php?msg=" . urlencode("No session id found"));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="transition duration-300">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>

<body class="bg-gradient-to-br from-slate-100 to-slate-300 dark:from-slate-900 dark:to-slate-800 min-h-screen transition">

<div class="w-full flex justify-between items-center px-6 py-4 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md shadow-md fixed top-0 left-0 z-50">

    <h1 class="text-xl font-bold text-slate-800 dark:text-white">
        My Profile
    </h1>
    <div>
        <button id="themeToggle"
            class="px-4 py-2 rounded-xl bg-black text-white dark:bg-white dark:text-black font-semibold transition hover:scale-105 text-sm">
            Theme
        </button>
        <a href="../auth/logout.php"
            class="px-4 py-2 rounded-xl bg-red-500 text-white font-semibold hover:scale-105 transition">
            Logout
        </a>
    </div>


</div>

<div class="pt-24 px-6 max-w-6xl mx-auto grid md:grid-cols-2 gap-6">

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-xl border border-gray-200 dark:border-slate-700">

        <h2 class="text-xl font-bold mb-4 text-slate-800 dark:text-white">
            Update Profile
        </h2>

        <form method="POST" enctype="multipart/form-data" class="flex flex-col gap-4">

            <input type="hidden" id="id_" name="id">

            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    Bio
                </label>

                <input type="text"
                    name="add_bio"
                    id="add_bio"
                    placeholder="Write something about yourself"
                    class="w-full mt-1 p-3 rounded-xl bg-gray-100 dark:bg-slate-800 text-black dark:text-white border border-gray-300 dark:border-slate-700 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    Profile Image
                </label>

                <input type="file"
                    name="fileToUpload"
                    class="w-full mt-1 p-2 rounded-xl bg-gray-100 dark:bg-slate-800 text-black dark:text-white border border-gray-300 dark:border-slate-700">
            </div>

            <input type="submit"
                name="submit"
                value="Update Profile"
                class="p-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold cursor-pointer transition">

        </form>
    </div>

    <div id="output"
        class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow-xl border border-gray-200 dark:border-slate-700 flex flex-col items-center text-center">
    </div>

</div>

<script>
(function () {

    const data = <?php echo json_encode($data); ?>;

    const output = document.getElementById("output");
    output.innerHTML = "Loading...";

    output.innerHTML = `
        <div class="flex flex-col items-center gap-4">

            <img src="${data.image}"
                class="w-28 h-28 rounded-full object-cover border-4 border-blue-500 shadow-md">

            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">
                ${data.name}
            </h2>

            <p class="text-gray-500 dark:text-gray-400">
                ${data.email}
            </p>

            <div class="text-sm px-3 py-1 rounded-full bg-blue-100 text-blue-700 dark:bg-slate-800 dark:text-blue-300">
                ${data.type}
            </div>

            <p class="text-gray-600 dark:text-gray-300 mt-2">
                ${data.bio || "No bio yet"}
            </p>

            <div class="text-xs text-gray-400 mt-2">
                ID: ${data.id}
            </div>

        </div>
    `;

    id_.value = data.id;

})();
</script>

<script>
        const btn = document.getElementById("themeToggle");

        let str = ThemeChecker();

        if (str === 'dark') {
            document.documentElement.classList.add("dark");
        }

        btn.onclick = () => {

            const currentTheme = localStorage.getItem('theme');

            const nextTheme =
                currentTheme === 'light'
                    ? 'dark'
                    : 'light';

            localStorage.setItem('theme', nextTheme);

            document.documentElement.classList.toggle("dark");
        };

        function ThemeChecker() {

            let obj = localStorage.getItem('theme');

            if (!obj) {
                localStorage.setItem('theme', 'light');
                return 'light';
            }

            return obj;
        }
</script>
</body>
</html>