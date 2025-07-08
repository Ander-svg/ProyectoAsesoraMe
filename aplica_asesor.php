<?php
include('conexion.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$error_message = '';
$success_message = '';

// --- 1. Cargar países y ciudades para los selects ---
$paises = [];
$ciudades = [];
$sql = "SELECT id, nombre FROM pais ORDER BY nombre";
$res = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($res)) {
    $paises[] = $row;
}
$sql = "SELECT id, nombre, pais_id FROM ciudad ORDER BY nombre";
$res = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($res)) {
    $ciudades[] = $row;
}

// --- 2. Procesar el formulario ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuarioId = $_SESSION['user_id'];
    $pais_id = intval($_POST['country']);
    $ciudad_id = intval($_POST['city']);
    $especialidades = trim($_POST['specialties']);
    $biografia = trim($_POST['biography']);
    $experiencia = trim($_POST['experience']);
    $tarifa = floatval($_POST['hourly_rate']);

    // Verifica si ya existe un registro para este usuario
    $check = mysqli_query($conn, "SELECT id FROM asesor WHERE usuarioId = $usuarioId");
    if (mysqli_num_rows($check) > 0) {
        $error_message = "Ya tienes una solicitud registrada como asesor.";
    } else {
        $sql = "INSERT INTO asesor (usuarioId, pais_id, ciudad_id, especialidades, experiencia, biografia, calificacionPromedio, tarifa_por_hora)
                VALUES ($usuarioId, $pais_id, $ciudad_id,
                    '" . mysqli_real_escape_string($conn, $especialidades) . "',
                    '" . mysqli_real_escape_string($conn, $experiencia) . "',
                    '" . mysqli_real_escape_string($conn, $biografia) . "',
                    0.00,
                    $tarifa
                )";
        if (mysqli_query($conn, $sql)) {
            // Cambia el rol del usuario a 2 (asesor)
            mysqli_query($conn, "UPDATE usuario SET rol_id = 1 WHERE id = $usuarioId");
            $_SESSION['role'] = "Asesor";
            $success_message = "¡Solicitud enviada correctamente! Ahora eres asesor.";
        } else {
            $error_message = "Error al enviar la solicitud: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conviértete en Asesor - AsesoraMe</title>
    <meta name="description" content="Aplica para convertirte en asesor en la plataforma AsesoraMe.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #111827; color: #d1d5db; }
        .form-container-bg {
            background: linear-gradient(rgba(17, 24, 39, 0.8), rgba(17, 24, 39, 0.8)), url('https://placehold.co/1920x1080/4f46e5/111827?text=.') no-repeat center center;
            background-size: cover;
        }
        select, option { color: #111827; }
    </style>
</head>
<body class="form-container-bg flex flex-col min-h-screen">
<?php include 'header.php'; ?>

<main class="flex-1 flex items-center justify-center py-12">
    <div class="w-full max-w-2xl p-8 space-y-8 bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-2xl shadow-indigo-900/20">
        <div class="text-center">
            <a href="index.php" class="text-3xl font-bold text-white">AsesoraMe</a>
            <h2 class="mt-2 text-3xl font-bold tracking-tight text-white">Conviértete en Asesor</h2>
            <p class="mt-2 text-sm text-gray-400">Comparte tu conocimiento y ayuda a otros a crecer.</p>
        </div>
        <?php if ($success_message): ?>
            <div class="bg-green-900/50 border border-green-400/30 text-green-300 px-4 py-3 rounded-lg relative text-center mb-2">
                <span><?= $success_message ?></span>
            </div>
        <?php elseif ($error_message): ?>
            <div class="bg-red-900/50 border border-red-400/30 text-red-300 px-4 py-3 rounded-lg relative text-center mb-2">
                <span><?= $error_message ?></span>
            </div>
        <?php endif; ?>
        <?php if (!$success_message): ?>
        <form class="mt-8 space-y-6" action="" method="POST" autocomplete="off">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- País -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-300 mb-1">País</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="ph ph-map-pin text-gray-400"></i>
                        </div>
                        <select id="country" name="country" required
                            class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-10 text-white focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                            <option value="">Selecciona país</option>
                            <?php foreach ($paises as $p): ?>
                                <option value="<?= $p['id'] ?>"
                                    <?= (isset($_POST['country']) && $_POST['country'] == $p['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <!-- Ciudad -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-300 mb-1">Ciudad</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="ph ph-buildings text-gray-400"></i>
                        </div>
                        <select id="city" name="city" required
                            class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-10 text-white focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                            <option value="">Selecciona ciudad</option>
                            <?php
                            // Si hay país seleccionado, muestra solo ciudades de ese país
                            $selected_pais = isset($_POST['country']) ? intval($_POST['country']) : null;
                            foreach ($ciudades as $c) {
                                if (!$selected_pais || $c['pais_id'] == $selected_pais) {
                                    echo '<option value="'.$c['id'].'"'.((isset($_POST['city']) && $_POST['city'] == $c['id']) ? ' selected' : '').'>'.htmlspecialchars($c['nombre']).'</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <!-- Especialidades -->
            <div>
                <label for="specialties" class="block text-sm font-medium text-gray-300 mb-1">Especialidades</label>
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="ph ph-star text-gray-400"></i>
                    </div>
                    <input id="specialties" name="specialties" type="text" required 
                        class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-10 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" 
                        placeholder="Ej: Marketing Digital, Desarrollo Web, Finanzas" value="<?= isset($_POST['specialties']) ? htmlspecialchars($_POST['specialties']) : '' ?>">
                </div>
                <p class="mt-1 text-xs text-gray-500">Separa tus especialidades con comas.</p>
            </div>
            <!-- Biografía -->
            <div>
                <label for="biography" class="block text-sm font-medium text-gray-300 mb-1">Biografía</label>
                <textarea id="biography" name="biography" rows="4" required 
                    class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" 
                    placeholder="Cuéntanos un poco sobre ti, tu pasión y por qué quieres ser asesor."><?= isset($_POST['biography']) ? htmlspecialchars($_POST['biography']) : '' ?></textarea>
            </div>
            <!-- Experiencia -->
            <div>
                <label for="experience" class="block text-sm font-medium text-gray-300 mb-1">Experiencia Profesional</label>
                <textarea id="experience" name="experience" rows="4" required 
                    class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" 
                    placeholder="Describe tu experiencia relevante, tus logros y proyectos anteriores."><?= isset($_POST['experience']) ? htmlspecialchars($_POST['experience']) : '' ?></textarea>
            </div>
            <!-- Tarifa por hora -->
            <div>
                <label for="hourly_rate" class="block text-sm font-medium text-gray-300 mb-1">Tu tarifa por hora</label>
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <span class="text-gray-400 sm:text-sm">$</span>
                    </div>
                    <input type="number" name="hourly_rate" id="hourly_rate" required step="0.01" min="0"
                        class="relative block w-full appearance-none rounded-lg border border-gray-600 bg-gray-700 px-3 py-3 pl-7 pr-12 text-white placeholder-gray-400 focus:z-10 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm" 
                        placeholder="0.00" value="<?= isset($_POST['hourly_rate']) ? htmlspecialchars($_POST['hourly_rate']) : '' ?>">
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                        <span class="text-gray-400 sm:text-sm" id="currency">PEN</span>
                    </div>
                </div>
            </div>
            <!-- Botón de envío -->
            <div class="pt-4">
                <button type="submit" 
                    class="group relative flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-3 px-4 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:ring-offset-gray-800">
                    Enviar Solicitud
                </button>
            </div>
        </form>
        <?php endif; ?>
    </div>
</main>
<!-- Script para filtrar ciudades por país (sin recargar) -->
<script>
const ciudades = <?= json_encode($ciudades) ?>;
document.getElementById('country').addEventListener('change', function() {
    const paisId = this.value;
    const citySelect = document.getElementById('city');
    citySelect.innerHTML = '<option value="">Selecciona ciudad</option>';
    ciudades.forEach(function(ciudad) {
        if (ciudad.pais_id == paisId) {
            let option = document.createElement('option');
            option.value = ciudad.id;
            option.textContent = ciudad.nombre;
            citySelect.appendChild(option);
        }
    });
});
</script>
</body>
</html>