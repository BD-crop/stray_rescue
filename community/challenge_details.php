<?php
include_once __DIR__."/../PDO/PDO.php";
session_start();

$obj = PDO_class::initializer();

$challenge_id = $_GET['id'] ?? null;

if (!$challenge_id) {
    exit("Invalid challenge ID");
}

$data = $obj->challengeDetails($challenge_id, $_SESSION['id']);

$challenge_title = $data[0]['challenge_title'] ?? '';
$challenge_description = $data[0]['challenge_description'] ?? '';
$challenge_created_at = $data[0]['challenge_created_at'] ?? '';
$challenge_ends_at = $data[0]['challenge_ends_at'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Challenge Details</title>

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

    <a class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
       href="./community_challenges.php">
        Back
    </a>

    <button id="themeToggle"
        class="px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition">
        Theme
    </button>

</div>

<div class="max-w-4xl mx-auto px-4 py-6">

    <h1 class="text-3xl font-bold">
        <?= htmlspecialchars($challenge_title) ?>
    </h1>

    <p class="text-gray-600 dark:text-gray-300 mt-2">
        <?= htmlspecialchars($challenge_description) ?>
    </p>

    <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
        Created: <?= $challenge_created_at ?>
        <?php if (!empty($challenge_ends_at)): ?>
            | Ends: <?= $challenge_ends_at ?>
        <?php endif; ?>
    </div>

    <hr class="my-6 border-gray-300 dark:border-slate-700">

    <h2 class="text-xl font-semibold mb-4">Entries</h2>
</div>

<div class="max-w-4xl mx-auto px-4 mb-6">
    <a href="./upload_entry.php?id=<?= $challenge_id ?>"
       class="inline-block px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg">
        Add Entry
    </a>
</div>

<div id="entries" class="max-w-4xl mx-auto px-4 space-y-4"></div>

<script src="../js/themetoggle.js"></script>

<script>

const data = <?php echo json_encode($data); ?>;
const container = document.getElementById("entries");

function escapeHtml(text) {
    return (text || "")
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}

function render() {

    container.innerHTML = "";

    const entries = data.filter(x => x.id !== null);

    if (!entries.length) {
        container.innerHTML = "No entries yet";
        return;
    }

    entries.forEach(entry => {

        container.innerHTML += `
            <a href="./entry_details.php?id=${entry.id}">
            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-4">

                <p class="mb-3">
                    ${escapeHtml(entry.content)}
                </p>

                <div class="flex justify-between items-center">

                    <span>
                        ${entry.total_votes} votes
                    </span>

                    ${
                        entry.is_voted == 1
                        ? `<span>Voted</span>`
                        : `<span>Not voted</span>`
                    }

                </div>

            </div></a>
        `;
    });
}

render();
console.log(data);
</script>

</body>
</html>