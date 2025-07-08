<?php
// Suponiendo que el usuario está logueado
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];

    // Si se ha enviado el formulario para cambiar el rol
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_role = $_POST['role']; // Recibe el nuevo rol del formulario

        // Actualiza el rol en la base de datos
        $update_query = "UPDATE Usuario SET role = '$new_role' WHERE id = $user_id";
        mysqli_query($conn, $update_query);

        // Actualiza la sesión con el nuevo rol
        $_SESSION['role'] = $new_role;

        echo "Rol actualizado a: " . $new_role;
    }
}
?>

<form method="POST">
    <label for="role">Seleccione su rol</label>
    <select name="role">
        <option value="usuario" <?= ($role == 'usuario') ? 'selected' : ''; ?>>Usuario</option>
        <option value="asesor" <?= ($role == 'asesor') ? 'selected' : ''; ?>>Asesor</option>
    </select>
    <button type="submit">Cambiar Rol</button>
</form>
