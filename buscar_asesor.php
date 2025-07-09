<?php

include('conexion.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || strtolower($_SESSION['role']) !== 'aprendiz') {
    header("Location: index.php");
    exit();
}

// Traer cursos y sus asesores
$sql = "
SELECT
    curso.id AS curso_id,
    curso.nombre AS curso_nombre,
    curso.descripcion,
    curso.categoria,
    curso.inscritos,
    curso.likes,
    asesor.id AS asesor_id,
    asesor.tarifa_por_hora,
    asesor.especialidades,
    usuario.nombre AS asesor_nombre,
    usuario.id AS asesor_user_id
FROM curso
JOIN asesor ON curso.asesorId = asesor.id
JOIN usuario ON asesor.usuarioId = usuario.id
ORDER BY curso.id DESC
";
$res = pg_query($conn, $sql);

$cursos = [];
while ($row = pg_fetch_assoc($res)) {
    $cursos[] = $row;
}

// Obtener valoraciones por asesoría (relaciona por asesorId)
$valoraciones = [];
$valRes = pg_query($conn, "SELECT v.*, u.nombre AS autor_nombre FROM valoracion v JOIN usuario u ON v.autorId=u.id ORDER BY v.fechaCreacion DESC");
while ($row = pg_fetch_assoc($valRes)) {
    $valoraciones[$row['asesoriaId']][] = $row;
}

// Paleta de colores para las cards (agrega/quita los que quieras)
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

// Función para convertir HEX a RGBA con opacidad
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

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar un Asesor - AsesoraMe</title>
    <meta name="description" content="Explora y busca cursos disponibles en AsesoraMe.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #111827; color: #d1d5db; }
        .dropdown:hover .dropdown-menu { display: block; }
        .dropdown-menu { display: none; }
        .modal-bg { background: rgba(17, 24, 39, 0.85); }
    </style>
</head>
<body class="text-gray-300">
    <?php include 'header.php'; ?>

    <main class="container mx-auto px-4 py-12">
        <section class="mb-12">
            <h1 class="text-4xl font-extrabold text-white mb-2">Explora Nuestros Cursos</h1>
            <p class="text-lg text-gray-400 mb-6">Encuentra la asesoría perfecta para llevar tus habilidades al siguiente nivel.</p>
            
            <div class="flex flex-col md:flex-row gap-4">
                <!-- Barra de Búsqueda -->
                <div class="relative flex-grow">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                        <i class="ph ph-magnifying-glass text-gray-400"></i>
                    </div>
                    <input type="search" name="search" id="search"
                           class="block w-full appearance-none rounded-lg border border-gray-700 bg-gray-800 p-3 pl-12 text-white placeholder-gray-500 focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
                           placeholder="Buscar por curso, especialidad o asesor...">
                </div>
                <!-- Filtro de Categoría -->
                <div class="relative">
                     <select id="category-filter" class="w-full md:w-auto appearance-none rounded-lg border border-gray-700 bg-gray-800 p-3 pr-10 text-white focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                        <option value="">Todas las categorías</option>
                        <?php
                        $categorias = array_unique(array_map(function($c) { return $c['categoria']; }, $cursos));
                        foreach ($categorias as $cat):
                            if ($cat) {
                        ?>
                            <option value="<?= htmlspecialchars($cat) ?>"><?= htmlspecialchars($cat) ?></option>
                        <?php }
                        endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-400">
                        <i class="ph ph-caret-down"></i>
                    </div>
                </div>
            </div>
        </section>

