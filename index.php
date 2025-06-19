<?php
session_start();
if (!isset($_SESSION['tasks'])) $_SESSION['tasks'] = [];

$editIndex = isset($_GET['edit']) ? (int) $_GET['edit'] : null;

// Handler: Update task
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_index'])) {
        $_SESSION['tasks'][$_POST['update_index']]['task'] = htmlspecialchars($_POST['task']);
        header('Location: index.php'); exit;
    }

    // Tambah tugas
    if (!empty($_POST['task']) && !isset($_POST['update_index'])) {
        $_SESSION['tasks'][] = ['task' => htmlspecialchars($_POST['task']), 'status' => 'belum'];
        header('Location: index.php'); exit;
    }

    // Hapus
    if (isset($_POST['delete_index'])) {
        unset($_SESSION['tasks'][$_POST['delete_index']]);
        $_SESSION['tasks'] = array_values($_SESSION['tasks']);
        header('Location: index.php'); exit;
    }

    // Centang
    if (isset($_POST['check_index'])) {
        $i = $_POST['check_index'];
        $_SESSION['tasks'][$i]['status'] = isset($_POST['checkbox']) ? 'selesai' : 'belum';
        header('Location: index.php'); exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>To-Do Fix</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 py-10">
    <div class="max-w-xl mx-auto bg-white p-6 rounded-xl shadow-xl">
        <h1 class="text-3xl font-bold text-center mb-6 text-green-700">📝 To-Do List</h1>

        <?php if ($editIndex === null): ?>
        <!-- Tambah -->
        <form method="POST" class="flex gap-2 mb-6">
            <input name="task" placeholder="Tugas baru..." required
                class="flex-1 px-4 py-2 border rounded-lg">
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg">Tambah</button>
        </form>
        <?php else: ?>
        <!-- Edit -->
        <form method="POST" class="flex gap-2 mb-6">
            <input type="hidden" name="update_index" value="<?= $editIndex ?>">
            <input name="task" value="<?= htmlspecialchars($_SESSION['tasks'][$editIndex]['task']) ?>" required
                class="flex-1 px-4 py-2 border rounded-lg">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Update</button>
            <a href="index.php" class="bg-gray-300 px-4 py-2 rounded-lg">Batal</a>
        </form>
        <?php endif; ?>

        <ul class="space-y-4">
            <?php foreach ($_SESSION['tasks'] as $i => $t): ?>
            <li class="bg-gray-50 p-4 rounded-lg shadow flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <form method="POST">
                        <input type="hidden" name="check_index" value="<?= $i ?>">
                        <input type="checkbox" name="checkbox" onchange="this.form.submit()" <?= $t['status'] === 'selesai' ? 'checked' : '' ?>>
                    </form>
                    <span class="text-lg"><?= htmlspecialchars($t['task']) ?></span>
                    <span class="text-sm <?= $t['status'] === 'selesai' ? 'text-green-600' : 'text-red-600' ?>">
                        (<?= $t['status'] ?>)
                    </span>
                </div>
                <div class="flex gap-2">
                    <a href="?edit=<?= $i ?>" class="px-3 py-1 bg-yellow-400 text-white rounded-lg">Edit</a>
                    <form method="POST">
                        <input type="hidden" name="delete_index" value="<?= $i ?>">
                        <button class="px-3 py-1 bg-red-500 text-white rounded-lg">Hapus</button>
                    </form>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</body>
</html>
