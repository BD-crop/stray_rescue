<?php
include __DIR__ . "/../../auth_all_Employee.php";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($level != 2) {
    header("Location: ../index.php");
    exit();
}

$obj = PDO_class::initializer();

$id = $_GET['animal_id'] ?? null;

if(isset($_POST['enlist'])){
    $obj->enlist($id);

}

if(isset($_POST['unlist'])){
    $obj->unlist($id);
}

if(isset($_POST['submit'])){

    if(!empty($_POST['age']) && $_POST['age'] >=0){
        $obj->UpdateAnimalAge($id , $_POST['age']);
    }

    if(!empty($_FILES['fileToUpload']['name'][0])){
        $obj->addAnimalImage($id);
    }

    if(!empty($_POST['property_type']) && !empty($_POST['animal_property']) ){
        $obj->addAnimalProperty(
            $id,
            $_POST['property_type'],
            $_POST['animal_property']
        );
    }

    header("Location: seeIndividualAnimal.php?animal_id=$id");
    exit();
}

if (!$id) {
    header("Location: ../index.php");
    exit();
}

$data = $obj->readAnimal($id);

if (!$data) {
    echo "Animal not found";
    exit();
}

$images = [];
if (!empty($data['images'])) {
    $images = explode(";;;", $data['images']);
}

$properties = [];
if (!empty($data['animal_property'])) {
    foreach (explode(";;;", $data['animal_property']) as $p) {
        $parts = explode("||", $p);
        $properties[] = [
            "type" => $parts[0] ?? "",
            "value" => $parts[1] ?? ""
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Details</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            darkMode: 'class'
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

<div class="max-w-5xl mx-auto p-6 space-y-8">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6">

        <h1 class="text-3xl font-bold mb-4">
            <?= htmlspecialchars($data['animal_name']) ?>
        </h1>

        <div class="grid md:grid-cols-2 gap-4 text-sm">

            <div class="space-y-2">
                <p><b>ID:</b> <?= htmlspecialchars($data['animal_id']) ?></p>
                <p><b>Rescue Point:</b> <?= htmlspecialchars($data['rescue_point_id']) ?></p>
                <p><b>Age:</b> <?= htmlspecialchars($data['animal_age']) ?></p>
                <p><b>Added:</b> <?= htmlspecialchars($data['added_at']) ?></p>
            </div>

            <div class="space-y-2">
                <p>
                    <b>Status:</b>
                    <?php
                        echo match((int)$data['health_status']) {
                            1 => "Normal",
                            2 => "Attention Needed",
                            3 => "Emergency",
                            default => "Unknown"
                        };
                    ?>
                </p>

                <p>
                    <b>Removed:</b>
                    <?= $data['is_removed'] ? "Yes" : "No" ?>
                </p>
            </div>

        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6">

        <h2 class="text-xl font-semibold mb-4">Images</h2>

        <?php if (empty($images)): ?>
            <p class="text-gray-500">No images available</p>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-4">
                <?php foreach ($images as $img): ?>
                    <img
                        src="<?= htmlspecialchars($img) ?>"
                        class="w-full h-48 object-cover rounded-xl border border-gray-200 dark:border-slate-700"
                    >
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6">

        <h2 class="text-xl font-semibold mb-4">Properties</h2>

        <?php if (empty($properties)): ?>
            <p class="text-gray-500">No properties available</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($properties as $p): ?>
                    <div class="p-3 rounded-xl border border-gray-200 dark:border-slate-700">
                        <b><?= htmlspecialchars($p['type']) ?>:</b>
                        <?= htmlspecialchars($p['value']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
<form method="POST" class="mt-4">

    <?php if($data['is_listed'] == 'not listed'): ?>
        <input
            type="submit"
            name="enlist"
            id="enlist"
            value="Enlist"
            class="px-5 py-2 rounded-xl bg-green-600 hover:bg-green-700 text-white transition"
        >
    <?php elseif($data['is_listed'] == 'listed'): ?>
        <input
            type="submit"
            name="unlist"
            id="unlist"
            value="Unlist"
            class="px-5 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white transition"
        >
    <?php endif; ?>

</form>

    
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6">

        <h2 class="text-xl font-semibold mb-4">Update Animal</h2>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">

            <input
                type="number"
                name="age"
                min="0"
                placeholder="Update age"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800"
            >

            <div id="imageContainer" class="space-y-3">
                <input type="file"
                       name="fileToUpload[]"
                       class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800">
            </div>

            <button type="button"
                    id="addImageBtn"
                    class="px-4 py-2 rounded-xl bg-gray-200 dark:bg-slate-700">
                + Add Another Image
            </button>

            <input
                type="text"
                name="property_type"
                placeholder="Property type"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800"
            >

            <input
                type="text"
                name="animal_property"
                placeholder="Animal property"
                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800"
            >

            <button type="submit"
                    name="submit"
                    class="w-full py-3 rounded-xl bg-blue-600 text-white">
                Update
            </button>

        </form>

    </div>

</div>

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

<script>
document.getElementById("addImageBtn").addEventListener("click", () => {

    const container = document.getElementById("imageContainer");

    const div = document.createElement("div");

    div.innerHTML = `
        <input type="file"
               name="fileToUpload[]"
               class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800">
    `;

    container.appendChild(div);
});

// console.log(<?= json_encode($data);?>);
</script>

</body>
</html>