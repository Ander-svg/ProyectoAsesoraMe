<?php
// Iniciar sesión
session_start();

// Verificar si el rol fue enviado
if (isset($_POST['role'])) {
    $_SESSION['role'] = $_POST['role']; // Guardamos el rol seleccionado en la sesión

    // Redirigir a la página correspondiente
    if ($_SESSION['role'] == 'Asesor') {
        header("Location: asesor_dashboard.php"); // Página del asesor
    } else {
        header("Location: usuario_dashboard.php"); // Página del usuario
    }
    exit();
} else {
    // Si no se seleccionó un rol, redirigir al login
    header("Location: login.php");
    exit();
}
