<?php
include_once __DIR__."/../PDO/PDO.php";
session_start();

$obj = PDO_class::initializer();
$challenge_id = $_GET['id'] ?? null;

if($obj->type_of_user() !== 'user'){
    header("Location: ./challenge_details.php?id=" . $challenge_id);
    exit("Only users can upload entries");

}


if (!$challenge_id) {
    exit("Invalid challenge ID");
}

if (isset($_POST['submit'])) {

    $user_id = $_SESSION['id'];
    $content = $_POST['content'] ?? '';

    if (!empty($content)) {

        $entry_id = $obj->upload_challenge_entry(
            $challenge_id,
            $user_id,
            $content
        );

        header("Location: ./challenge_details.php?id=" . $challenge_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Entry</title>

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

<div class="sticky top-0 z-50 flex gap-3 items-center p-4 border-b border-gray-200 dark:border-slate-700">

    <a class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white"
       href="./challenge_details.php?id=<?= $challenge_id ?>">
        Back
    </a>

    <button id="themeToggle"
        class="px-4 py-2 rounded-xl bg-black text-white dark:bg-gray-100 dark:text-black">
        Theme
    </button>

</div>

<div class="max-w-2xl mx-auto px-4 py-10">

    <h1 class="text-2xl font-bold mb-6">Create Entry</h1>

    <form method="POST" enctype="multipart/form-data"
          class="space-y-4 bg-white dark:bg-slate-800 p-6 rounded-xl border border-gray-200 dark:border-slate-700">

        <div>
            <label class="block mb-2">Content</label>
            <textarea name="content"
                      class="w-full p-3 rounded-lg border dark:border-slate-700 bg-transparent"
                      rows="5"
                      required></textarea>
        </div>

        <div>
            <label class="block mb-2">Images</label>

            <div id="imageContainer">

                <div class="image-input-group mb-2">
                    <input type="file" name="fileToUpload[]"
                           class="w-full p-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600"
                           required>
                </div>

            </div>

            <button type="button" id="addImageBtn"
                    class="mt-2 px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                + Add Image
            </button>
        </div>

        <button type="submit" name="submit"
                class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg">
            Submit Entry
        </button>

    </form>

</div>

<script>
const imageContainer = document.getElementById("imageContainer");
const addImageBtn = document.getElementById("addImageBtn");

addImageBtn.addEventListener("click", () => {

    const wrapper = document.createElement("div");
    wrapper.className = "image-input-group mb-2 flex gap-2";

    wrapper.innerHTML = `
        <input type="file" name="fileToUpload[]"
               class="w-full p-2 border rounded-lg dark:bg-slate-700 dark:border-slate-600">

        <button type="button"
                class="px-3 bg-red-500 text-white rounded-lg removeBtn">
            X
        </button>
    `;

    wrapper.querySelector(".removeBtn").addEventListener("click", () => {
        wrapper.remove();
    });

    imageContainer.appendChild(wrapper);
});
</script>

<script src="../js/themetoggle.js"></script>

</body>
</html>