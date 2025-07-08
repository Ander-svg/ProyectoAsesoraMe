<?php
$servername = "localhost";
$username = "root";
$password = ""; // Cambia si tienes contraseña
$dbname = "asesorame"; // Nombre de tu base de datos

// Crear la conexión
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . mysqli_connect_error());
}
?>
