<?php
include_once __DIR__ . "/../../PDO/PDO.php";

$obj = PDO_class::initializer();

$page = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$name = $_GET['name'] ?? "";
$rank_by = $_GET['rank_by'] ?? "post_time_stamp";

$res = $obj->adoptionListing($page,10, $name, $rank_by);

$res_json = json_encode($res);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rescue Posts</title>

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

<body class="bg-gray-100 dark:bg-slate-950 text-gray-900 dark:text-gray-100 min-h-screen transition-colors duration-300">

<div class="flex justify-between items-center px-6 py-4
            bg-white dark:bg-slate-900
            shadow-md sticky top-0 z-50
            border-b border-gray-200 dark:border-slate-800 transition">

    <a class="hidden sm:block px-4 py-2 rounded-xl
              bg-blue-500 hover:bg-blue-600 text-white transition"
       href="../index.php">
        Home
    </a>

    <button id="themeToggle"
        class="px-4 py-2 rounded-xl font-medium
               bg-gray-900 text-white
               dark:bg-white dark:text-black
               hover:scale-105 transition">
        Theme
    </button>
</div>

<div class="max-w-6xl mx-auto px-4 py-10">

    <h1 class="text-4xl font-bold text-center mb-8
               text-gray-900 dark:text-white">
        Rescue Posts
    </h1>

    <form method="GET"
          class="flex flex-col md:flex-row justify-center items-center gap-4 mb-10">

        <input
            type="text"
            name="name"
            value="<?= htmlspecialchars($name) ?>"
            placeholder="Search animal name..."
            class="w-full md:w-80 px-4 py-2 rounded-xl
                   bg-white dark:bg-slate-900
                   border border-gray-300 dark:border-slate-700
                   text-gray-900 dark:text-gray-100
                   focus:ring-2 focus:ring-purple-500 outline-none"
        >

        <select
            name="rank_by"
            class="w-full md:w-48 px-4 py-2 rounded-xl
                   bg-white-900 dark:bg-slate-900
                   border border-gray-300 dark:border-slate-700
                   text-gray-900 dark:text-gray-100"
        >
            <option value="created_at" <?= $rank_by=='created_at'?'selected':'' ?>>Newest</option>
            <option value="health_status" <?= $rank_by=='health_status'?'selected':'' ?>>SOS Level</option>
            <option value="animal_age" <?= $rank_by=='animal_age'?'selected':'' ?>>Age</option>
        </select>

        <button
            type="submit"
            class="px-6 py-2 rounded-xl
                   bg-purple-600 hover:bg-purple-700
                   text-white font-medium transition shadow-md">
            Apply
        </button>
    </form>

    <div id="rescuePosts" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"></div>

    <div id="pagination" class="flex justify-center mt-10 gap-4"></div>

</div>

<script>

const container = document.getElementById("rescuePosts");
const pagination = document.getElementById("pagination");

const data = <?= $res_json ?>;

if (!data || !data.posts || data.posts.length === 0) {

    container.innerHTML = `
        <div class="col-span-full text-center text-gray-500 dark:text-gray-400 text-lg">
            No posts found
        </div>
    `;
} else {

    if (data.posts.length === 11) {
        data.posts.pop();
    }

    data.posts.forEach(post => {

        container.innerHTML += `
            <div class="rounded-2xl overflow-hidden shadow-md hover:shadow-xl transition
                        bg-white dark:bg-slate-900
                        border border-gray-200 dark:border-slate-800">

                <img src="${post.image_path}"
                     class="w-full h-52 object-cover">

                <div class="p-4 space-y-2">

                    <div class="flex justify-between items-center">



                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            Age ${post.animal_age}
                        </span>

                    </div>



                    <div class="text-xs border-t pt-2 space-y-1
                                text-gray-600 dark:text-gray-400
                                border-gray-200 dark:border-slate-800">

                        <div><b>Name:</b> ${post.animal_name}</div>
                        <div><b>SOS:</b> ${post.health_status}</div>
                        <div><b>Time:</b> ${post.created_at}</div>

                    </div>

                    <a href="./individualListing.php?id=${post.animal_id}"
                       class="block text-center mt-3 py-2 rounded-xl
                              bg-purple-600 hover:bg-purple-700
                              text-white transition">
                        View Details ->
                    </a>

                </div>
            </div>
        `;
    });

    let html = `<div class="flex items-center gap-4">`;

    if (data.is_left !== -1) {
        html += `
            <a href="?offset=${data.is_left}&name=<?= urlencode($name) ?>&rank_by=${data.rank_by}"
               class="px-4 py-2 rounded-lg
                      bg-gray-700 hover:bg-gray-800 text-white">
                Prev
            </a>
        `;
    }

    html += `<span class="font-semibold">Page ${data.page}</span>`;

    if (data.is_right !== -1) {
        html += `
            <a href="?offset=${data.is_right}&name=<?= urlencode($name) ?>&rank_by=${data.rank_by}"
               class="px-4 py-2 rounded-lg
                      bg-purple-600 hover:bg-purple-700 text-white">
                Next
            </a>
        `;
    }

    html += `</div>`;

    pagination.innerHTML = html;
}
console.log(<?= $res_json ;?>);
</script>

<script src="../../js/themetoggle.js"></script>


</body>
</html>