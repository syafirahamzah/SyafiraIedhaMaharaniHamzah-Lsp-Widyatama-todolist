<?php
// Simulasi penyimpanan sementara
session_start();

// Memastikan data tugas ada di sesi
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

// Menangani form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Menambah tugas baru
    if (!empty($_POST['task']) && empty($_POST['task_index'])) {
        $newTask = htmlspecialchars($_POST['task']);
        addTask($_SESSION['tasks'], $newTask);
    }
    // Mengubah status tugas
    elseif (isset($_POST['task_index']) && isset($_POST['status'])) {
        $taskIndex = $_POST['task_index'];
        $status = $_POST['status'];
        updateTaskStatus($_SESSION['tasks'], $taskIndex, $status);
    }
    // Menghapus tugas
    elseif (isset($_POST['delete_task_index'])) {
        $deleteTaskIndex = $_POST['delete_task_index'];
        deleteTask($_SESSION['tasks'], $deleteTaskIndex);
    }
    // Mengedit tugas
    elseif (isset($_POST['task_index']) && isset($_POST['task'])) {
        $taskIndex = $_POST['task_index'];
        $updatedTask = htmlspecialchars($_POST['task']);
        editTask($_SESSION['tasks'], $taskIndex, $updatedTask);
    }
}

// Fungsi untuk menambahkan tugas
function addTask(&$taskList, $task) {
    $taskList[] = ['task' => $task, 'status' => 'belum'];
}

// Fungsi untuk mengubah status tugas
function updateTaskStatus(&$taskList, $taskIndex, $status) {
    if (isset($taskList[$taskIndex])) {
        $taskList[$taskIndex]['status'] = $status;
    }
}

// Fungsi untuk menghapus tugas
function deleteTask(&$taskList, $taskIndex) {
    if (isset($taskList[$taskIndex])) {
        unset($taskList[$taskIndex]);
        $taskList = array_values($taskList); // Re-index array setelah penghapusan
    }
}

// Fungsi untuk mengedit tugas
function editTask(&$taskList, $taskIndex, $updatedTask) {
    if (isset($taskList[$taskIndex])) {
        $taskList[$taskIndex]['task'] = $updatedTask;
    }
}

// Fungsi untuk menampilkan daftar tugas
function showTasks($taskList) {
    if (empty($taskList)) {
        echo "<p class='text-center text-gray-500'>Tidak ada tugas.</p>";
    } else {
        echo "<ul class='space-y-3'>";
        foreach ($taskList as $index => $taskData) {
            $statusText = $taskData['status'] === 'selesai' ? '✔️ Selesai' : '❌ Belum';
            $checked = $taskData['status'] === 'selesai' ? 'checked' : '';
            echo "<li class='flex justify-between items-center bg-white shadow-md p-4 rounded-lg'>
                    <span class='text-lg'>{$taskData['task']} - <strong class='text-green-500'>{$statusText}</strong></span>
                    <div class='space-x-2'>
                        <!-- Checkbox untuk menandai tugas -->
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='checkbox_index' value='{$index}' />
                            <input type='checkbox' name='checkbox' {$checked} class='h-5 w-5' onclick='this.form.submit()'>
                        </form>
                        <!-- Formulir untuk menghapus tugas -->
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='delete_task_index' value='{$index}' />
                            <button type='submit' class='bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-400'>Hapus</button>
                        </form>
                        <!-- Formulir untuk mengedit tugas -->
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='task_index' value='{$index}' />
                            <button type='submit' class='bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-400'>Edit</button>
                        </form>
                    </div>
                  </li>";
        }
        echo "</ul>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi To-Do List</title>
    <!-- Menggunakan CDN Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">

    <h1 class="text-center text-4xl font-bold text-gray-700 mb-8">Aplikasi To Do List</h1>

    <!-- Formulir untuk menambah tugas -->
    <form method="POST" action="" class="flex justify-center mb-6">
        <input type="text" name="task" placeholder="Tulis tugas baru..." required class="p-3 w-2/3 sm:w-1/2 md:w-1/3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400">
        <button type="submit" class="ml-4 bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-400">Tambah Tugas</button>
    </form>

    <?php
        // Menampilkan formulir untuk mengedit tugas jika ada
        if (isset($_POST['task_index'])) {
            $taskIndex = $_POST['task_index'];
            $taskToEdit = $_SESSION['tasks'][$taskIndex]['task'];
            echo "<h2 class='text-2xl font-semibold text-gray-700 mb-4'>Edit Tugas:</h2>";
            echo "<form method='POST' action='' class='flex justify-center mb-6'>
                    <input type='text' name='task' value='{$taskToEdit}' required class='p-3 w-2/3 sm:w-1/2 md:w-1/3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400'>
                    <input type='hidden' name='task_index' value='{$taskIndex}' />
                    <button type='submit' class='ml-4 bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-400'>Update Tugas</button>
                  </form>";
        }
    ?>

    <h2 class="text-2xl font-semibold text-gray-700 mb-4">Daftar Tugas:</h2>
    <?php showTasks($_SESSION['tasks']); ?>

</body>
</html>
