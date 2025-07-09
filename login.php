<?php
include('conexion.php');
session_start();

$error_message = '';
$success_message = '';
$show_loading = false;
$max_attempts = 3;
$lock_time = 60; // segundos

// Variables de control de intentos por sesión
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['lock_expires'])) {
    $_SESSION['lock_expires'] = 0;
}

// Si está bloqueado, mostrar pantalla de espera
if ($_SESSION['login_attempts'] > $max_attempts - 1) {
    $now = time();
    if ($_SESSION['lock_expires'] == 0) {
        $_SESSION['lock_expires'] = $now + $lock_time;
    }
    $remaining = $_SESSION['lock_expires'] - $now;
    if ($remaining > 0) {
        // Pantalla de espera
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Demasiados intentos - AsesoraMe</title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
            <script src="https://unpkg.com/@phosphor-icons/web"></script>
            <style>
                body { font-family: 'Inter', sans-serif; background-color: #111827; color: #d1d5db; }
            </style>
        </head>
        <body class="flex flex-col min-h-screen items-center justify-center bg-gray-900">
            <div class="bg-gray-800/90 p-10 rounded-xl shadow-lg flex flex-col items-center">
                <i class="ph ph-clock-countdown text-5xl text-yellow-400 mb-4 animate-pulse"></i>
                <h2 class="text-2xl font-bold text-white mb-2">Demasiados intentos fallidos</h2>
                <p class="mb-2 text-gray-300">Por motivos de seguridad, debes esperar <span id="timer"><?= $remaining ?></span> segundos antes de volver a intentarlo.</p>
                <p class="text-gray-400 text-sm">No cierres ni recargues esta página.</p>
            </div>
            <script>
                let tiempo = <?= $remaining ?>;
                let timer = document.getElementById('timer');
                let interval = setInterval(function() {
                    tiempo--;
                    timer.textContent = tiempo;
                    if (tiempo <= 0) {
                        clearInterval(interval);
                        window.location.href = "login.php?reset=1";
                    }
                }, 1000);
            </script>
        </body>
        </html>
        <?php
        exit();
    } else {
        // Reiniciar intentos después del tiempo de bloqueo
        $_SESSION['login_attempts'] = 0;
        $_SESSION['lock_expires'] = 0;
        if (isset($_GET['reset'])) {
            $success_message = "Ya puedes volver a intentar iniciar sesión.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT u.*, r.nombre AS role_name 
              FROM usuario u
              LEFT JOIN roles r ON u.rol_id = r.id
              WHERE u.correo = '$username' LIMIT 1";
    $result = pg_query($conn, $sql); // Cambiado a función de PostgreSQL
    $user = pg_fetch_assoc($result);

    if ($user) {
        if ($user['bloqueado'] == 1) {
            $error_message = "Tu cuenta está bloqueada. Por favor, contacta al administrador.";
        } else {
            if (password_verify($password, $user['contrasena_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['nombre'];
                $_SESSION['role'] = $user['role_name'];
                $_SESSION['login_attempts'] = 0;
                $_SESSION['lock_expires'] = 0;

                $success_message = "Inicio de sesión exitoso. Redirigiendo...";
                $show_loading = true;
                echo "<script>
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                </script>";
            } else {
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] > $max_attempts - 1) {
                    header("Location: login.php"); // Redirige para mostrar pantalla de espera
                    exit();
                } else {
                    $intentos_restantes = $max_attempts - $_SESSION['login_attempts'];
                    $error_message = "Correo o contraseña incorrectos. Te quedan <b>$intentos_restantes</b> intento(s).";
                }
            }
        }
    } else {
        $_SESSION['login_attempts']++;
        if ($_SESSION['login_attempts'] > $max_attempts - 1) {
            header("Location: login.php"); // Redirige para mostrar pantalla de espera
            exit();
        } else {
            $intentos_restantes = $max_attempts - $_SESSION['login_attempts'];
            $error_message = "Correo o contraseña incorrectos. Te quedan <b>$intentos_restantes</b> intento(s).";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - AsesoraMe</title>
    <meta name="description" content="Inicia sesión en tu cuenta de AsesoraMe.">
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
<body class="form-container-bg min-h-screen flex flex-col">
    <?php include 'header.php'; ?>

    <main class="flex-1 flex items-center justify-center">
        <div class="w-full max-w-md p-8 space-y-8 bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-2xl shadow-indigo-900/20 mt-8 mb-8">
            <div class="text-center">
                <a href="index.php" class="text-3xl font-bold text-white">AsesoraMe</a>
                <h2 class="mt-2 text-2xl font-bold tracking-tight text-white">Inicia sesión en tu cuenta</h2>
                <p class="mt-2 text-sm text-gray-400">
                    ¿Aún no tienes una?
                    <a href="register.php" class="font-medium text-indigo-400 hover:text-indigo-300">
                        Regístrate aquí
                    </a>
                </p>
            </div>

            <!-- Mostrar número de intentos restantes -->
            <?php
            if ($_SESSION['login_attempts'] > 0 && $_SESSION['login_attempts'] < $max_attempts) {
                $intentos_restantes = $max_attempts - $_SESSION['login_attempts'];
                echo '<div class="bg-yellow-900/40 border border-yellow-600/30 text-yellow-300 px-4 py-2 rounded-lg text-center mb-2">
                        Te quedan <b>' . $intentos_restantes . '</b> intento(s) para iniciar sesión.
                      </div>';
            }
            ?>

            <?php if ($error_message): ?>
                <div class="bg-red-900/50 border border-red-400/30 text-red-300 px-4 py-3 rounded-lg relative text-center" role="alert">
                    <span class="block sm:inline"><?= $error_message ?></span>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="bg-green-900/50 border border-green-400/30 text-green-300 px-4 py-3 rounded-lg relative text-center flex items-center justify-center gap-2" role="alert">
                    <i class="ph ph-check-circle text-green-400 text-2xl"></i>
                    <span><?= $success_message ?></span>
                    <?php if ($show_loading): ?>
                        <span class="ml-2 animate-spin inline-block">
                            <i class="ph ph-spinner text-green-300 text-2xl"></i>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form class="mt-8 space-y-6" action="" method="POST" id="loginForm" autocomplete="off">
                <input type="hidden" name="remember" value="true">
                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="username" class="sr-only">Correo electrónico</label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="ph ph-envelope text-gray-400"></i>
                            </div>
                            <input id="username" name="username" type="email" autocomplete="email" required 
                                   class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-10 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" 
                                   placeholder="Correo electrónico">
                        </div>
                    </div>
                    <div class="pt-4">
                        <label for="password" class="sr-only">Contraseña</label>
                        <div class="relative">
                             <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="ph ph-lock text-gray-400"></i>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required 
                                   class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-10 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" 
                                   placeholder="Contraseña">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 rounded border-gray-500 bg-gray-700 text-indigo-600 focus:ring-indigo-500">
                        <label for="remember-me" class="ml-2 block text-sm text-gray-300">Recuérdame</label>
                    </div>

                    <div class="text-sm">
                        <a href="#" class="font-medium text-indigo-400 hover:text-indigo-300">¿Olvidaste tu contraseña?</a>
                    </div>
                </div>

                <div>
                    <button type="submit" id="loginBtn"
                            class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-3 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                        Iniciar Sesión
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
    // Mostrar loading en el botón al enviar el formulario (solo frontend)
    document.getElementById('loginForm').addEventListener('submit', function() {
        var btn = document.getElementById('loginBtn');
        btn.innerHTML = '<span class="animate-spin inline-block mr-2"><i class="ph ph-spinner text-white text-xl"></i></span> Iniciando...';
        btn.disabled = true;
    });
    </script>
</body>
</html>