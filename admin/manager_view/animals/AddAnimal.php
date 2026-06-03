<?php
include __DIR__ . "/../../auth_all_Employee.php";
//  ini_set('display_errors', 1);
//  ini_set('display_startup_errors', 1);
//  error_reporting(E_ALL); 
$employee_level = $level;

if ($employee_level != 2) {
    header("Location: ../index.php");
    exit();
}
$rescue_point_id = $obj->getManagerRescuePointID($_SESSION['id']);

if (isset($_SESSION['id'])) {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $obj12 = PDO_class::initializer();

        if (!isset($_POST['submit'])) {
            $msg = urlencode("Not correct Submission");
            header("Location: addAnimal.php?msg=$msg");
            exit();
        }

        if (
            !isset($_POST['rescue_point_id']) ||
            !isset($_POST['animal_name']) ||
            !isset($_POST['animal_age']) ||
            !isset($_POST['health_status']) ||
            !isset($_FILES['fileToUpload'])
        ) {
            $msg = urlencode("All fields must be present");
            header("Location: addAnimal.php?msg=$msg");
            exit();
        }

        try {

            $res = $obj12->addAnimal();

            header("Location: seeIndividualAnimal.php?animal_id=$res");
            exit();

        } catch (Exception $e) {

            $msg = urlencode($e->getMessage());
            header("Location: addAnimal.php?msg=$msg");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Animal</title>

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

    <div class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md bg-white/70 dark:bg-slate-900/70 border-b border-gray-200 dark:border-slate-700">

        <a
            class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
            href="../index.php"
        >
            Go Back
        </a>

        <button
            id="themeToggle"
            class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg"
        >
            Theme
        </button>

    </div>

    <div class="max-w-3xl mx-auto px-4 py-8">

        <?php if (isset($_GET['msg'])): ?>
            <div class="mb-6 rounded-2xl border border-red-300 bg-red-100 text-red-700 px-4 py-3 shadow">
                <?= htmlspecialchars($_GET['msg']) ?>
            </div>
        <?php endif; ?>

        <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-gray-200 dark:border-slate-700 overflow-hidden">

            <div class="p-8 border-b border-gray-200 dark:border-slate-700">
                <h1 class="text-3xl md:text-4xl font-bold">
                    Add Animal
                </h1>

                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    Register a new animal into the shelter system.
                </p>
            </div>

            <form
                id="rescueForm"
                enctype="multipart/form-data"
                method="POST"
                class="p-8 space-y-6"
            >

                <div>


                    <input
                        type="hidden"
                        name="rescue_point_id"
                        value="<?= $rescue_point_id ;?>"
                        placeholder="Enter rescue point ID"
                        class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition"
                    >
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold">
                        Animal Name
                    </label>

                    <input
                        type="text"
                        name="animal_name"
                        required
                        placeholder="Enter animal name"
                        class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition"
                    >
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold">
                        Animal Age
                    </label>

                    <input
                        type="number"
                        step="0.1"
                        min="0"
                        name="animal_age"
                        required
                        placeholder="Enter age"
                        class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition"
                    >
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold">
                        Health Status
                    </label>

                    <select
                        name="health_status"
                        class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3 outline-none focus:ring-2 focus:ring-blue-500 transition"
                    >
                        <option value="1">Normal / Healthy</option>
                        <option value="2">Attention Needed Soon</option>
                        <option value="3">Emergency</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-2 text-sm font-semibold">
                        Animal Images
                    </label>

                    <div id="imageContainer" class="space-y-3">
                        <div class="image-input-group">
                            <input
                                type="file"
                                name="fileToUpload[]"
                                required
                                class="block w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3"
                            >
                        </div>
                    </div>

                    <button
                        type="button"
                        id="addImageBtn"
                        class="mt-4 px-4 py-2 rounded-xl bg-gray-200 hover:bg-gray-300 dark:bg-slate-700 dark:hover:bg-slate-600 transition"
                    >
                        + Add Another Image
                    </button>
                </div>

                <button
                    type="submit"
                    name="submit"
                    class="w-full py-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold shadow-lg transition-all duration-300 hover:scale-[1.01]"
                >
                    Submit Animal
                </button>

            </form>

        </div>

    </div>

    <script>

        document.getElementById("addImageBtn").addEventListener("click", () => {

            const container = document.getElementById("imageContainer");

            const div = document.createElement("div");

            div.className = "image-input-group";

            div.innerHTML = `
                <input
                    type="file"
                    name="fileToUpload[]"
                    class="block w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-3"
                >
            `;

            container.appendChild(div);
        });

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

            document.documentElement.classList.toggle("dark", theme === 'dark');
        }

        window.addEventListener("storage", (event) => {
            if (event.key === "theme") {
                eventListenerToggle();
            }
        });

    </script>

    <script src="../../js/themetoggle.js"></script>
</body>
</html>