<section>
    <div id="courses-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php foreach ($cursos as $i => $curso):
            $color = $colores[$i % count($colores)];
            $valoracionesCurso = isset($valoraciones[$curso['asesor_id']]) ? $valoraciones[$curso['asesor_id']] : [];
        ?>
        <div class="curso-card bg-gray-800 rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform duration-300 group flex flex-col cursor-pointer"
             data-curso='<?= htmlspecialchars(json_encode([
                'id' => $curso['curso_id'],
                'nombre' => $curso['curso_nombre'],
                'descripcion' => $curso['descripcion'],
                'categoria' => $curso['categoria'],
                'inscritos' => $curso['inscritos'],
                'likes' => $curso['likes'],
                'tarifa_por_hora' => $curso['tarifa_por_hora'],
                'especialidades' => $curso['especialidades'],
                'asesor_nombre' => $curso['asesor_nombre'],
                'asesor_user_id' => $curso['asesor_user_id'],
                'asesor_id' => $curso['asesor_id'],
                'color' => $color,
                'valoraciones' => $valoracionesCurso
            ], JSON_UNESCAPED_UNICODE)) ?>'
        >
            <div class="h-40 flex items-center justify-center p-4" style="background-color: <?= $color ?>;">
                <h2 class="text-4xl font-bold text-white text-center"><?= htmlspecialchars($curso['categoria'] ?: 'Otra') ?></h2>
            </div>
            <div class="p-6 flex flex-col flex-grow">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-white text-xs font-semibold px-2.5 py-0.5 rounded-full"
                        style="background-color: <?= hex2rgba($color, 0.5) ?>;">
                        <?= htmlspecialchars($curso['categoria'] ?: 'General') ?>
                    </span>
                    <span class="text-lg font-bold text-white">S/ <?= number_format($curso['tarifa_por_hora'], 2) ?> /h</span>
                </div>
                <h3 class="text-xl font-bold mb-2 text-white"><?= htmlspecialchars($curso['curso_nombre']) ?></h3>
                <p class="text-gray-400 text-sm mb-4 flex-grow"><?= htmlspecialchars($curso['descripcion']) ?></p>
                <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
                    <div class="flex items-center">
                        <img src="https://i.pravatar.cc/40?u=<?= urlencode($curso['asesor_nombre']) ?>" alt="<?= htmlspecialchars($curso['asesor_nombre']) ?>" class="w-8 h-8 rounded-full mr-2 border-2 border-gray-600">
                        <span class="text-sm font-medium text-gray-300"><?= htmlspecialchars($curso['asesor_nombre']) ?></span>
                    </div>
                    <div class="flex items-center space-x-4 text-sm text-gray-400">
                        <span class="flex items-center" title="Estudiantes"><i class="ph ph-users mr-1"></i> <?= (int)$curso['inscritos'] ?></span>
                        <span class="flex items-center" title="Likes"><i class="ph ph-heart mr-1"></i> <?= (int)$curso['likes'] ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <div id="no-results" style="display:none;" class="col-span-full text-center text-gray-400 text-lg py-12">No hay cursos disponibles por ahora.</div>
