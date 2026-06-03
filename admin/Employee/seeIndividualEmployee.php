<?php
session_start();

include_once __DIR__ . "/../auth.php";
include_once __DIR__ . "/../../template/admin_check.php";

$obj = PDO_class::initializer();

if (!check_if_employee()) {
    $msg = urlencode("You need to be a Senior Employee or upper to access page");
    header("Location:http://localhost:80/dashboard/index.php?msg=$msg");
    exit();
}

$level = $obj->find_employee_level();

if ($level > 1 || $level < 0 ) {
    $msg = urlencode("You need to be a Senior Employee or upper to access page");
    header("Location:http://localhost:80/dashboard/index.php?msg=$msg");
    exit();
}

$emp = $obj->get_employee_info($_GET['id']);

if (!$emp) {
    die("Employee not found");
}

$targetRank = (int) $emp['emp_rank'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $level < $targetRank) {

    $empId = $_POST['emp_id'];
    $reason = $_POST['reason'];

    try {

        if (isset($_POST['salary']) && $_POST['salary'] !== '' && $_POST['salary'] != $emp['salary']) {
            $obj->change_employee_salary(
                $empId,
                $_POST['salary'],
                $_SESSION['id'],
                $reason ?? "Salary Change"
            );
        }

        if (isset($_POST['point']) && $_POST['point'] !== '' ) {

            $name = $_POST['point'];

            $obj->assign_at_a_point(
                $empId,
                7,
                $_SESSION['id'],
                $point_id,
                $reason ?? "Assigned at a point"
            );
        }

        if (isset($_POST['manager']) && $_POST['manager'] !== '') {
            $id = $obj->getEmployee_id($_POST['manager']);
            if ($id && $id !== $emp['immediate_supervisor_id']) {

                $obj->assign_employee_supervisor(
                    $empId,
                    $id,
                    $_SESSION['id'],
                    $reason ?? 'Supervisor Updated'
                );
            }
        }

        header("Location:" . $_SERVER['PHP_SELF'] . "?id=" . $empId);
        exit();

    } catch (Exception $e) {

        if ($obj->pdo->inTransaction()) {
            $obj->pdo->rollBack();
        }

        die("ERROR:" . $e->getMessage());
    }
}

$msg = json_encode($emp);
?>

<!DOCTYPE html>
<html>

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Employee Admin Panel</title>

<script src="https://cdn.tailwindcss.com"></script>

<script>
tailwind.config = {
    darkMode: 'class'
}
</script>

</head>

<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-800 text-gray-900 dark:text-white transition-all duration-500">

<div class="sticky top-0 z-50 flex items-center justify-between px-6 py-3 backdrop-blur-md bg-white/70 dark:bg-slate-900/60 border-b border-gray-200 dark:border-slate-700">

    <h1 class="font-semibold text-lg">
        Admin Control Panel
    </h1>

    <button id="themeToggle"
        class="px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black hover:scale-105 transition">
        Theme
    </button>

</div>

<div class="max-w-5xl mx-auto px-4 py-8">

<div id="content" class="space-y-6">Loading...</div>

</div>

<script>
const emp = <?php echo $msg; ?>;
const content = document.getElementById("content");

content.innerHTML = `
<div class="bg-white dark:bg-slate-900 rounded-2xl shadow border border-gray-100 dark:border-slate-700 p-6">

    <div class="flex items-center gap-4 mb-6">

        <img class="w-16 h-16 rounded-full object-cover border"
             src="${emp.emp_profile_picture_link}">

        <div>
            <h2 class="text-xl font-bold">${emp.emp_name}</h2>
            <p class="text-sm opacity-70">${emp.email}</p>
        </div>

    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">

        <div class="p-3 rounded-lg bg-gray-100 dark:bg-slate-800">
            <b>ID</b><br>${emp.emp_id}
        </div>

        <div class="p-3 rounded-lg bg-gray-100 dark:bg-slate-800">
            <b>Rank</b><br>${emp.emp_rank}
        </div>

        <div class="p-3 rounded-lg bg-gray-100 dark:bg-slate-800">
            <b>Salary</b><br>${emp.salary}
        </div>

        <div class="p-3 rounded-lg bg-gray-100 dark:bg-slate-800 col-span-2">
            <b>Supervisor</b><br>${emp.supervisor_id || ""}
        </div>

    </div>

</div>
    <?php if($emp_rank < $targetRank ): ?>

<form method="POST"
      class="bg-white dark:bg-slate-900 rounded-2xl shadow border border-gray-100 dark:border-slate-700 p-6 space-y-6">

    <input type="hidden" name="emp_id" value="${emp.emp_id}">

    
    <div>
        <h3 class="font-semibold mb-2">Salary</h3>
        <input type="number"
               name="salary"
               min="0"
               value="${emp.salary}"
               class="w-full p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">
    </div>

    <div>
        <h3 class="font-semibold mb-2">Manager</h3>
        <input type="text"
               name="manager"
               placeholder="Manager Email"
               class="w-full p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">
    </div>

    <div>
        <h3 class="font-semibold mb-2">Assign Point</h3>
        <input type="text"
               name="point"
               placeholder="Write a point name"
               class="w-full p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800">
    </div>

    <div>
        <h3 class="font-semibold mb-2">Reason</h3>
        <textarea name="reason"
                  class="w-full p-2 rounded-lg border border-gray-300 dark:border-slate-700 dark:bg-slate-800"></textarea>
    </div>

    <button type="submit"
            class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl transition">
        Submit Changes
    </button>

</form>
<?php endif; ?>

`;
</script>

<script src="../../js/themetoggle.js"></script>

</body>
</html>