<?php
    include_once __DIR__."/../auth_all_Employee.php";



    $obj = PDO_class::initializer();

    if (isset($_POST['submit'])) {
        $title = $_POST['title'] ?? null;
        $description = $_POST['description'] ?? null;
        $ends_at = $_POST['ends_at'] ?? null;

        if (!empty($title)) {

            $result = $obj->createCommunityChallenge(
                $title,
                $description,
                $_SESSION['id'],
                $ends_at ?: null
            );

            if ($result) {
                header("Location: ./createChallenge.php?msg=success");
                exit();
            } else {
                $error = "Failed to create challenge";
            }
        } else {
            $error = "Title is required";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Challenge</title>

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

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white">

    <div class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md border-b border-gray-200 dark:border-slate-700">
        
        <a class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
           href="../index.php">
            Go Back
        </a>

        <button id="themeToggle"
            class="px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition">
            Theme
        </button>
    </div>

    <div class="flex justify-center items-center py-10 px-4">

        <div class="w-full max-w-xl bg-white dark:bg-slate-900 shadow-xl rounded-2xl p-6 border border-gray-200 dark:border-slate-700">

            <h1 class="flex items-center gap-2 text-2xl font-bold mb-6 text-center"><img  src="https://www.svgrepo.com/show/405161/crown.svg" alt="Challenge" class="w-8 h-8">Create Community Challenge</h1>

            <?php if (!empty($error)) : ?>
                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'success') : ?>
                <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200">
                    Challenge created successfully!
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block mb-1 text-sm font-medium">Title</label>
                    <input type="text" name="title" required placeholder="Challenge Title"
                        class="w-full px-4 py-2 rounded-lg border dark:border-slate-700 bg-transparent focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Description</label>
                    <textarea name="description" rows="4" placeholder="Challenge Description"
                        class="w-full px-4 py-2 rounded-lg border dark:border-slate-700 bg-transparent focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
                </div>

                <div>
                    <label class="block mb-1 text-sm font-medium">Ends At (optional)</label>
                    <input type="datetime-local" name="ends_at"
                        class="w-full px-4 py-2 rounded-lg border dark:border-slate-700 bg-transparent focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <button type="submit" name="submit"
                    class="w-full py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold transition">
                    Create Challenge
                </button>

            </form>
        </div>

    </div>

    <script src="../../js/themetoggle.js"></script>

</body>
</html>