</section>
    </main>

    <!-- MODAL CURSO -->
    <div id="modal-curso-bg" class="modal-bg fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
    <div class="absolute inset-0 bg-black bg-opacity-80" onclick="closeModalCurso()"></div>
    <div id="modal-curso"
         class="relative bg-gray-900 rounded-2xl shadow-2xl max-w-5xl w-full mx-2 overflow-y-auto max-h-[95vh] border-2 border-indigo-700">
        <button onclick="closeModalCurso()" class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl z-10">
            <i class="ph ph-x"></i>
        </button>
        <div class="lg:grid lg:grid-cols-12 gap-12">
            <!-- Main info -->
            <div class="lg:col-span-8 p-10">
                <span id="modal-curso-categoria" class="text-xs font-semibold px-3 py-1 rounded-full mb-3 inline-block"></span>
                <h1 id="modal-curso-titulo" class="text-3xl md:text-4xl font-extrabold text-white"></h1>
                <p id="modal-curso-descripcion" class="mt-4 text-base text-gray-400"></p>
                <div class="bg-gray-800 rounded-2xl shadow-lg overflow-hidden my-8">
                    <div id="modal-curso-banner" class="h-40 flex items-center justify-center p-4">
                        <h2 class="text-5xl font-bold text-white text-center" id="modal-curso-banner-title"></h2>
                    </div>
                </div>
                <!-- Opiniones -->
                <div>
                    <h3 class="text-xl font-bold text-white mb-4">Opiniones de aprendices</h3>
                    <div id="modal-curso-valoraciones" class="space-y-6"></div>
                </div>
            </div>
            <!-- Aside -->
            <aside class="lg:col-span-4 p-10 bg-gray-800 rounded-tr-2xl rounded-br-2xl">
                <div class="text-center mb-6">
                    <img id="modal-curso-asesor-img" src="" alt="Asesor" class="w-20 h-20 rounded-full mx-auto mb-4 border-4 border-gray-700">
                    <h4 id="modal-curso-asesor-nombre" class="text-lg font-bold text-white"></h4>
                    <p id="modal-curso-asesor-especialidad" class="text-indigo-400 font-medium text-sm"></p>
                </div>
                <ul class="space-y-3 text-sm border-y border-gray-700 py-4">
                    <li class="flex justify-between"><span><i class="ph ph-money mr-2 text-gray-400"></i>Tarifa</span> <span class="font-semibold text-white" id="modal-curso-tarifa"></span></li>
                    <li class="flex justify-between"><span><i class="ph ph-users mr-2 text-gray-400"></i>Inscritos</span> <span class="font-semibold text-white" id="modal-curso-inscritos"></span></li>
                    <li class="flex justify-between"><span><i class="ph ph-heart mr-2 text-gray-400"></i>Likes</span> <span class="font-semibold text-white" id="modal-curso-likes"></span></li>
                </ul>
                <div class="mt-6">
                    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-5 rounded-lg transition-colors duration-300 flex items-center justify-center text-base">
                        <i class="ph ph-calendar-plus mr-2"></i>
                        Agendar Asesoría
                    </button>
                    <p class="text-xs text-center text-gray-500 mt-2">Paga de forma segura. Cancela cuando quieras.</p>
                </div>
            </aside>
        </div>
    </div>
