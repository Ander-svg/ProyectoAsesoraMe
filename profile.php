<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'Sin rol';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - AsesoraMe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body class="bg-gray-900 text-gray-200">
    <?php include 'header.php'; ?>

    <main class="flex items-center justify-center min-h-[70vh]">
        <div class="bg-gray-800 rounded-2xl shadow-lg p-8 max-w-md w-full">
            <div class="flex flex-col items-center">
                <img src="https://i.pravatar.cc/100?u=<?= htmlspecialchars($username) ?>" alt="Avatar" class="w-20 h-20 rounded-full border-4 border-indigo-500 mb-4">
                <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($username) ?></h2>
                <span class="bg-indigo-900/50 text-indigo-300 font-semibold px-3 py-1 rounded-full mb-2">
                    <?= htmlspecialchars(ucfirst($role)) ?>
                </span>
            </div>
            <div class="mt-6">
                <a href="logout.php" class="block w-full text-center py-2 rounded bg-red-600 hover:bg-red-700 text-white font-bold transition">Cerrar sesión</a>
            </div>
        </div>
    </main>
</body>
</html>