<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : null;
$role = $is_logged_in ? $_SESSION['role'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AsesoraMe - Aprende y Crece</title>
    <meta name="description" content="Plataforma de asesorías y cursos en línea para potenciar tu desarrollo profesional y personal.">
    <meta name="keywords" content="cursos, asesoría, online, aprendizaje, desarrollo profesional">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #111827;
            color: #d1d5db;
        }
        .hero-gradient {
            background: linear-gradient(135deg, #1e3a8a 0%, #4f46e5 50%, #7c3aed 100%);
        }
    </style>
</head>
<body class="text-gray-300">
    <?php include 'header.php'; ?>
    <!-- ========== MAIN CONTENT ========== -->
    <main>
        <!-- ========== Hero Section ========== -->
        <section id="hero" class="hero-gradient text-white">
            <div class="container mx-auto px-4 h-[85vh] flex flex-col justify-center items-center text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold mb-4 leading-tight">Alcanza tu Máximo Potencial</h1>
                <p class="text-lg md:text-xl max-w-3xl mb-8 text-indigo-200">Conecta con asesores expertos en cientos de materias. Aprende a tu ritmo, de forma virtual o presencial.</p>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                    <?php if ($is_logged_in && strtolower($role) === 'aprendiz'): ?>
                        <a href="buscar_asesor.php" class="bg-white text-indigo-700 hover:bg-indigo-100 font-bold py-3 px-8 rounded-full transition-transform transform hover:scale-105 duration-300">Buscar un Asesor</a>
                    <?php else: ?>
                        <a href="#courses" class="bg-white text-indigo-700 hover:bg-indigo-100 font-bold py-3 px-8 rounded-full transition-transform transform hover:scale-105 duration-300">Buscar un Asesor</a>
                    <?php endif; ?>
                    <a href="aplica_asesor.php" class="bg-transparent border-2 border-white hover:bg-white/20 text-white font-bold py-3 px-8 rounded-full transition-all transform hover:scale-105 duration-300">
                        Conviértete en Asesor
                    </a>
                </div>
            </div>
        </section>

        <!-- ========== About Section ========== -->
        <section id="about" class="py-20 bg-gray-900">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-2 gap-12 items-center">
                    <div class="order-2 md:order-1">
                        <h2 class="text-3xl font-bold text-white mb-4">Encuentra tu Curso Ideal con Asesores Expertos</h2>
                        <p class="text-gray-300 mb-6">En AsesoraMe, el conocimiento se potencia con la experiencia. Te conectamos con asesores expertos que imparten cursos prácticos en una amplia variedad de materias para que adquieras habilidades reales.</p>
                        <ul class="space-y-4">
                            <li class="flex items-start">
                                <i class="ph-fill ph-check-circle text-purple-400 text-2xl mr-3 mt-1"></i>
                                <span><strong class="text-white">Variedad de Cursos:</strong> Explora un catálogo diverso en tecnología, artes, ciencias, negocios y más.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ph-fill ph-check-circle text-purple-400 text-2xl mr-3 mt-1"></i>
                                <span><strong class="text-white">Aprendizaje Flexible:</strong> Elige el formato que más te convenga, con horarios flexibles y sesiones personalizadas.</span>
                            </li>
                            <li class="flex items-start">
                                <i class="ph-fill ph-check-circle text-purple-400 text-2xl mr-3 mt-1"></i>
                                <span><strong class="text-white">Calidad Garantizada:</strong> Todos nuestros asesores son profesionales verificados con experiencia probada en su campo.</span>
                            </li>
                        </ul>
                        <a href="#courses" class="mt-8 inline-block bg-purple-600 text-white font-semibold py-3 px-6 rounded-lg hover:bg-purple-700 transition-colors duration-300">
                            Ver todos los cursos <i class="ph ph-arrow-right ml-2"></i>
                        </a>
                    </div>
                    <div class="order-1 md:order-2">
                        <img src="https://placehold.co/600x400/111827/7c3aed?text=Aprende+con+Expertos" alt="Persona aprendiendo en línea" class="rounded-2xl shadow-2xl shadow-purple-900/30 w-full h-auto">
                    </div>
                </div>
            </div>
        </section>
        
        <!-- ========== Stats Section ========== -->
        <section id="stats" class="py-20 bg-gray-800">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                    <div class="p-4">
                        <h3 class="text-4xl font-extrabold text-white">1,200+</h3>
                        <p class="text-gray-400 font-medium mt-2">Estudiantes Felices</p>
                    </div>
                    <div class="p-4">
                        <h3 class="text-4xl font-extrabold text-white">60+</h3>
                        <p class="text-gray-400 font-medium mt-2">Cursos de Calidad</p>
                    </div>
                    <div class="p-4">
                        <h3 class="text-4xl font-extrabold text-white">40+</h3>
                        <p class="text-gray-400 font-medium mt-2">Asesores Expertos</p>
                    </div>
                    <div class="p-4">
                        <h3 class="text-4xl font-extrabold text-white">98%</h3>
                        <p class="text-gray-400 font-medium mt-2">Tasa de Satisfacción</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ========== Courses Section ========== -->
        <section id="courses" class="py-20 bg-gray-900">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-white">Nuestros Cursos Populares</h2>
                    <p class="text-gray-400 mt-2 max-w-2xl mx-auto">Explora una selección de nuestros cursos mejor valorados y comienza a aprender una nueva habilidad hoy mismo.</p>
                </div>
                <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Course Card 1 -->
                    <div class="bg-gray-800 rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform duration-300 group">
                        <img src="https://placehold.co/400x250/a5b4fc/1e1b4b?text=Autocad" alt="Curso de Autocad" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-indigo-900/50 text-indigo-300 text-xs font-semibold px-2.5 py-0.5 rounded-full">Diseño 2D y 3D</span>
                                <span class="text-lg font-bold text-white">S/ 30 /h</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-white group-hover:text-indigo-400 transition-colors">Curso de Autocad</h3>
                            <p class="text-gray-400 text-sm mb-4">Aprende a crear planos y modelos 3D con la herramienta líder en la industria del diseño técnico.</p>
                            <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
                                <div class="flex items-center">
                                    <img src="https://i.pravatar.cc/40?u=antonio" alt="Antonio" class="w-8 h-8 rounded-full mr-2 border-2 border-gray-600">
                                    <span class="text-sm font-medium text-gray-300">Antonio</span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-400">
                                    <span class="flex items-center"><i class="ph ph-users mr-1"></i> 50</span>
                                    <span class="flex items-center"><i class="ph ph-heart mr-1"></i> 65</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Course Card 2 -->
                    <div class="bg-gray-800 rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform duration-300 group">
                        <img src="https://placehold.co/400x250/fca5a5/450a0a?text=Aritmética" alt="Curso de Aritmética" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-red-900/50 text-red-300 text-xs font-semibold px-2.5 py-0.5 rounded-full">Matemáticas</span>
                                <span class="text-lg font-bold text-white">S/ 20 /h</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-white group-hover:text-red-400 transition-colors">Curso de Aritmética</h3>
                            <p class="text-gray-400 text-sm mb-4">Domina los conceptos fundamentales de la aritmética para resolver problemas complejos.</p>
                            <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
                                <div class="flex items-center">
                                    <img src="https://i.pravatar.cc/40?u=lana" alt="Lana" class="w-8 h-8 rounded-full mr-2 border-2 border-gray-600">
                                    <span class="text-sm font-medium text-gray-300">Lana</span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-400">
                                    <span class="flex items-center"><i class="ph ph-users mr-1"></i> 35</span>
                                    <span class="flex items-center"><i class="ph ph-heart mr-1"></i> 42</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Course Card 3 -->
                    <div class="bg-gray-800 rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 transition-transform duration-300 group">
                        <img src="https://placehold.co/400x250/86efac/14532d?text=POO" alt="Curso de Programación Orientada a Objetos" class="w-full h-48 object-cover">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-3">
                                <span class="bg-green-900/50 text-green-300 text-xs font-semibold px-2.5 py-0.5 rounded-full">Programación</span>
                                <span class="text-lg font-bold text-white">S/ 25 /h</span>
                            </div>
                            <h3 class="text-xl font-bold mb-2 text-white group-hover:text-green-400 transition-colors">Programación Orientada a Objetos</h3>
                            <p class="text-gray-400 text-sm mb-4">Construye software robusto y escalable aprendiendo los pilares de la POO.</p>
                            <div class="border-t border-gray-700 pt-4 flex justify-between items-center">
                                <div class="flex items-center">
                                    <img src="https://i.pravatar.cc/40?u=brandon" alt="Brandon" class="w-8 h-8 rounded-full mr-2 border-2 border-gray-600">
                                    <span class="text-sm font-medium text-gray-300">Brandon</span>
                                </div>
                                <div class="flex items-center space-x-4 text-sm text-gray-400">
                                    <span class="flex items-center"><i class="ph ph-users mr-1"></i> 20</span>
                                    <span class="flex items-center"><i class="ph ph-heart mr-1"></i> 85</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ========== FOOTER ========== -->
    <footer id="contact" class="bg-black text-white">
        <div class="container mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                <!-- About -->
                <div class="col-span-1 md:col-span-2 lg:col-span-1">
                    <h4 class="text-2xl font-bold mb-4">AsesoraMe</h4>
                    <p class="text-gray-400 mb-4">Tu plataforma de confianza para el aprendizaje y desarrollo continuo. Conectamos talento con conocimiento.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="ph ph-twitter-logo text-2xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="ph ph-facebook-logo text-2xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="ph ph-instagram-logo text-2xl"></i></a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors"><i class="ph ph-linkedin-logo text-2xl"></i></a>
                    </div>
                </div>
                <!-- Links -->
                <div>
                    <h5 class="font-bold text-lg mb-4">Enlaces</h5>
                    <ul class="space-y-2">
                        <li><a href="#hero" class="text-gray-400 hover:text-white transition-colors">Inicio</a></li>
                        <li><a href="#courses" class="text-gray-400 hover:text-white transition-colors">Cursos</a></li>
                        <li><a href="#about" class="text-gray-400 hover:text-white transition-colors">Asesores</a></li>
                        <li><a href="#contact" class="text-gray-400 hover:text-white transition-colors">Contacto</a></li>
                    </ul>
                </div>
                <!-- Servicios -->
                <div>
                    <h5 class="font-bold text-lg mb-4">Servicios</h5>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Marketing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Desarrollo Web</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Diseño Gráfico</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Fotografía</a></li>
                    </ul>
                </div>
                <!-- Newsletter -->
                <div>
                    <h5 class="font-bold text-lg mb-4">Suscríbete</h5>
                    <p class="text-gray-400 mb-4">Recibe las últimas noticias y ofertas especiales.</p>
                    <form>
                        <div class="flex">
                            <input type="email" placeholder="Tu email" class="w-full px-4 py-2 rounded-l-lg bg-gray-800 border border-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-r-lg transition-colors">
                                <i class="ph ph-paper-plane-tilt"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-500 text-sm">
                <p>&copy; <span id="year"></span> AsesoraMe. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- ========== JavaScript ========== -->
    <script>
    (function () {
        var mobileMenuButton = document.getElementById('mobile-menu-button');
        var mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }

        var profileBtn = document.getElementById('user-profile-btn');
        var profileMenu = document.getElementById('profile-menu');
        if (profileBtn && profileMenu) {
            profileBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });
            document.addEventListener('click', function () {
                profileMenu.classList.add('hidden');
            });
            profileMenu.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }
        document.getElementById('year').textContent = new Date().getFullYear();
    })();
    </script>
</body>
</html>