</div>
    <!-- FIN MODAL -->

    <footer id="contact" class="bg-black text-white mt-16">
        <div class="container mx-auto px-4 py-8 text-center text-gray-500 text-sm">
            <p>&copy; <span id="year"></span> AsesoraMe. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();

        // JS para búsqueda y filtrado
        const searchInput = document.getElementById('search');
        const categorySelect = document.getElementById('category-filter');
        const cards = Array.from(document.querySelectorAll('.curso-card'));
        const noResults = document.getElementById('no-results');

        function filtrarCursos() {
            const search = (searchInput.value || '').toLowerCase();
            const categoria = (categorySelect.value || '').toLowerCase();
            let visibles = 0;

            cards.forEach(card => {
                const cursoData = JSON.parse(card.dataset.curso);
                const nombre = (cursoData.nombre||"").toLowerCase();
                const descripcion = (cursoData.descripcion||"").toLowerCase();
                const especialidades = (cursoData.especialidades||"").toLowerCase();
                const asesor = (cursoData.asesor_nombre||"").toLowerCase();
                const cat = (cursoData.categoria||"").toLowerCase();

                let match = true;
                if (categoria && cat !== categoria) match = false;
                if (search) {
                    const text = nombre + ' ' + descripcion + ' ' + especialidades + ' ' + asesor;
                    if (!text.includes(search)) match = false;
                }
                if (match) {
                    card.style.display = '';
                    visibles++;
                } else {
                    card.style.display = 'none';
                }
            });

            noResults.style.display = visibles === 0 ? '' : 'none';
        }
        searchInput.addEventListener('input', filtrarCursos);
        categorySelect.addEventListener('change', filtrarCursos);

        // MODAL LÓGICA
        function closeModalCurso() {
            document.getElementById('modal-curso-bg').style.display = 'none';
        }
        // Cerrar con ESC
        document.addEventListener('keydown', function(e){
            if(e.key === 'Escape') closeModalCurso();
        });

        // Mostrar modal y poblar datos
        cards.forEach(card => {
            card.addEventListener('click', function() {
                const data = JSON.parse(card.dataset.curso);
                // Categoría badge
                const catBadge = document.getElementById('modal-curso-categoria');
                catBadge.textContent = data.categoria || '';
                catBadge.style.backgroundColor = "<?= hex2rgba('#000', 0.5) ?>";
                catBadge.className = "bg-indigo-900/50 text-xs font-semibold px-3 py-1 rounded-full mb-3 inline-block";
                catBadge.style.backgroundColor = hex2rgba(data.color,0.5);
                catBadge.style.color = "#fff";
                // Título, descripción
                document.getElementById('modal-curso-titulo').textContent = data.nombre;
                document.getElementById('modal-curso-descripcion').textContent = data.descripcion;
                // Banner
                const banner = document.getElementById('modal-curso-banner');
                banner.style.backgroundColor = data.color;
                document.getElementById('modal-curso-banner-title').textContent = data.nombre;
                // Asesor
                document.getElementById('modal-curso-asesor-img').src = "https://i.pravatar.cc/80?u=" + encodeURIComponent(data.asesor_nombre);
                document.getElementById('modal-curso-asesor-nombre').textContent = data.asesor_nombre;
                document.getElementById('modal-curso-asesor-especialidad').textContent = data.especialidades || '';
                // Detalles
                document.getElementById('modal-curso-tarifa').textContent = "S/ " + Number(data.tarifa_por_hora).toFixed(2) + " / hora";
                document.getElementById('modal-curso-inscritos').textContent = data.inscritos;
                document.getElementById('modal-curso-likes').textContent = data.likes;

                // Opiniones
                const cont = document.getElementById('modal-curso-valoraciones');
                cont.innerHTML = "";
                if(Array.isArray(data.valoraciones) && data.valoraciones.length) {
                    data.valoraciones.forEach(val => {
                        const stars = '<span class="flex items-center text-yellow-400">' + 
                            '<i class="ph-fill ph-star'.repeat(val.puntuacion) + '"></i>'.repeat(val.puntuacion) +
                            '</span>';
                        cont.innerHTML += `
                        <div class="bg-gray-700 p-4 rounded-2xl">
                            <div class="flex items-center mb-2">
                                <img src="https://i.pravatar.cc/40?u=${encodeURIComponent(val.autor_nombre)}" class="w-9 h-9 rounded-full mr-3">
                                <div>
                                    <p class="font-semibold text-white mb-1">${val.autor_nombre}</p>
                                    <div class="flex items-center text-yellow-400">
                                        ${'<i class="ph-fill ph-star"></i>'.repeat(val.puntuacion)}
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-300 text-sm">"${val.comentario}"</p>
                        </div>
                        `;
                    });
                } else {
                    cont.innerHTML = '<p class="text-gray-400 text-sm">Este curso aún no tiene valoraciones.</p>';
                }

                document.getElementById('modal-curso-bg').style.display = 'flex';
            });
        });

        // Utilidad para RGBA desde JS
        function hex2rgba(hex, opacity) {
            hex = hex.replace('#', '');
            let r, g, b;
            if (hex.length === 3) {
                r = parseInt(hex[0] + hex[0], 16);
                g = parseInt(hex[1] + hex[1], 16);
                b = parseInt(hex[2] + hex[2], 16);
            } else {
                r = parseInt(hex.substring(0, 2), 16);
                g = parseInt(hex.substring(2, 4), 16);
                b = parseInt(hex.substring(4, 6), 16);
            }
            return `rgba(${r},${g},${b},${opacity})`;
        }
    </script>
</body>
</html>