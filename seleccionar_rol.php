<?php
session_start();
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role']; // Capturamos el rol seleccionado
    $user_id = $_SESSION['user_id']; // Usamos el ID del usuario que está en la sesión

    // Actualizamos el rol en la base de datos
    $query = "UPDATE usuario SET role = '$role' WHERE id = $user_id";
    mysqli_query($conn, $query); // Ejecuatamos la consulta

    // Guardamos el rol en la sesión
    $_SESSION['role'] = $role;

    // Redirigimos al dashboard correspondiente
    if ($role == 'Asesor') {
        header("Location: asesor_dashboard.php");
    } else {
        header("Location: usuario_dashboard.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Rol - AsesoraMe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 600px;
            margin-top: 100px;
        }
        .card {
            border-radius: 20px;
            border: none;
            padding: 20px;
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        .card-header {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            color: #2d3e50;
        }
        .btn-role {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
            font-size: 18px;
            text-align: center;
            transition: 0.3s;
        }
        .btn-role:hover {
            opacity: 0.8;
        }
        .btn-asero {
            background-color: #4CAF50;
            color: white;
        }
        .btn-user {
            background-color: #2196F3;
            color: white;
        }
        .btn-cancel {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="card">
            <div class="card-header">
                Seleccionar Rol
            </div>
            <div class="card-body">
                <p class="text-center" style="font-size: 18px;">¿Con qué rol te gustaría iniciar sesión?</p>
                
                <!-- Botón para Asesor -->
                <a href="asesor_dashboard.php" class="btn btn-role btn-asero">
                    <i class="fas fa-user-tie"></i> Asesor
                </a>
                
                <!-- Botón para Usuario -->
                <a href="usuario_dashboard.php" class="btn btn-role btn-user">
                    <i class="fas fa-user"></i> Usuario
                </a>

                <div class="text-center mt-4">
                    <!-- Botón de Cancelar que vuelve a la página anterior -->
                    <button class="btn btn-cancel" onclick="history.back();">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
