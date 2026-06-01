<?php
session_start();
include_once __DIR__ . "/../PDO/PDO.php";

function login_template()
{
    if (session_status() === PHP_SESSION_ACTIVE) {
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

    if ($_SERVER['REQUEST_METHOD'] !== "POST" || !isset($_POST["submit"])) {
        header("Location: login.php");
        exit();
    }

    if (
        !isset($_POST['email']) ||
        !isset($_POST['password']) ||
        !isset($_POST['type'])
    ) {
        $msg = urlencode("All fields are required");
        header("Location: login.php?msg=$msg");
        exit();
    }

    $user_data = [
        "email" => $_POST['email'],
        "password" => $_POST['password'],
        "type" => $_POST['type']
    ];

    $allowed_types = [
        "Users" => "user_id",
        "Employee" => "emp_id",
        "volunteers" => "volunteer_id"
    ];

    if (!isset($allowed_types[$user_data['type']])) {
        $msg = urlencode("Unknown user type");
        header("Location: login.php?msg=$msg");
        exit();
    }

    $table_name = $user_data['type'];

    $obj = PDO_class::initializer();

    if (!$obj->login_email_checker($user_data["email"], $table_name)) {
        $msg = urlencode("Email not verified");
        header("Location: login.php?msg=$msg");
        exit();
    }

    if (
        !$obj->password_checker(
            $user_data["email"],
            $user_data['password'],
            $table_name
        )
    ) {
        $msg = urlencode("Wrong password");
        header("Location: login.php?msg=$msg");
        exit();
    }

    session_start();
    session_regenerate_id(true);

    return $user_data;
}

if (isset($_POST['submit'])) {

    $userdata = login_template();

    $obj = PDO_class::initializer();

    session_start();

    $type_map = [
        "Users" => "user_id",
        "Employee" => "emp_id",
        "volunteers" => "volunteer_id"
    ];

    $table = $_POST['type'];
    $_type = $type_map[$table];

    $_SESSION['type'] = $table;

    $_SESSION['id'] = $obj->get_id(
        $_type,
        $_POST['email'],
        $_POST['type']
    );

    $msg = urlencode("Login successful");

    header("Location: login.php?msg=$msg");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" class="transition duration-300">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>
</head>

<body
    class="bg-gradient-to-br from-slate-100 to-slate-300 dark:from-slate-900 dark:to-slate-800 transition-colors duration-300 min-h-screen overflow-x-hidden">

    <div
        class="w-full flex justify-between items-center px-4 py-3 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md shadow-md fixed top-0 left-0 z-50">

        <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 text-white" href="../index.php">Home</a>


        <button id="themeToggle"
            class="px-3 py-2 rounded-xl bg-black text-white dark:bg-white dark:text-black font-semibold transition hover:scale-105 text-sm">
            Theme
        </button>

    </div>

    <div class="flex items-center justify-center px-4 pt-24 pb-6">

        <div
            class="w-full max-w-sm bg-white dark:bg-slate-900 shadow-2xl rounded-2xl p-6 border border-gray-200 dark:border-slate-700">

            <h1 class="text-2xl font-bold text-center mb-1 text-slate-800 dark:text-white">
                Welcome Back
            </h1>

            <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-5">
                Login to continue
            </p>

            <form id="login" action="" method="POST" class="flex flex-col gap-4">

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        Account Type
                    </label>

                    <select name="type"
                        class="p-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800 text-black dark:text-white outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        <option value="Users">User</option>
                        <option value="Employee">Employee</option>
                        <option value="volunteers">Volunteer</option>
                    </select>
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        Email
                    </label>

                    <input type="text" name="email" placeholder="Enter your email"
                        class="p-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800 text-black dark:text-white outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <div class="flex flex-col gap-1">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                        Password
                    </label>

                    <input type="password" name="password" placeholder="Enter your password"
                        class="p-2.5 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800 text-black dark:text-white outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                </div>

                <input type="submit" name="submit" value="Login"
                    class="p-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold cursor-pointer transition duration-200 hover:scale-[1.02] text-sm">
                <a class="p-2.5 rounded-xl bg-blue-600 text-center hover:bg-blue-700 text-white font-semibold cursor-pointer transition duration-200 hover:scale-[1.02] text-sm"
                href="./signup.php">Not Signed?</a>

            </form>

            <div class="mt-4 text-center">

                <?php
                if (isset($_GET['msg'])) {

                    $msg = htmlspecialchars($_GET['msg']);

                    $isSuccess = stripos($msg, 'success') !== false;

                    $color = $isSuccess
                        ? 'text-green-500'
                        : 'text-red-500';

                    echo "<p class='$color font-semibold text-sm'>$msg</p>";
                }
                ?>

            </div>

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