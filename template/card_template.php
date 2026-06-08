<?php

function card_returner($path, $title, $description, $icon_url) {
    return "
    <a href='$path'
       class='block p-6 rounded-2xl bg-gray-300 dark:bg-slate-800 shadow-md hover:shadow-2xl hover:-translate-y-1 transition-all duration-300'>

        <img src='$icon_url' class='w-8 h-8 mb-4' alt=''>

        <h2 class='text-xl font-semibold text-gray-800 dark:text-white'>
            $title
        </h2>

        <p class='text-gray-600 dark:text-slate-300 mt-2'>
            $description
        </p>

    </a>";
}


?>