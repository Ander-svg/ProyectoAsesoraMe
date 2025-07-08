<?php
session_start();
include("conexion.php");

// Seguridad: solo asesores logueados
if (!isset($_SESSION['user_id']) || strtolower(trim($_SESSION['role'])) !== 'asesor') {
    header("Location: index.php");
    exit();
}
$asesor_id = $_SESSION['user_id'];

// Paleta de colores (igual que buscar_asesor.php)
$colores = [
    "#8B5CF6", // violeta
    "#34D399", // verde
    "#F87171", // rojo
    "#60A5FA", // azul
    "#FBBF24", // amarillo
    "#F472B6", // rosado
    "#F59E42", // naranja
    "#38BDF8", // celeste
    "#A3E635", // lima
    "#E879F9", // magenta
];
function hex2rgba($hex, $opacity = 0.5) {
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) === 3) {
        $r = hexdec(substr($hex,0,1).substr($hex,0,1));
        $g = hexdec(substr($hex,1,1).substr($hex,1,1));
        $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));
    }
    return "rgba($r,$g,$b,$opacity)";
}

// Procesar creación de curso
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['crear_curso'])) {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = trim($_POST['categoria']);
    if ($nombre !== "") {
        $stmt = $conn->prepare("INSERT INTO curso (asesorId, nombre, descripcion, categoria) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $asesor_id, $nombre, $descripcion, $categoria);
        $stmt->execute();
        $stmt->close();
        header("Location: asesor_cursos.php?msg=creado");
        exit();
    }
}

// Procesar edición de curso
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['editar_curso'])) {
    $curso_id = intval($_POST['curso_id']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = trim($_POST['categoria']);
    $stmt = $conn->prepare("UPDATE curso SET nombre = ?, descripcion = ?, categoria = ? WHERE id = ? AND asesorId = ?");
    $stmt->bind_param("sssii", $nombre, $descripcion, $categoria, $curso_id, $asesor_id);
    $stmt->execute();
    $stmt->close();
    header("Location: asesor_cursos.php?msg=editado");
    exit();
}

// Listar cursos del asesor
$res = $conn->query("SELECT * FROM curso WHERE asesorId = $asesor_id ORDER BY id DESC");
$cursos = $res->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Asesor - Mis Cursos</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #111827; color: #d1d5db; }
        .modal-bg { background: rgba(17, 24, 39, 0.85); }
        .card-crear-curso {
            transition: border 0.2s, box-shadow 0.2s, background 0.2s;
            border: 2px dashed #374151;
            cursor: pointer;
        }
        .card-crear-curso:hover {
            background: #23293a;
            border-color: #6366f1;
            box-shadow: 0 0 0 2px #6366f1;
        }
        .card-crear-curso .icono-mas {
            transition: color 0.2s, border-color 0.2s;
            color: #6b7280;
            border: 2px solid #6b7280;
        }
        .card-crear-curso:hover .icono-mas {
            color: #6366f1;
            border-color: #6366f1;
        }
    </style>
</head>
<body class="text-gray-300">
    <?php include 'header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <section class="mb-8 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-4xl font-extrabold text-white">Mis Cursos</h1>
                <p class="text-lg text-gray-400 mt-1">Gestiona, edita y crea nuevos cursos para tus estudiantes.</p>
            </div>
            <button onclick="document.getElementById('modal-nuevo').style.display='flex'"
                class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-lg transition-colors duration-300 flex items-center justify-center">
                <i class="ph ph-plus-circle mr-2 text-xl"></i>
                Crear Nuevo Curso
            </button>
        </section>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == "creado"): ?>
            <div class="bg-green-900/70 text-green-300 px-4 py-2 rounded mb-4">¡Curso creado correctamente!</div>
        <?php elseif (isset($_GET['msg']) && $_GET['msg'] == "editado"): ?>
            <div class="bg-blue-900/70 text-blue-300 px-4 py-2 rounded mb-4">Curso editado correctamente.</div>
        <?php endif; ?>

        <!-- Listado de cursos con cards de color -->
        <section>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <?php foreach ($cursos as $i => $curso):
                    $color = $colores[$i % count($colores)];
                ?>
                <div class="bg-gray-800 rounded-2xl shadow-lg overflow-hidden flex flex-col">
                    <div class="h-32 flex items-center justify-center p-4"
                         style="background-color: <?= $color ?>;">
                        <h2 class="text-3xl font-bold text-white text-center"><?= htmlspecialchars($curso['nombre']) ?></h2>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full"
                              style="background-color: <?= hex2rgba($color, 0.5) ?>;">
                            <?= htmlspecialchars($curso['categoria']) ?>
                        </span>
                        <p class="text-gray-400 text-sm mb-4 flex-grow"><?= htmlspecialchars($curso['descripcion']) ?></p>
                        <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
                            <div class="flex items-center space-x-4 text-sm text-gray-400">
                                <span title="Estudiantes"><i class="ph ph-users mr-1"></i> <?= (int)$curso['inscritos'] ?></span>
                                <span title="Likes"><i class="ph ph-heart mr-1"></i> <?= (int)$curso['likes'] ?></span>
                            </div>
                            <button onclick="abrirEditarCurso(<?= $curso['id'] ?>, <?= htmlspecialchars(json_encode($curso), ENT_QUOTES, 'UTF-8') ?>)"
                                class="text-indigo-400 hover:text-indigo-300 text-sm font-semibold">Editar</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Card para crear curso -->
                <div class="card-crear-curso rounded-2xl flex flex-col items-center justify-center text-center p-6 min-h-[220px] select-none"
                     onclick="document.getElementById('modal-nuevo').style.display='flex'">
                    <span class="icono-mas rounded-full flex items-center justify-center mx-auto mb-3" style="width:54px; height:54px;">
                        <i class="ph ph-plus text-4xl"></i>
                    </span>
                    <span class="font-semibold text-gray-400 text-lg">Crear un nuevo curso</span>
                </div>
            </div>
        </section>
    </main>

    <!-- MODAL NUEVO CURSO -->
    <div id="modal-nuevo" class="modal-bg fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
        <div class="absolute inset-0 bg-black bg-opacity-90" onclick="document.getElementById('modal-nuevo').style.display='none'"></div>
        <form action="" method="POST" class="bg-gray-900 rounded-2xl shadow-2xl max-w-lg w-full mx-2 p-8 z-10 relative border-2 border-indigo-700">
            <h2 class="text-2xl font-bold text-white mb-4">Crear Nuevo Curso</h2>
            <div class="mb-4">
                <label class="block mb-1 text-sm" for="nombre-nuevo">Nombre del curso</label>
                <input required type="text" name="nombre" id="nombre-nuevo" class="w-full rounded p-2 bg-gray-800 border border-gray-700 text-white">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm" for="categoria-nuevo">Categoría</label>
                <input type="text" name="categoria" id="categoria-nuevo" class="w-full rounded p-2 bg-gray-800 border border-gray-700 text-white">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm" for="descripcion-nuevo">Descripción</label>
                <textarea name="descripcion" id="descripcion-nuevo" rows="3" class="w-full rounded p-2 bg-gray-800 border border-gray-700 text-white"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-nuevo').style.display='none'"
                    class="px-4 py-2 rounded bg-gray-700 text-white hover:bg-gray-600">Cancelar</button>
                <button type="submit" name="crear_curso"
                    class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white font-bold">Crear</button>
            </div>
        </form>
    </div>

    <!-- MODAL EDITAR CURSO -->
    <div id="modal-editar" class="modal-bg fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
        <div class="absolute inset-0 bg-black bg-opacity-90" onclick="document.getElementById('modal-editar').style.display='none'"></div>
        <form id="form-editar" action="" method="POST" class="bg-gray-900 rounded-2xl shadow-2xl max-w-lg w-full mx-2 p-8 z-10 relative border-2 border-indigo-700">
            <input type="hidden" name="curso_id" id="editar-id">
            <h2 class="text-2xl font-bold text-white mb-4">Editar Curso</h2>
            <div class="mb-4">
                <label class="block mb-1 text-sm" for="editar-nombre">Nombre</label>
                <input required type="text" name="nombre" id="editar-nombre" class="w-full rounded p-2 bg-gray-800 border border-gray-700 text-white">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm" for="editar-categoria">Categoría</label>
                <input type="text" name="categoria" id="editar-categoria" class="w-full rounded p-2 bg-gray-800 border border-gray-700 text-white">
            </div>
            <div class="mb-4">
                <label class="block mb-1 text-sm" for="editar-descripcion">Descripción</label>
                <textarea name="descripcion" id="editar-descripcion" rows="3" class="w-full rounded p-2 bg-gray-800 border border-gray-700 text-white"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modal-editar').style.display='none'"
                    class="px-4 py-2 rounded bg-gray-700 text-white hover:bg-gray-600">Cancelar</button>
                <button type="submit" name="editar_curso"
                    class="px-4 py-2 rounded bg-indigo-600 hover:bg-indigo-700 text-white font-bold">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <footer id="contact" class="bg-black text-white mt-16">
        <div class="container mx-auto px-4 py-8 text-center text-gray-500 text-sm">
            <p>&copy; <span id="year"></span> AsesoraMe. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        // Año en footer
        const yearSpan = document.getElementById('year');
        if (yearSpan) yearSpan.textContent = new Date().getFullYear();

        // Llenar modal de edición
        function abrirEditarCurso(id, data) {
            document.getElementById('editar-id').value = id;
            document.getElementById('editar-nombre').value = data.nombre;
            document.getElementById('editar-categoria').value = data.categoria || "";
            document.getElementById('editar-descripcion').value = data.descripcion || "";
            document.getElementById('modal-editar').style.display = 'flex';
        }
    </script>
</body>
</html>