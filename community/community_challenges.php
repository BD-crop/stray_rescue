<?php
include_once __DIR__."/../PDO/PDO.php";
session_start();    
$obj = PDO_class::initializer();

$data = $obj->getCommunityChallenges($_SESSION['id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Challenges</title>

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

<body class="bg-gray-100 dark:bg-slate-900 text-gray-900 dark:text-white transition">

    <div class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md border-b border-gray-200 dark:border-slate-700">

        <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
            href="../index.php">
            Home
        </a>

        <button id="themeToggle"
            class="px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition">
            Theme
        </button>

    </div>

    <div class="p-6 text-center">
        <h1 class="text-3xl font-bold">Community Challenges</h1>
        <p class="text-gray-600 dark:text-gray-300 mt-2">
            An initiative to engage the community in various challenges and activities.
        </p>
    </div>

    <div class="max-w-4xl mx-auto px-4 space-y-4">

        <?php foreach ($data as $challenge): ?>

            <div class="flex items-center justify-between bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-4 hover:bg-gray-50 dark:hover:bg-slate-700 transition">

                <div class="flex-1">

                    <h2 class="text-lg font-semibold">
                        <?= htmlspecialchars($challenge['title']) ?>
                    </h2>

                    <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-1">
                        <?= htmlspecialchars($challenge['description']) ?>
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Created: <?= $challenge['created_at'] ?>
                    </p>

                </div>

                <div class="flex items-center gap-3">

                    <?php if ($challenge['vote_left'] == 1): ?>
                        <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300">
                            Voted
                        </span>
                    <?php else: ?>
                        <span class="px-3 py-1 text-xs rounded-full bg-gray-200 text-gray-700 dark:bg-slate-700 dark:text-gray-300">
                            Not voted
                        </span>
                    <?php endif; ?>

                    <a href="./challenge_details.php?id=<?= $challenge['id'] ?>"
                        class="px-4 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white text-sm transition">
                        View
                    </a>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

    <script src="../js/themetoggle.js"></script>

</body>
</html>