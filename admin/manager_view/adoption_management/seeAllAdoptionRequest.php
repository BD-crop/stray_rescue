<?php
    include_once __DIR__. '/../../../PDO/PDO.php';
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL); 



    $obj  = PDO_class::initializer();

    $role = $obj->type_of_user();
    $emp_level = $obj->find_employee_level();

    if($role != 'employee' || $emp_level != 2){
        header("Location: ../../../index.php");
        exit();
    }

    if(isset($_POST['update_status'])){
        if(!empty($_POST['adoption_application_id']) && !empty($_POST['status']) ){
            if($_POST['status'] == 'accepted' || $_POST['status'] == 'rejected' ){
                $obj->updateAdoptionRequest($_POST['adoption_application_id'] 
                    ,$_POST['status']);
            }

        }
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL); 

        // header("Location: seeAllAdoptionRequest.php");
        // exit();
    }

    $data = $obj->seeAdoptionRequests($_SESSION['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>See All Adoption Request</title>

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
            href="../index.php">
            Go Back
        </a>

        <button id="themeToggle"
            class="sm:block px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition-all duration-300 shadow-lg">
            Theme
        </button>

    </div>

    <div class="max-w-6xl mx-auto p-6">

        <h1 class="text-2xl font-bold mb-6">Adoption Requests</h1>

        <?php if (empty($data)): ?>
            <p class="text-gray-500">No adoption requests found.</p>
        <?php else: ?>

            <div class="grid gap-4">

                <?php foreach ($data as $row): ?>

                    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-lg p-5 space-y-2">

                        <div class="flex justify-between items-center">
                            <h2 class="text-xl font-semibold">
                                <?= htmlspecialchars($row['animal_name']) ?>
                            </h2>

                            <span class="px-3 py-1 rounded-xl text-sm
                                <?php
                                    echo match($row['adoption_application_status']) {
                                        'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                        'approved' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                        'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        default => 'bg-gray-100 text-gray-800 dark:bg-slate-800 dark:text-gray-300'
                                    };
                                ?>">
                                <?= htmlspecialchars($row['adoption_application_status']) ?>
                            </span>
                        </div>

                        <p class="text-sm">
                            <b>Animal ID:</b> <?= htmlspecialchars($row['animal_id']) ?>
                        </p>

                        <p class="text-sm">
                            <b>User ID:</b> <?= htmlspecialchars($row['user_id']) ?>
                        </p>

                        <p class="text-sm">
                            <b>Application Text:</b> <?= htmlspecialchars($row['adoption_Application_text']) ?>
                        </p>

                        <p class="text-sm">
                            <b>Application ID:</b> <?= htmlspecialchars($row['adoption_application_id']) ?>
                        </p>

                        <p class="text-sm">
                            <b>Created At:</b> <?= htmlspecialchars($row['created_at']) ?>
                        </p>

                        <div class="pt-2 border-t border-gray-200 dark:border-slate-700 text-sm space-y-1">

                            <p>
                                <b>Rescue Point ID:</b> <?= htmlspecialchars($row['rescue_point_id']) ?>
                            </p>

                            <p>
                                <b>Rescue Point Name:</b> <?= htmlspecialchars($row['rescue_point_name']) ?>
                            </p>


                            <p>
                                <b>Supervisor ID:</b> <?= htmlspecialchars($row['supervisor_id']) ?>
                            </p>

                            <p>
                                <b>Animal Name:</b> <?= htmlspecialchars($row['animal_name']) ?>
                            </p>

                            <p>
                                <b>Age:</b> <?= htmlspecialchars($row['animal_age']) ?>
                            </p>

                            <p>
                                <b>Health Status:</b> <?= htmlspecialchars($row['health_status']) ?>
                            </p>

                        </div>
                        <form method="POST" action="seeAllAdoptionRequest.php" class="pt-4 flex gap-2 items-center">
                            <input type="hidden" name="adoption_application_id"
                                   value="<?= htmlspecialchars($row['adoption_application_id']) ?>">

                            <select name="status"
                                    class="px-3 py-2 rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800">
                                <option value="pending" <?= $row['adoption_application_status']=='pending'?'selected':''; ?>>Pending</option>
                                <option value="accepted" <?= $row['adoption_application_status']=='accepted'?'selected':''; ?>>Approved</option>
                                <option value="rejected" <?= $row['adoption_application_status']=='rejected'?'selected':''; ?>>Rejected</option>
                            </select>

                            <button type="submit" name="update_status"
                                    class="px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition">
                                Update
                            </button>
                        </form>

                    </div>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>

    <script>
        console.log(<?= json_encode($data); ?>);
    </script>

    <script src="../../../js/themetoggle.js"></script>

</body>
</html>