<?php
include_once __DIR__."/../PDO/PDO.php";

session_start();
$obj = PDO_class::initializer();

$entry_id = $_GET['id'] ?? null;

if (!$entry_id) {
    exit("Invalid entry ID");
}

$data = $obj->getIndividualEntry($entry_id, $_SESSION['id']);

if (!$data || !is_array($data)) {
    exit("Entry not found");
}

$images = !empty($data['images'])
    ? explode(';;;', $data['images'])
    : [];

$type = $obj->type_of_user();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entry Details</title>

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

    <div class="sticky top-0 z-50 flex justify-between items-center p-4 border-b border-gray-200 dark:border-slate-700 bg-white/70 dark:bg-slate-900/70 backdrop-blur">

        <a class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
           href="./challenge_details.php?id=<?php echo urlencode($data['challenge_id']) ;?>">
            Back
        </a>

        <button id="themeToggle"
            class="px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition">
            Theme
        </button>

    </div>

    <div class="max-w-4xl mx-auto px-4 py-8">

        <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden">

            <div class="p-6">

                <div class="flex justify-between items-center mb-4">

                    <h1 class="text-2xl font-bold">
                        Challenge Entry
                    </h1>

                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        <?= htmlspecialchars($data['created_at']) ?>
                    </span>

                </div>

                <p class="text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed">
                    <?= htmlspecialchars($data['content']) ?>
                </p>

            </div>

            <?php if (!empty($images) && !empty($images[0])): ?>

                <div class="px-6 pb-6">

                    <h2 class="font-semibold mb-3">
                        Images
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                        <?php foreach ($images as $img): ?>

                            <a href="<?= htmlspecialchars($img) ?>" target="_blank">

                                <img
                                    src="<?= htmlspecialchars($img) ?>"
                                    alt="Entry Image"
                                    class="w-full h-56 object-cover rounded-xl border border-gray-300 dark:border-slate-700 hover:scale-105 transition duration-300">

                            </a>

                        <?php endforeach; ?>

                    </div>

                </div>

            <?php endif; ?>

            <div class="border-t border-gray-200 dark:border-slate-700 p-6">

                <div class="flex justify-between items-center">

                    <div>

                        <?php if ($data['is_voted'] == 1): ?>

                            <span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 text-sm">
                                You have voted for this entry
                            </span>

                        <?php else: ?>

                            <span class="inline-block px-3 py-1 rounded-full bg-gray-200 text-gray-700 dark:bg-slate-700 dark:text-gray-300 text-sm">
                                You have not voted yet
                            </span>

                        <?php endif; ?>

                    </div>

                    <div>

                        <?php if ($data['is_voted'] == 0 && $type == 'user'): ?>

                            <form method="POST" action="./vote.php">

                                <input
                                    type="hidden"
                                    name="entry_id"
                                    value="<?= htmlspecialchars($data['id']) ?>">

                                <input
                                    type="hidden"
                                    name="challenge_id"
                                    value="<?= htmlspecialchars($data['challenge_id']) ?>">

                                <button
                                    type="submit"
                                    class="px-5 py-2 rounded-lg bg-blue-500 hover:bg-blue-600 text-white transition">

                                    Vote

                                </button>

                            </form>

                        <?php else: ?>
                            <form method="POST" action="./un_vote.php">

                                <input
                                    type="hidden"
                                    name="entry_id"
                                    value="<?= htmlspecialchars($data['id']) ?>">

                                <input
                                    type="hidden"
                                    name="challenge_id"
                                    value="<?= htmlspecialchars($data['challenge_id']) ?>">

                                <button
                                    type="submit"
                                    class="px-5 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white transition">

                                    Unvote

                                </button>

                            </form>


                        <?php endif; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="../js/themetoggle.js"></script>

</body>
</html>