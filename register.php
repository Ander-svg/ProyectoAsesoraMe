<?php
include('conexion.php');
session_start();

// Redirigir si ya está logueado
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error_message = '';
$success_message = '';

// Procesamiento del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $terms = isset($_POST['terms']);

    if (!$terms) {
        $error_message = "Debes aceptar los Términos y Condiciones.";
    } elseif ($password != $confirm_password) {
        $error_message = "Las contraseñas no coinciden.";
    } else {
        // Comprobar si el usuario o el correo ya existen
        $query = "SELECT * FROM Usuario WHERE correo = '$email' OR nombre = '$username' LIMIT 1";
        $result = pg_query($conn, $query);

        if (pg_num_rows($result) > 0) {
            $error_message = "El nombre de usuario o el correo ya están en uso.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $rol_id = 2; // O el que corresponda a tu rol de usuario
            $query = "INSERT INTO Usuario (nombre, correo, contrasena_hash, rol_id) VALUES ('$username', '$email', '$hashed_password', $rol_id)";
            if (pg_query($conn, $query)) {
                $success_message = "¡Registro exitoso! Redirigiendo al inicio de sesión...";
                echo "<script>
                    setTimeout(function(){ window.location.href = 'login.php?register=success'; }, 2000);
                </script>";
            } else {
                $error_message = "Error al registrar el usuario. Inténtalo nuevamente.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - AsesoraMe</title>
    <meta name="description" content="Regístrate para obtener una cuenta en AsesoraMe.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #111827;
            color: #d1d5db;
        }
        .form-container-bg {
            background: linear-gradient(rgba(17, 24, 39, 0.8), rgba(17, 24, 39, 0.8)), url('https://placehold.co/1920x1080/4f46e5/111827?text=.') no-repeat center center;
            background-size: cover;
        }
    </style>
</head>
<body class="form-container-bg flex flex-col min-h-screen">

    <?php include 'header.php'; ?>

    <main class="flex-1 flex items-center justify-center py-12">
    <div class="w-full max-w-md p-8 space-y-8 bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-2xl shadow-indigo-900/20">
        <div class="text-center">
            <a href="index.php" class="text-3xl font-bold text-white">AsesoraMe</a>
            <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">Crea una nueva cuenta</h2>
            <p class="mt-2 text-sm text-gray-400">
                ¿Ya tienes una?
                <a href="login.php" class="font-medium text-indigo-400 hover:text-indigo-300">
                    Inicia sesión aquí
                </a>
            </p>
        </div>

        <?php if ($error_message): ?>
            <div class="bg-red-900/50 border border-red-400/30 text-red-300 px-4 py-3 rounded-lg relative text-center mb-2" role="alert">
                <span class="block sm:inline"><?= $error_message ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="bg-green-900/50 border border-green-400/30 text-green-300 px-4 py-3 rounded-lg relative text-center flex items-center justify-center gap-2 mb-2" role="alert">
                <i class="ph ph-check-circle text-green-400 text-2xl"></i>
                <span><?= $success_message ?></span>
                <span class="ml-2 animate-spin inline-block">
                    <i class="ph ph-spinner text-green-300 text-2xl"></i>
                </span>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="" method="POST" autocomplete="off">
            <div>
                <label for="username" class="sr-only">Nombre de usuario</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="ph ph-user text-gray-400"></i>
                    </div>
                    <input id="username" name="username" type="text" autocomplete="username" required
                           class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-10 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                           placeholder="Nombre de usuario" value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                </div>
            </div>
            <div>
                <label for="email" class="sr-only">Correo electrónico</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="ph ph-envelope text-gray-400"></i>
                    </div>
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-10 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                           placeholder="Correo electrónico" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
            </div>
            <div>
                <label for="password" class="sr-only">Contraseña</label>
                <div class="relative">
                     <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="ph ph-lock text-gray-400"></i>
                    </div>
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-10 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                           placeholder="Contraseña">
                </div>
            </div>
            <div>
                <label for="confirm_password" class="sr-only">Confirmar Contraseña</label>
                <div class="relative">
                     <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="ph ph-lock-key text-gray-400"></i>
                    </div>
                    <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required
                           class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-10 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                           placeholder="Confirmar Contraseña">
                </div>
            </div>

            <div class="flex items-start">
                <div class="flex h-5 items-center">
                  <input id="terms" name="terms" type="checkbox" class="h-4 w-4 rounded border-gray-500 bg-gray-700 text-indigo-600 focus:ring-indigo-500" required>
                </div>
                <div class="ml-3 text-sm">
                  <label for="terms" class="font-light text-gray-400">Acepto los <a class="font-medium text-indigo-400 hover:text-indigo-300" href="#">Términos y Condiciones</a></label>
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-3 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                    Crear Cuenta
                </button>
            </div>
        </form>
    </div>
    </main>
</body>
</html>