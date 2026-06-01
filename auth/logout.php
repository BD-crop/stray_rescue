<?php
session_start();

if (isset($_COOKIE[session_name()])) {

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}
?>

<!DOCTYPE html>
<html lang="en" class="transition duration-300">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>

<body
    class="min-h-screen bg-gradient-to-br from-slate-100 to-slate-300 dark:from-slate-900 dark:to-slate-800 flex items-center justify-center px-4 transition-colors duration-300">

    <div
        class="w-full flex justify-between items-center px-4 py-3 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md shadow-md fixed top-0 left-0 z-50">

        <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 text-white" href="../index.php">Home</a>


        <button id="themeToggle"
            class="px-4 py-2 rounded-xl bg-black text-white dark:bg-white dark:text-black font-semibold transition hover:scale-105 text-sm">
            Theme
        </button>

    </div>

    <div
        class="w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl rounded-3xl p-8 border border-gray-200 dark:border-slate-700 text-center">

        <div class="text-6xl mb-4">
            👋
        </div>

        <h2 class="text-3xl font-bold text-slate-800 dark:text-white mb-2">
            Logout Successful
        </h2>

        <h4 class="text-gray-500 dark:text-gray-400 mb-6">
            Login again to continue using Stray Rescue
        </h4>

        <div class="flex justify-center gap-3">

            <a href="login.php"
                class="px-5 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                Login
            </a>

            <a href="../index.php"
                class="px-5 py-3 rounded-xl bg-slate-200 dark:bg-slate-700 dark:text-white hover:scale-105 transition font-semibold">
                Home
            </a>

        </div>

    </div>

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