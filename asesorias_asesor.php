<?php
session_start();
include('conexion.php');

if (!isset($_SESSION['user_id']) || strtolower(trim($_SESSION['role'])) !== 'asesor') {
    header("Location: index.php");
    exit();
}

// Obtener el id interno de asesor
$user_id = $_SESSION['user_id'];
$res = mysqli_query($conn, "SELECT id FROM asesor WHERE usuarioId = $user_id LIMIT 1");
$asesorData = mysqli_fetch_assoc($res);
if (!$asesorData) {
    die("No eres un asesor registrado.");
}
$asesor_id = $asesorData['id'];

// Procesar acciones de aceptar/cancelar
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['accion']) && isset($_POST['sesion_id'])) {
        $sesion_id = intval($_POST['sesion_id']);
        if ($_POST['accion'] === 'confirmar') {
            mysqli_query($conn, "UPDATE sesionasesoria SET estado = 'CONFIRMADA' WHERE id = $sesion_id AND asesorId = $asesor_id AND estado = 'PENDIENTE'");
        } elseif ($_POST['accion'] === 'cancelar') {
            mysqli_query($conn, "UPDATE sesionasesoria SET estado = 'CANCELADA' WHERE id = $sesion_id AND asesorId = $asesor_id AND estado IN ('PENDIENTE','CONFIRMADA')");
        }
        header("Location: asesorias_asesor.php");
        exit();
    }
}

// Traer todas las asesorías de este asesor
$sql = "
SELECT
    sa.*,
    c.nombre AS curso_nombre,
    u.nombre AS aprendiz_nombre
FROM sesionasesoria sa
LEFT JOIN curso c ON sa.cursoId = c.id
JOIN usuario u ON sa.aprendizId = u.id
WHERE sa.asesorId = $asesor_id
ORDER BY sa.fecha_hora_inicio DESC
";
$res = mysqli_query($conn, $sql);

$asesorias = [
    'PENDIENTE' => [],
    'CONFIRMADA' => [],
    'COMPLETADA' => []
];

while ($row = mysqli_fetch_assoc($res)) {
    if ($row['estado'] === 'PENDIENTE') $asesorias['PENDIENTE'][] = $row;
    elseif ($row['estado'] === 'CONFIRMADA') $asesorias['CONFIRMADA'][] = $row;
    elseif ($row['estado'] === 'COMPLETADA') $asesorias['COMPLETADA'][] = $row;
    // CANCELADA no se muestra
}

