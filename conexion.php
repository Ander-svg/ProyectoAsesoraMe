<?php
// Estos valores son los que obtendrá tu aplicación de las variables de entorno de Render.
// Si por alguna razón la variable de entorno no está configurada, usará el valor de fallback.
// Asegúrate de que las variables de entorno en Render estén configuradas con estos mismos valores.

// Variables de entorno para MySQL/MariaDB
// Los nombres son los mismos, pero asegúrate de que sus valores en Render apunten a tu DB MySQL/MariaDB
$host = getenv('DB_HOST') ?: 'localhost'; // O la IP/hostname de tu servidor MySQL/MariaDB
$port = getenv('DB_PORT') ?: '3306';     // Puerto estándar de MySQL/MariaDB
$dbname = getenv('DB_NAME') ?: 'asesorame'; // Nombre de tu base de datos MySQL/MariaDB
$user = getenv('DB_USER') ?: 'root';      // Usuario de tu base de datos MySQL/MariaDB
$password = getenv('DB_PASSWORD') ?: ''; // Contraseña de tu base de datos MySQL/MariaDB

// Crear conexión con MySQLi
// Usamos mysqli_connect para conectar a MySQL/MariaDB
$conn = mysqli_connect($host, $user, $password, $dbname, $port);

// Verificar la conexión
if (!$conn) {
    // Si la conexión falla, muestra el error de MySQLi
    die("Conexión fallida a MySQL/MariaDB: " . mysqli_connect_error());
} else {
    // Opcional: configurar el conjunto de caracteres a UTF-8 (recomendado)
    mysqli_set_charset($conn, "utf8mb4");

    // Esto es solo para depuración. Puedes borrarlo una vez que la conexión funcione.
    // echo "Conexión exitosa a MySQL/MariaDB en Render!";
}
?>