<?php
include __DIR__. "/../../PDO/PDO.php";

 ini_set('display_errors', 1);
 ini_set('display_startup_errors', 1);
 error_reporting(E_ALL); 



$obj = PDO_class::initializer();
session_start();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: ../index.php");
    exit();
}
$role = $obj -> type_of_user();
$data = $obj->readAdoptionDetail($id ,$_SESSION['id']);

if (!$data) {
    echo "Animal not found";
    exit();
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

    header("Location: individualListing.php?id=$id");
    exit();
}

if (isset($_POST['apply_submit'])) {

    $text = trim($_POST['application_text'] ?? '');

    $currentStatus = $data['adoption_status'] ?? 'NO Application';

    if (
        !empty($_SESSION['id']) &&
        !empty($text) &&
        ($currentStatus === 'NO Application' || $currentStatus === 'Rejected')
    ) {
        $obj->Add_Adoption_Application($id, $_SESSION['id'], $text);

        header("Location: seeIndividualAnimal.php?id=$id");
        exit();
    }
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

        <a class="hidden sm:block px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition"
           href="../index.php">
            Go Back
        </a>

        <button id="themeToggle"
            class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg">
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
                <p><b>Adoption ID:</b> <?= htmlspecialchars($data['animal_id']) ?></p>
                <p><b>Shelter ID:</b> <?= htmlspecialchars($data['shelter_id']) ?></p>
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
                    <img src="<?= htmlspecialchars($img) ?>"
                         class="w-full h-48 object-cover rounded-xl border border-gray-200 dark:border-slate-700">
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

    </div>
    <?php if($role == 'user'):?>

    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6">

        <h2 class="text-xl font-semibold mb-4">Apply For Adoption</h2>

        <?php
            $canApply = ($data['adoption_status'] ?? 'NO Application') === 'NO Application'
                     || ($data['adoption_status'] ?? '') === 'Rejected';
        ?>

        <?php if ($canApply && $role == 'user'): ?>

            <form method="POST" class="space-y-4">

                <textarea
                    name="application_text"
                    class="w-full p-3 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800"
                    placeholder="Write your adoption message..."
                    required
                ></textarea>

                <button
                    type="submit"
                    name="apply_submit"
                    class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition"
                >
                    Submit Application
                </button>

            </form>

        <?php else: ?>

            <p class="text-gray-500">You already have an active application.</p>

        <?php endif; ?>

    </div>
        <?php endif;?>

    <?php if($role == 'user'):?>
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-6">

        <h2 class="text-xl font-semibold mb-4">Last Application Status</h2>

        <?php
            $status = $data['adoption_status'] ?? 'NO Application';

            $statusClass = match ($status) {
                'Pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                'Approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                'Rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                default => 'bg-gray-100 text-gray-800 dark:bg-slate-800 dark:text-gray-300'
            };
        ?>

        <div class="flex items-center gap-3">
            <span class="font-medium">Status:</span>

            <span class="px-4 py-2 rounded-xl <?= $statusClass ?>">
                <?= htmlspecialchars($status) ?>
            </span>
        </div>

    </div>
    <?php endif;?>
</div>

<script>
    console.log(<?= json_encode($data ,JSON_PRETTY_PRINT); ?>);
</script>

<script src="../../js/themetoggle.js"></script>

</body>
</html>