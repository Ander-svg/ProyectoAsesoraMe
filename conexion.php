<?php
// Estos valores son los que obtendrá tu aplicación de las variables de entorno de Render.
// Si por alguna razón la variable de entorno no está configurada, usará el valor de fallback.
// Asegúrate de que las variables de entorno en Render estén configuradas con estos mismos valores.

$host = getenv('DB_HOST') ?: 'dpg-d1mper7fte5s739j38dg-a';
$port = getenv('DB_PORT') ?: '5432';
$dbname = getenv('DB_NAME') ?: 'asesoramedb_jimb';
$user = getenv('DB_USER') ?: 'guevara';
$password = getenv('DB_PASSWORD') ?: 'N9C7MTdwtDEmlrSm6ayeFyGd16jIl1Rd';

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