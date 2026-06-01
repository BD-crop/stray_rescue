<?php
session_start();

INCLUDE_ONCE __DIR__."/../PDO/PDO.php";

function signup_template(){

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

    if(
        !isset($_POST['email']) ||
        !isset($_POST['password']) ||
        !isset($_POST['table'])
    ){
        $msg = urlencode("All fields are required");

        header("Location: signup.php?msg=$msg");
        exit();
    }

    $user_data = [];

    $user_data["name"] = $_POST['name'];
    $user_data["email"] = $_POST['email'];
    $user_data['password'] = $_POST['password'];
    $user_data['type'] = $_POST['table'];

    $table_name = $user_data['type'];

    if(
        !(
            $user_data['type'] === 'Users' ||
            $user_data['type'] === 'Employee' ||
            $user_data['type'] === 'volunteers'
        )
    ){
        $msg = urlencode("wrong type of user");

        header("Location: signup.php?msg=$msg");
        exit();
    }

    $obj = PDO_class::initializer();

    if(($obj->email_checker($user_data["email"], $table_name))){
        $msg = urlencode("email already exists");

        header("Location: signup.php?msg=$msg");
        exit();
    }

    if($table_name == "Users"){
        $obj->user_insert(
            $user_data['name'],
            $user_data["email"],
            $user_data['password']
        );
    }
    else if($table_name === "Employee"){
        $obj->admin_insert(
            $user_data['name'],
            $user_data["email"],
            $user_data['password']
        );
    }
    else{
        $obj->volunteer_insert(
            $user_data['name'],
            $user_data["email"],
            $user_data["password"]
        );
    }

    session_start();
    session_regenerate_id(true);

    $email = $user_data["email"];

    $msg = urlencode("mail sent to $email");

    header("Location: signup.php?msg=$msg");
    exit();
}

if(isset($_POST['submit'])){
    signup_template();
}
?>

<!DOCTYPE html>
<html lang="en" class="transition duration-300">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *{
            font-family:'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-100 to-slate-300 dark:from-slate-900 dark:to-slate-800 transition-colors duration-300 min-h-screen overflow-x-hidden">

    <div
        class="w-full flex justify-between items-center px-4 py-3 bg-white/80 dark:bg-slate-800/80 backdrop-blur-md shadow-md fixed top-0 left-0 z-50">

        <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 text-white" href="../index.php">Home</a>


        <button id="themeToggle"
            class="px-3 py-2 rounded-xl bg-black text-white dark:bg-white dark:text-black font-semibold transition hover:scale-105 text-sm">
            Theme
        </button>

    </div>

<div class="flex items-center justify-center px-4 pt-24 pb-6">

    <div class="w-full max-w-md bg-white dark:bg-slate-900 shadow-2xl rounded-3xl p-7 border border-gray-200 dark:border-slate-700">

        <div class="text-5xl text-center mb-2">
            🐾
        </div>

        <h1 class="text-3xl font-bold text-center text-slate-800 dark:text-white">
            Create Account
        </h1>

        <p class="text-center text-gray-500 dark:text-gray-400 mt-1 mb-6 text-sm">
            Join the Stray Rescue Platform
        </p>

        <?php
            if(isset($_GET['msg'])){

                $msg = htmlspecialchars($_GET['msg']);

                $class =
                    (stripos($msg, 'mail sent') !== false)
                    ? 'bg-green-100 text-green-700 border-green-300'
                    : 'bg-red-100 text-red-700 border-red-300';

                echo "
                    <div class='mb-5 px-4 py-3 rounded-xl border text-sm font-medium $class'>
                        $msg
                    </div>
                ";
            }
        ?>

        <form action="" method="POST" class="flex flex-col gap-4">

            <div class="flex flex-col gap-1">

                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    Full Name
                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Enter your full name"
                    class="p-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800 text-black dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                >

            </div>

            <div class="flex flex-col gap-1">

                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    Email Address
                </label>

                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    class="p-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800 text-black dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                >

            </div>

            <div class="flex flex-col gap-1">

                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    class="p-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800 text-black dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                >

            </div>

            <div class="flex flex-col gap-1">

                <label class="text-sm font-medium text-slate-700 dark:text-slate-300">
                    Account Type
                </label>

                <select
                    name="table"
                    class="p-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-gray-100 dark:bg-slate-800 text-black dark:text-white outline-none focus:ring-2 focus:ring-blue-500"
                >

                    <option value="Users">
                        User
                    </option>

                    <option value="Employee">
                        Employee
                    </option>

                    <option value="volunteers">
                        Volunteer
                    </option>

                </select>

            </div>

            <input
                type="submit"
                name="submit"
                value="Create Account"
                class="p-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold cursor-pointer transition duration-200 hover:scale-[1.02]"
            >

        </form>

        <div class="text-center mt-5 text-sm text-gray-500 dark:text-gray-400">

            Already have an account?

            <a
                href="login.php"
                class="text-blue-600 hover:text-blue-700 font-semibold"
            >
                Login
            </a>

        </div>

    </div>

</div>

<script>
const btn = document.getElementById("themeToggle");

let str = ThemeChecker();

if(str === 'dark'){
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

function ThemeChecker(){

    let obj = localStorage.getItem('theme');

    if(!obj){
        localStorage.setItem('theme', 'light');
        return 'light';
    }

    return obj;
}
</script>

</body>
</html>