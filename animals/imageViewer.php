<?php
include_once __DIR__.'/../PDO/PDO.php';
$obj = PDO_class::initializer();

$rows = $obj->get_images(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Viewer</title>

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

<div class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md bg-dark/30 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

    <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
       href="./index.php">
        Go Back
    </a>

    <button id="themeToggle"
        class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg">
        Theme
    </button>

</div>

<div class="sticky top-16 z-40 bg-white/70 dark:bg-slate-900/70 backdrop-blur-md border-b border-gray-200 dark:border-slate-700">

    <div class="mx-auto max-w-6xl flex gap-3 p-4">

        <button value="1" class="filter-btn px-4 py-2 rounded-xl bg-gray-300 dark:bg-slate-700 hover:bg-indigo-500 hover:text-white transition">
            Adoption Listing
        </button>

        <button value="0" class="filter-btn px-4 py-2 rounded-xl bg-gray-300 dark:bg-slate-700 hover:bg-indigo-500 hover:text-white transition">
            Community Images
        </button>

    </div>
</div>

<div class="flex justify-center">
    <div id="gallery"
         class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4 p-6 w-full max-w-6xl">

        <?php foreach ($rows as $row): ?>
            <a href="<?= htmlspecialchars($row['url_id']) ?>"
               class="block break-inside-avoid rounded-xl overflow-hidden shadow-md hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 bg-white dark:bg-slate-800">

                <img
                    src="<?= htmlspecialchars($row['image_path']) ?>"
                    class="w-full object-cover"
                    loading="lazy"
                    alt="image"
                >

            </a>
        <?php endforeach; ?>

    </div>
</div>

<script src="../js/themetoggle.js"></script>

<script>
let page = 1;
let loading = false;
let type = null;

const gallery = document.getElementById("gallery");
const buttons = document.querySelectorAll(".filter-btn");


async function loadMore(reset = false) {
    if (loading) return;
    loading = true;

    if (reset) {
        gallery.innerHTML = "";
        page = 0;
    }

    const url = `get_images.php?page=${page}&type=${type ?? ""}`;

    const res = await fetch(url);
    const data = await res.json();

    data.forEach(row => {
        const a = document.createElement("a");
        a.href = row.url_id;
        a.className =
            "block break-inside-avoid rounded-xl overflow-hidden shadow-md hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 bg-white dark:bg-slate-800";

        a.innerHTML = `
            <img src="${row.image_path}"
                 class="w-full object-cover"
                 loading="lazy"
                 alt="image">
        `;

        gallery.appendChild(a);
    });

    page++;
    loading = false;
}


buttons.forEach(btn => {
    btn.addEventListener("click", () => {

        if (type === btn.value) {
            type = null;
        } else {
            type = btn.value;
        }

        gallery.innerHTML = "";
        page = 0;
        loadMore(true);
    });
});


window.addEventListener("scroll", () => {
    if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 300) {
        loadMore();
    }
});

</script>

</body>
</html>