<?php
include_once __DIR__ . "/../auth_all_Employee.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $msg = urlencode("No rescue point ID found");
    header("Location: seeIndividualLocation.php?msg=$msg");
    exit;
}

$id = $_GET['id'];
$res = PDO_class::initializer()->get_point_by_id($id);

if (!$res) {
    $msg = urlencode("Rescue point not found");
    header("Location: seeIndividualLocation.php?msg=$msg");
    exit;
}

$images = [];

if (!empty($res['images'])) {
    $images = explode(';;;', $res['images']);
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Rescue Point Dashboard</title>

<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    darkMode: 'class'
}
</script>

</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white transition-colors duration-500">

<div class="sticky top-0 z-50 flex items-center justify-between px-6 py-3 backdrop-blur-md bg-white/70 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

    <a class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition" href="../index.php">
        Go Back
    </a>

    <button id="themeToggle"
        class="px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition">
        Theme
    </button>

</div>

<div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-6">

<div class="lg:col-span-2 space-y-6">

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow border border-gray-100 dark:border-slate-700">
        <h1 class="text-2xl font-bold mb-2">Rescue Point Details</h1>

        <p class="text-lg">
            Point Name:
            <span class="font-semibold">
                <?php echo htmlspecialchars($res['rescue_point_name']) ?>
            </span>
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-700">
            <p class="font-bold">ID</p>
            <p class="text-sm break-all"><?php echo htmlspecialchars($res['rescue_point_id']) ?></p>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-700">
            <p class="font-bold">Supervisor ID</p>
            <p class="text-sm break-all"><?php echo htmlspecialchars($res['supervisor_id']) ?></p>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-700">
            <p class="font-bold">Latitude</p>
            <p><?php echo htmlspecialchars($res['rescue_point_location_latitude']) ?></p>
        </div>

        <div class="p-4 rounded-xl bg-white dark:bg-slate-900 border border-gray-100 dark:border-slate-700">
            <p class="font-bold">Longitude</p>
            <p><?php echo htmlspecialchars($res['rescue_point_location_longtitude']) ?></p>
        </div>

    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow border border-gray-100 dark:border-slate-700">

        <h2 class="text-xl font-bold mb-4">Supervisor</h2>

        <div class="flex items-center gap-4">

            <img class="w-14 h-14 rounded-full object-cover border"
                 src="<?php echo $res['supervisor_image']; ?>" />

            <div>
                <p class="font-semibold">
                    <?php echo htmlspecialchars($res['supervisor_name']); ?>
                </p>
                <p class="text-sm opacity-70">
                    <?php echo htmlspecialchars($res['supervisor_email']); ?>
                </p>
            </div>

        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow border border-gray-100 dark:border-slate-700">

        <h2 class="text-xl font-bold mb-4">Shelter Images</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

            <?php if (!empty($images)): ?>

                <?php foreach ($images as $img): ?>
                    <form action="./rescuePointImageDelete.php" method="POST" class="space-y-2">
                        <img class="rounded-lg w-full h-48 object-cover"
                             src="<?php echo htmlspecialchars($img); ?>">

                        <input type="hidden" name="name" value="<?php echo htmlspecialchars($img); ?>">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">

                        <input type="submit"
                               name="submit"
                               value="DELETE"
                               class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg">
                    </form>
                <?php endforeach; ?>

            <?php else: ?>
                <p class="opacity-70">No images available</p>
            <?php endif; ?>

        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow border border-gray-100 dark:border-slate-700">

        <h2 class="text-xl font-bold mb-4">⬆ Upload Images</h2>

        <form action="./rescuePointImageUpload.php"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-3">

            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <input type="file"
                   name="fileToUpload"
                   class="w-full p-2 border rounded-lg dark:bg-slate-800 dark:border-slate-700">

            <input type="submit"
                   name="submit"
                   value="Upload"
                   class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg">

        </form>

    </div>

</div>

<div class="space-y-6">

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow border border-gray-100 dark:border-slate-700">

        <h2 class="text-xl font-bold mb-4">Assigned Employees</h2>

        <?php
        if (!empty($res['EMP_INFO'])) {

            $emps = explode(';;;', $res['EMP_INFO']);

            for ($i = 0; $i < count($emps); $i += 3) {

                $name = $emps[$i] ?? '';
                $email = $emps[$i + 1] ?? '';
                $uuid = $emps[$i + 2] ?? '';

                $avatar = strtoupper(substr($name, 0, 1));

                echo "
                <div class='flex items-center gap-3 p-3 mb-2 rounded-lg border dark:border-slate-700'>

                    <div class='w-10 h-10 flex items-center justify-center rounded-full bg-gray-200 dark:bg-slate-700 font-bold'>
                        {$avatar}
                    </div>

                    <div>
                        <p class='font-semibold'>" . htmlspecialchars($name) . "</p>
                        <p class='text-sm opacity-70'>" . htmlspecialchars($email) . "</p>
                        <p class='text-xs opacity-50'>" . htmlspecialchars($uuid) . "</p>
                    </div>

                </div>
                ";
            }

        } else {
            echo "<p class='opacity-70'>No employees assigned</p>";
        }
        ?>

    </div>

    <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl shadow border border-gray-100 dark:border-slate-700">

        <h2 class="text-xl font-bold mb-4">Quick Image View</h2>

        <div class="grid grid-cols-2 gap-3">

            <?php if (!empty($images)): ?>
                <?php foreach ($images as $img): ?>
                    <img class="rounded-lg h-28 w-full object-cover"
                         src="<?php echo htmlspecialchars($img); ?>">
                <?php endforeach; ?>
            <?php else: ?>
                <p class="opacity-70">No images</p>
            <?php endif; ?>

        </div>

    </div>

</div>

</div>

<script>
const btn = document.getElementById("themeToggle");

function applyTheme(theme) {
    document.documentElement.classList.toggle("dark", theme === "dark");
}

function getTheme() {
    return localStorage.getItem("theme") || "light";
}

applyTheme(getTheme());

btn.onclick = () => {
    const newTheme = getTheme() === "light" ? "dark" : "light";
    localStorage.setItem("theme", newTheme);
    applyTheme(newTheme);
};
</script>

</body>
</html>