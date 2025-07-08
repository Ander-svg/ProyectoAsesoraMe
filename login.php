<?php
// Incluir el archivo de conexión
include('conexion.php');  // Asegúrate de que la ruta sea correcta

// Iniciar sesión
session_start();

// Comprobar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger los datos del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Consulta SQL para obtener el usuario y su rol desde la tabla usuarioRol
    $query = "SELECT u.*, r.role_name 
              FROM usuario u
              LEFT JOIN usuarioRol ur ON u.id = ur.usuario_id
              LEFT JOIN roles r ON ur.rol_id = r.id
              WHERE u.correo = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    // Verificar si el usuario existe
    if ($user) {
        // Verificar si la cuenta está bloqueada
        if ($user['bloqueado'] == 1) {
            $error_message = "Tu cuenta está bloqueada. Por favor, contacta al administrador.";
        } else {
            // Verificar si la contraseña es correcta
            if (password_verify($password, $user['contrasena_hash'])) {
                // Iniciar sesión y guardar los datos del usuario
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['nombre'];
                $_SESSION['role'] = $user['role_name']; // Guardamos el rol de la base de datos

                // Resetear intentos fallidos a 0 cuando el login es correcto
                $update_query = "UPDATE usuario SET intentos_fallidos = 0 WHERE correo = '$username'";
                mysqli_query($conn, $update_query);

                // Redirigir al usuario a la página de selección de rol
                if (empty($_SESSION['role'])) {
                    header("Location: seleccionar_rol.php"); // Redirige a la página de selección de rol
                } else {
                    header("Location: dashboard.php"); // Redirige al dashboard según el rol
                }
                exit();
            } else {
                // Si la contraseña es incorrecta, aumentar el contador de intentos fallidos
                $update_query = "UPDATE usuario SET intentos_fallidos = intentos_fallidos + 1 WHERE correo = '$username'";
                mysqli_query($conn, $update_query);

                // Comprobar si los intentos fallidos llegaron a 3
                $user_check = mysqli_query($conn, "SELECT intentos_fallidos FROM usuario WHERE correo = '$username'");
                $user_data = mysqli_fetch_assoc($user_check);
                if ($user_data['intentos_fallidos'] >= 3) {
                    // Bloquear la cuenta si tiene 3 intentos fallidos
                    mysqli_query($conn, "UPDATE usuario SET bloqueado = 1 WHERE correo = '$username'");
                    $error_message = "Has excedido el número de intentos. Tu cuenta ha sido bloqueada.";
                } else {
                    $error_message = "Nombre de usuario o contraseña incorrectos.";
                }
            }
        }
    } else {
        $error_message = "Nombre de usuario o contraseña incorrectos.";
    }
}
?>




<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Index - Mentor Bootstrap Template</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
                                                                                                        
  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Mentor
  * Template URL: https://bootstrapmade.com/mentor-free-education-bootstrap-theme/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.png" alt=""> -->
        <h1 class="sitename">AsesoraMe</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.php" class="active">Inicio<br></a></li> <!-- End of dropdown menu -->
          <li><a href="about.html">¿Quiénes somos?</a></li> <!-- End of dropdown menu -->
          <li><a href="courses.html">Cursos</a></li> <!-- End of dropdown menu -->
          <li><a href="contact.html">Soporte</a></li> <!-- End of dropdown menu -->
        </ul> <!-- End of dropdown menu items -->
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

      <a class="btn-getstarted" href="login.php">Iniciar Sesion</a>

     <!-- Eliminar el enlace de "Iniciar sesión" en el header -->
    </div>
  </header>

  <!-- Formulario de Login -->
<div class="container py-5">
    <h2 class="text-center">Iniciar sesión</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger text-center">
            <?= $error_message ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mx-auto" style="max-width: 400px;">
        <div class="mb-3">
            <label for="username" class="form-label">Nombre de usuario</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
    </form>

    <p class="text-center mt-3">
        ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
    </p>

    <!-- Mostrar nombre de usuario y rol después de iniciar sesión -->
    <?php if (isset($_SESSION['username'])): ?>
        <div class="alert alert-success mt-4">
            <p>Bienvenido, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</p>
        </div>
    <?php endif; ?>

    <!-- Mostrar nombre de usuario y rol después de iniciar sesión -->
    <?php if (isset($_SESSION['username'])): ?>
        <div class="alert alert-success mt-4">
            <p>Bienvenido, <?= $_SESSION['username']; ?> (<?= $_SESSION['role']; ?>)</p>
        </div>
    <?php endif; ?>
</div>


  <!-- Scripts de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>