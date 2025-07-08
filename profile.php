<?php
include('conexion.php');
session_start();
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_role = $_POST['role'];

        // Busca el id del nuevo rol
        $result = mysqli_query($conn, "SELECT id FROM roles WHERE nombre = '$new_role' LIMIT 1");
        $row = mysqli_fetch_assoc($result);
        $rol_id = $row['id'];

        // Actualiza el rol en la base de datos
        $update_query = "UPDATE usuario SET rol_id = $rol_id WHERE id = $user_id";
        mysqli_query($conn, $update_query);

        // Actualiza la sesión
        $_SESSION['role'] = $new_role;

        echo "Rol actualizado a: " . $new_role;
    }
}
?>
<form method="POST">
    <label for="role">Seleccione su rol</label>
    <select name="role">
        <option value="Aprendiz" <?= ($role == 'Aprendiz') ? 'selected' : ''; ?>>Aprendiz</option>
        <option value="Asesor" <?= ($role == 'Asesor') ? 'selected' : ''; ?>>Asesor</option>
        <option value="Admin" <?= ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
    </select>
    <button type="submit">Cambiar Rol</button>
</form>