function formatDate($datetime) {
    setlocale(LC_TIME, 'es_ES.UTF-8');
    $dt = new DateTime($datetime);
    return $dt->format('Y-m-d');
}
function formatTime($datetime) {
    $dt = new DateTime($datetime);
    return $dt->format('H:i');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Asesorías - Asesor | AsesoraMe</title>
    <meta name="description" content="Gestiona tus asesorías programadas y pasadas en AsesoraMe.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #111827; color: #d1d5db; }
        .kanban-board::-webkit-scrollbar { height: 8px; }
        .kanban-board::-webkit-scrollbar-track { background: #1f2937; }
        .kanban-board::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }
        .kanban-board::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="text-gray-300">
    <?php include 'header.php'; ?>

    <main class="container mx-auto px-4 py-12">

        <section class="mb-8">
            <h1 class="text-4xl font-extrabold text-white">Tablero de Asesorías</h1>
            <p class="text-lg text-gray-400 mt-1">Organiza y gestiona tus próximas sesiones.</p>
        </section>

        <!-- Tablero Kanban -->
        <section class="w-full">
            <div class="kanban-board flex space-x-6 overflow-x-auto pb-4">

                <!-- Columna: Pendientes -->
                <div class="flex-shrink-0 w-80 bg-gray-800 rounded-2xl shadow-lg">
                    <div class="p-4 border-b border-gray-700">
                        <h2 class="font-bold text-white flex items-center"><span class="w-3 h-3 rounded-full bg-yellow-400 mr-2"></span>Pendientes</h2>
                    </div>
                    <div class="p-4 space-y-4 min-h-[120px]">
                        <?php if (count($asesorias['PENDIENTE']) === 0): ?>
                            <div class="text-gray-500 text-sm text-center py-6">Sin asesorías pendientes.</div>
                        <?php endif; ?>
                        <?php foreach ($asesorias['PENDIENTE'] as $sesion): ?>
                        <div class="bg-gray-700/50 p-4 rounded-lg border border-gray-700">
                            <p class="font-semibold text-white"><?= htmlspecialchars($sesion['curso_nombre'] ?: 'Curso no asignado') ?></p>
                            <p class="text-sm text-gray-400 mb-2">con <?= htmlspecialchars($sesion['aprendiz_nombre']) ?></p>
                            <div class="text-xs text-gray-300 space-y-1 mb-3">
                                <p><i class="ph ph-calendar-blank mr-1"></i> <?= formatDate($sesion['fecha_hora_inicio']) ?></p>
                                <p><i class="ph ph-clock mr-1"></i> <?= formatTime($sesion['fecha_hora_inicio']) ?> (<?= intval($sesion['duracion_minutos']) ?> min)</p>
                                <?php if ($sesion['modalidad'] === 'VIRTUAL'): ?>
                                    <p><i class="ph ph-link mr-1"></i> <?= htmlspecialchars($sesion['enlace_reunion']) ?></p>
                                <?php elseif ($sesion['modalidad'] === 'PRESENCIAL'): ?>
                                    <p><i class="ph ph-buildings mr-1"></i> <?= htmlspecialchars($sesion['ubicacion_fisica']) ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="flex gap-2">
                                <form method="post" class="w-full">
                                    <input type="hidden" name="sesion_id" value="<?= $sesion['id'] ?>">
                                    <button name="accion" value="confirmar" type="submit" class="w-full bg-green-600/50 hover:bg-green-600 text-white text-xs font-bold py-1.5 px-3 rounded-md">Confirmar</button>
                                </form>
                                <form method="post" class="w-full">
                                    <input type="hidden" name="sesion_id" value="<?= $sesion['id'] ?>">
                                    <button name="accion" value="cancelar" type="submit" class="w-full bg-red-600/50 hover:bg-red-600 text-white text-xs font-bold py-1.5 px-3 rounded-md">Cancelar</button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: Próximas (Confirmadas) -->
                <div class="flex-shrink-0 w-80 bg-gray-800 rounded-2xl shadow-lg">
                    <div class="p-4 border-b border-gray-700">
                        <h2 class="font-bold text-white flex items-center"><span class="w-3 h-3 rounded-full bg-green-400 mr-2"></span>Próximas</h2>
                    </div>
                    <div class="p-4 space-y-4 min-h-[120px]">
                        <?php if (count($asesorias['CONFIRMADA']) === 0): ?>
                            <div class="text-gray-500 text-sm text-center py-6">Sin próximas asesorías.</div>
                        <?php endif; ?>
                        <?php foreach ($asesorias['CONFIRMADA'] as $sesion): ?>
                        <div class="bg-gray-700/50 p-4 rounded-lg border border-gray-700">
                            <p class="font-semibold text-white"><?= htmlspecialchars($sesion['curso_nombre'] ?: 'Curso no asignado') ?></p>
                            <p class="text-sm text-gray-400 mb-2">con <?= htmlspecialchars($sesion['aprendiz_nombre']) ?></p>
                            <div class="text-xs text-gray-300 space-y-1 mb-3">
                                <p><i class="ph ph-calendar-blank mr-1"></i> <?= formatDate($sesion['fecha_hora_inicio']) ?></p>
                                <p><i class="ph ph-clock mr-1"></i> <?= formatTime($sesion['fecha_hora_inicio']) ?> (<?= intval($sesion['duracion_minutos']) ?> min)</p>
                                <?php if ($sesion['modalidad'] === 'VIRTUAL'): ?>
                                    <p><i class="ph ph-link mr-1"></i> <a href="<?= htmlspecialchars($sesion['enlace_reunion']) ?>" class="underline" target="_blank"><?= htmlspecialchars($sesion['enlace_reunion']) ?></a></p>
                                <?php elseif ($sesion['modalidad'] === 'PRESENCIAL'): ?>
                                    <p><i class="ph ph-buildings mr-1"></i> <?= htmlspecialchars($sesion['ubicacion_fisica']) ?></p>
                                <?php endif; ?>
                            </div>
                            <form method="post" class="w-full">
                                <input type="hidden" name="sesion_id" value="<?= $sesion['id'] ?>">
                                <button name="accion" value="cancelar" type="submit" class="w-full bg-red-600/50 hover:bg-red-600 text-white text-xs font-bold py-1.5 px-3 rounded-md">Cancelar</button>
                            </form>
                            <?php if ($sesion['modalidad'] === 'VIRTUAL' && !empty($sesion['enlace_reunion'])): ?>
                                <a href="<?= htmlspecialchars($sesion['enlace_reunion']) ?>" target="_blank" class="block mt-2 w-full bg-indigo-600/80 hover:bg-indigo-600 text-white text-sm font-bold py-2 px-3 rounded-md text-center">Unirse a la reunión</a>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Columna: Completadas -->
                <div class="flex-shrink-0 w-80 bg-gray-800 rounded-2xl shadow-lg">
                    <div class="p-4 border-b border-gray-700">
                        <h2 class="font-bold text-white flex items-center"><span class="w-3 h-3 rounded-full bg-gray-500 mr-2"></span>Completadas</h2>
                    </div>
                    <div class="p-4 space-y-4 min-h-[120px]">
                        <?php if (count($asesorias['COMPLETADA']) === 0): ?>
                            <div class="text-gray-500 text-sm text-center py-6">Sin asesorías completadas.</div>
                        <?php endif; ?>
                        <?php foreach ($asesorias['COMPLETADA'] as $sesion): ?>
                        <div class="bg-gray-700/50 p-4 rounded-lg border border-gray-700 opacity-70">
                            <p class="font-semibold text-white"><?= htmlspecialchars($sesion['curso_nombre'] ?: 'Curso no asignado') ?></p>
                            <p class="text-sm text-gray-400 mb-2">con <?= htmlspecialchars($sesion['aprendiz_nombre']) ?></p>
                            <div class="text-xs text-gray-400 space-y-1">
                                <p><i class="ph ph-calendar-check mr-1"></i> <?= formatDate($sesion['fecha_hora_inicio']) ?></p>
                                <p><i class="ph ph-clock mr-1"></i> <?= formatTime($sesion['fecha_hora_inicio']) ?> (<?= intval($sesion['duracion_minutos']) ?> min)</p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </section>

    </main>

    <footer id="contact" class="bg-black text-white mt-16">
        <div class="container mx-auto px-4 py-8 text-center text-gray-500 text-sm">
            <p>&copy; <span id="year"></span> AsesoraMe. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>