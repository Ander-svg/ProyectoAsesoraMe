<?php
// Obtén estos valores de la sección "Connections" en tu panel de Render
$host = "your_render_hostname";      // Por ejemplo: dpg-youruniquedbname-a.oregon-postgres.render.com
$port = "5432";                      // Confirma este valor en Render, es el puerto estándar de PostgreSQL
$dbname = "asesorame";               // El nombre de tu base de datos en Render
$user = "your_render_username";      // El nombre de usuario que te da Render
$password = "your_render_password";  // La contraseña que te da Render

// Cadena de conexión para PostgreSQL
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Crear la conexión
$conn = pg_connect($conn_string);

// Verificar la conexión
if (!$conn) {
    die("Conexión fallida: " . pg_last_error());
} else {
    // Opcional: Si la conexión es exitosa, puedes mostrar un mensaje
    // echo "Conexión exitosa a PostgreSQL en Render!";
}
?>