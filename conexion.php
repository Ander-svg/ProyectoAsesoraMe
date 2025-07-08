<?php
// Definir los valores de la sección "Connections" en tu panel de Render
$host = "dpg-d1mper7fte5s739j38dg-a"; // El hostname de Render
$port = "5432";  // El puerto por defecto para PostgreSQL en Render
$dbname = "asesoramedb_jimb"; // El nombre de tu base de datos en Render
$user = "guevara"; // El nombre de usuario que te da Render
$password = "N9C7MTdwtDEmlrSm6ayeFyGd16jIl1Rd"; // La contraseña que te da Render

// Cadena de conexión para PostgreSQL
$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Crear la conexión
$conn = pg_connect($conn_string);

// Verificar la conexión
if (!$conn) {
    // Si la conexión falla, muestra el error de PostgreSQL
    die("Conexión fallida a PostgreSQL: " . pg_last_error());
} else {
    // Esto es solo para depuración. Puedes borrarlo una vez que la conexión funcione.
    // echo "Conexión exitosa a PostgreSQL en Render!";
}
?>
