<?php
    include_once __DIR__.'/../auth_all_Employee.php';

    $data = $obj->get_notifications($_SESSION['id']);
    $data = json_encode($data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Notification</title>

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

    <div class="sticky top-0 z-50 flex gap-3 items-center p-4 backdrop-blur-md bg-white/70 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

        <a
            class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
            href="../index.php">
            Go Back
        </a>

        <button id="themeToggle"
            class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg">
            Theme
        </button>

    </div>

    <div class="max-w-5xl mx-auto p-4 sm:p-6">

        <div class="mb-6">
            <h1 class="text-3xl font-bold">
                Employee Notifications
            </h1>

            <p class="text-gray-600 dark:text-gray-400 mt-1">
                Recent updates and actions related to your account.
            </p>
        </div>

        <div id="notificationContainer" class="space-y-4">
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-700 p-6">
                Loading notifications...
            </div>
        </div>

    </div>

<script>

const data = <?= $data ?>;
console.log(data);
const container = document.getElementById("notificationContainer");

function escapeHtml(text) {

    if(text === null || text === undefined){
        return "";
    }

    const div = document.createElement("div");
    div.innerText = text;
    return div.innerHTML;
}

function renderNotifications() {

    const notifications = data.notifications || [];

    if(notifications.length === 0){

        container.innerHTML = `
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-700 p-8 text-center">
                <h2 class="text-xl font-semibold mb-2">
                    No Notifications
                </h2>
                <p class="text-gray-500 dark:text-gray-400">
                    You currently have no notifications.
                </p>
            </div>
        `;

        return;
    }

    let html = "";

    notifications.forEach(notification => {

        html += `
            <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg border border-gray-200 dark:border-slate-700 p-5 hover:shadow-xl transition-all duration-300">

                <div class="flex justify-between items-start gap-4 mb-3">

                    <div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            Notification ID
                        </div>

                        <div class="font-mono text-xs break-all">
                            ${escapeHtml(notification.notification_id)}
                        </div>
                    </div>

                    <div class="text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        ${escapeHtml(notification.created_at)}
                    </div>

                </div>

                <div class="mb-4">
                    <p class="leading-relaxed text-base">
                        ${escapeHtml(notification.message)}
                    </p>
                </div>

                <div class="pt-3 border-t border-gray-200 dark:border-slate-700">

                    <div class="text-sm">
                        <span class="font-semibold">
                            Created By:
                        </span>

                        <span class="font-mono break-all">
                            ${escapeHtml(notification.created_by)}
                        </span>
                    </div>

                </div>

            </div>
        `;
    });

    container.innerHTML = html;
}

renderNotifications();

</script>

<script>
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

</body>
</html>