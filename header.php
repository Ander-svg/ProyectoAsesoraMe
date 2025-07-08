<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : null;
$role = $is_logged_in ? $_SESSION['role'] : null;
?>
<header class="bg-gray-900/80 backdrop-blur-lg sticky top-0 z-50 shadow-lg shadow-indigo-900/10">
    <div class="container mx-auto px-4">
        <nav class="flex justify-between items-center py-4">
            <!-- Logo -->
            <a href="index.php" class="text-2xl font-bold text-white">AsesoraMe</a>

            <!-- Menú de Navegación -->
            <ul class="hidden md:flex items-center space-x-8">
                <?php if ($is_logged_in && strtolower($role) === 'aprendiz'): ?>
                    <li><a href="index.php" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Inicio</a></li>
                    <li><a href="buscar_asesor.php" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Cursos</a></li>
                    <li><a href="asesorias.php" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Asesorías</a></li>
                    <li><a href="pagos.php" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Pagos</a></li>
                    <li><a href="chat.php" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Chat</a></li>
                <?php elseif ($is_logged_in && strtolower($role) === 'asesor'): ?>
                    <li><a href="index.php" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Inicio</a></li>
                    <li><a href="asesor_cursos.php" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Cursos</a></li>
                    <li><a href="asesorias_asesor.php" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Asesorías</a></li>
                    <li><a href="chat.php" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Chat</a></li>
                <?php else: ?>
                    <li><a href="#hero" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Inicio</a></li>
                    <li><a href="#about" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">¿Cómo funciona?</a></li>
                    <li><a href="#courses" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Cursos</a></li>
                    <li><a href="#contact" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Soporte</a></li>
                <?php endif; ?>
            </ul>

            <!-- Acciones de Usuario -->
            <div id="user-actions" class="flex items-center space-x-4">
            <?php if ($is_logged_in): ?>
                <div class="relative">
                    <!-- Avatar + Nombre + Rol en botón -->
                    <button id="user-profile-btn" type="button" class="flex items-center space-x-2 text-gray-200 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 rounded-full px-2 py-1">
                        <img src="https://i.pravatar.cc/40?u=<?= htmlspecialchars($username) ?>" alt="avatar" class="w-9 h-9 rounded-full border-2 border-gray-600">
                        <span class="hidden sm:flex flex-col text-left">
                            <span class="font-medium"><?= htmlspecialchars($username) ?></span>
                            <span class="text-xs text-indigo-300 font-semibold"><?= htmlspecialchars(ucfirst($role)) ?></span>
                        </span>
                        <i class="ph ph-caret-down"></i>
                    </button>
                    <!-- Menú desplegable -->
                    <div id="profile-menu" class="absolute right-0 mt-2 w-56 bg-gray-800 rounded-md shadow-lg py-2 z-30 border border-gray-700 hidden animate-fade-in">
                        <div class="px-4 py-2 border-b border-gray-700 mb-1">
                            <span class="block font-medium text-white"><?= htmlspecialchars($username) ?></span>
                            <span class="block text-xs text-indigo-300 font-semibold"><?= htmlspecialchars(ucfirst($role)) ?></span>
                        </div>
                        <a href="profile.php" class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Mi perfil</a>
                        <a href="logout.php" class="block px-4 py-2 text-sm text-red-400 hover:text-white hover:bg-red-600/80 transition-colors duration-150">Cerrar sesión</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" id="login-btn" class="text-gray-300 hover:text-white font-medium transition-colors duration-300">Iniciar Sesión</a>
                <a href="register.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-5 rounded-full transition-colors duration-300 hidden sm:block">Regístrate</a>
            <?php endif; ?>
            </div>

            <!-- Menú Móvil (Hamburguesa) -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-300 hover:text-white">
                    <i class="ph ph-list text-3xl"></i>
                </button>
            </div>
        </nav>
    </div>
    <!-- Menú desplegable para móvil -->
    <div id="mobile-menu" class="hidden md:hidden bg-gray-900/90 backdrop-blur-lg">
        <ul class="flex flex-col items-center space-y-4 py-4">
            <?php if ($is_logged_in && strtolower($role) === 'aprendiz'): ?>
                <li><a href="index.php" class="text-gray-300 hover:text-white font-medium">Inicio</a></li>
                <li><a href="buscar_asesor.php" class="text-gray-300 hover:text-white font-medium">Cursos</a></li>
                <li><a href="asesorias.php" class="text-gray-300 hover:text-white font-medium">Asesorías</a></li>
                <li><a href="pagos.php" class="text-gray-300 hover:text-white font-medium">Pagos</a></li>
                <li><a href="chat.php" class="text-gray-300 hover:text-white font-medium">Chat</a></li>
                <li>
                    <span class="block font-medium text-white"><?= htmlspecialchars($username) ?></span>
                    <span class="block text-xs text-indigo-300 font-semibold"><?= htmlspecialchars(ucfirst($role)) ?></span>
                </li>
                <li><a href="profile.php" class="text-gray-300 hover:text-white font-medium">Mi perfil</a></li>
                <li><a href="logout.php" class="text-red-400 hover:text-white font-medium">Cerrar sesión</a></li>
            <?php elseif ($is_logged_in && strtolower($role) === 'asesor'): ?>
                <li><a href="index.php" class="text-gray-300 hover:text-white font-medium">Inicio</a></li>
                <li><a href="asesor_cursos.php" class="text-gray-300 hover:text-white font-medium">Cursos</a></li>
                <li><a href="asesorias_asesor.php" class="text-gray-300 hover:text-white font-medium">Asesorías</a></li>
                <li><a href="chat.php" class="text-gray-300 hover:text-white font-medium">Chat</a></li>
                <li>
                    <span class="block font-medium text-white"><?= htmlspecialchars($username) ?></span>
                    <span class="block text-xs text-indigo-300 font-semibold"><?= htmlspecialchars(ucfirst($role)) ?></span>
                </li>
                <li><a href="profile.php" class="text-gray-300 hover:text-white font-medium">Mi perfil</a></li>
                <li><a href="logout.php" class="text-red-400 hover:text-white font-medium">Cerrar sesión</a></li>
            <?php else: ?>
                <li><a href="#hero" class="text-gray-300 hover:text-white font-medium">Inicio</a></li>
                <li><a href="#about" class="text-gray-300 hover:text-white font-medium">¿Cómo funciona?</a></li>
                <li><a href="#courses" class="text-gray-300 hover:text-white font-medium">Cursos</a></li>
                <li><a href="#contact" class="text-gray-300 hover:text-white font-medium">Soporte</a></li>
                <li><a href="login.php" class="text-gray-300 hover:text-white font-medium">Iniciar Sesión</a></li>
                <li><a href="register.php" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-5 rounded-full transition-colors duration-300">Regístrate</a></li>
            <?php endif; ?>
        </ul>
    </div>
</header>
<!-- Animación fade-in solo para el menú -->
<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px);}
    to { opacity: 1; transform: translateY(0);}
}
.animate-fade-in { animation: fade-in 0.16s ease; }
</style>
<script>
// Asegura el correcto funcionamiento del menú usuario en todas las páginas
(function() {
    function activarMenuUsuario() {
        var profileBtn = document.getElementById('user-profile-btn');
        var profileMenu = document.getElementById('profile-menu');
        if (profileBtn && profileMenu) {
            profileBtn.onclick = function(e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            }
            document.addEventListener('click', function () {
                profileMenu.classList.add('hidden');
            });
            profileMenu.onclick = function(e) {
                e.stopPropagation();
            }
        }
    }
    if (document.readyState === "loading") {
        document.addEventListener('DOMContentLoaded', activarMenuUsuario);
    } else {
        activarMenuUsuario();
    }
})();
</script>