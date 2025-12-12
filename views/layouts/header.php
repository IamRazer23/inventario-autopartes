<?php
/**
 * Header del layout público
 * Diseño con Tailwind CSS y menús horizontales
 * Cumple con requisito 10: CSS y menús horizontales
 * 
 * @author Grupo 1SF131
 */

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema de Inventario de Autopartes - Encuentra las piezas que necesitas">
    <meta name="author" content="Grupo 1SF131">
    
    <title><?= $pageTitle ?? 'AutoPartes Pro - Sistema de Inventario' ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        /* Animaciones personalizadas */
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .animate-slide-down {
            animation: slideDown 0.3s ease-out;
        }
        
        /* Dropdown hover effect */
        .dropdown:hover .dropdown-menu {
            display: block;
        }
        
        /* Cards hover */
        .card-hover {
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        /* Flash messages */
        .flash-message {
            animation: slideDown 0.3s ease-out;
        }
    </style>
    
    <?php if (isset($customStyles)): ?>
        <style><?= $customStyles ?></style>
    <?php endif; ?>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    
    <!-- Top Bar -->
    <div class="bg-gray-800 text-white text-xs py-2 hidden md:block">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <span><i class="fas fa-phone mr-1"></i> +507 6123-4567</span>
                <span><i class="fas fa-envelope mr-1"></i> info@autopartes.com</span>
            </div>
            <div class="flex items-center space-x-4">
                <span><i class="fas fa-clock mr-1"></i> Lun-Vie: 8:00 AM - 6:00 PM</span>
            </div>
        </div>
    </div>

    <!-- Header Principal -->
    <header class="bg-white shadow-md sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between py-4">
                
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <a href="<?= BASE_URL ?>/index.php" class="flex items-center space-x-3 group">
                        <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-blue-500 rounded-lg flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-car-side text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">
                                AutoPartes Pro
                            </h1>
                            <p class="text-xs text-gray-500">Sistema de Inventario</p>
                        </div>
                    </a>
                </div>

                <!-- Barra de búsqueda -->
                <div class="hidden lg:flex flex-1 max-w-xl mx-8">
                    <form action="<?= BASE_URL ?>/index.php" method="GET" class="w-full">
                        <input type="hidden" name="module" value="publico">
                        <input type="hidden" name="action" value="catalogo">
                        <div class="relative">
                            <input 
                                type="text" 
                                name="buscar"
                                placeholder="Buscar autopartes (marca, modelo, año)..." 
                                class="w-full px-4 py-2 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                                value="<?= htmlspecialchars($_GET['buscar'] ?? '') ?>"
                            >
                            <button 
                                type="submit"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-1.5 rounded-md transition-colors"
                            >
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Menú de usuario -->
                <nav class="flex items-center space-x-4">
                    
                    <?php if (isAuthenticated()): ?>
                        
                        <?php if (hasRole(ROL_CLIENTE)): ?>
    <!-- Carrito -->
    <a href="<?= BASE_URL ?>/index.php?module=carrito&action=ver" class="relative group flex items-center">
        <div class="relative">
            <i class="fas fa-shopping-cart text-gray-600 group-hover:text-indigo-600 text-xl transition-colors"></i>
            <?php $totalItems = $_SESSION['carrito_items'] ?? 0; ?>
            <span id="cart-count" 
                  class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold <?= $totalItems == 0 ? 'hidden' : '' ?>">
                <?= $totalItems ?>
            </span>
        </div>
        <span class="hidden md:inline-block ml-2 text-sm text-gray-600 group-hover:text-indigo-600 transition-colors">Carrito</span>
    </a>
<?php endif; ?>

                        <!-- Dropdown usuario -->
                        <div class="relative dropdown">
                            <button class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-sm">
                                        <?= strtoupper(substr($_SESSION['usuario_nombre'] ?? 'U', 0, 1)) ?>
                                    </span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-gray-700"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['rol_nombre'] ?? 'Rol') ?></p>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400 hidden md:block"></i>
                            </button>
                            <div class="dropdown-menu hidden absolute right-0 mt-1 w-56 bg-white rounded-lg shadow-xl border border-gray-100 py-2 z-50">
                                <?php 
                                // Determinar el módulo del usuario según su rol
                                $userModule = 'cliente';
                                if (hasRole(ROL_ADMINISTRADOR)) $userModule = 'admin';
                                elseif (hasRole(ROL_OPERADOR)) $userModule = 'operador';
                                ?>
                                
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($_SESSION['usuario_email'] ?? '') ?></p>
                                </div>
                                
                                <?php if (hasRole(ROL_ADMINISTRADOR) || hasRole(ROL_OPERADOR)): ?>
                                <a href="<?= BASE_URL ?>/index.php?module=<?= $userModule ?>&action=dashboard" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    <i class="fas fa-tachometer-alt mr-2 w-4"></i>Panel de Control
                                </a>
                                <?php endif; ?>
                                
                                <a href="<?= BASE_URL ?>/index.php?module=<?= $userModule ?>&action=perfil" 
                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                    <i class="fas fa-user mr-2 w-4"></i>Mi Perfil
                                </a>
                                
                                <hr class="my-2 border-gray-200">
                                
                                <a href="<?= BASE_URL ?>/index.php?module=auth&action=logout" 
                                   class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <i class="fas fa-sign-out-alt mr-2 w-4"></i>Cerrar Sesión
                                </a>
                            </div>
                        </div>

                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/index.php?module=auth&action=login" 
                           class="hidden md:flex items-center space-x-2 text-gray-600 hover:text-indigo-600 transition-colors">
                            <i class="fas fa-sign-in-alt"></i>
                            <span class="text-sm font-medium">Iniciar Sesión</span>
                        </a>
                        
                        <a href="<?= BASE_URL ?>/index.php?module=auth&action=registro" 
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            <i class="fas fa-user-plus mr-2"></i>Registrarse
                        </a>
                    <?php endif; ?>

                    <!-- Botón móvil -->
                    <button id="mobile-menu-button" class="lg:hidden text-gray-600 hover:text-indigo-600 transition-colors" onclick="toggleMobileMenu()">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </nav>
            </div>
        </div>

        <!-- Menú móvil -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-200 animate-slide-down">
            <div class="container mx-auto px-4 py-4 space-y-3">
                <form action="<?= BASE_URL ?>/index.php" method="GET">
                    <input type="hidden" name="module" value="publico">
                    <input type="hidden" name="action" value="catalogo">
                    <input type="text" name="buscar" placeholder="Buscar autopartes..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                </form>
                <a href="<?= BASE_URL ?>/index.php" class="block py-2 text-gray-700 hover:text-indigo-600"><i class="fas fa-home mr-2 w-5"></i> Inicio</a>
                <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" class="block py-2 text-gray-700 hover:text-indigo-600"><i class="fas fa-th-large mr-2 w-5"></i> Catálogo</a>
                <?php if (!isAuthenticated()): ?>
                    <hr class="border-gray-200">
                    <a href="<?= BASE_URL ?>/index.php?module=auth&action=login" class="block text-center py-2 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Menú de navegación horizontal -->
    <nav class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between">
                <ul class="flex items-center space-x-1">
                    <li>
                        <a href="<?= BASE_URL ?>/index.php" class="flex items-center px-4 py-3 hover:bg-white/20 rounded-t-lg transition-colors <?= ($_GET['action'] ?? '') == '' || ($_GET['action'] ?? '') == 'inicio' ? 'bg-white/20' : '' ?>">
                            <i class="fas fa-home mr-2"></i><span>Inicio</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" class="flex items-center px-4 py-3 hover:bg-white/20 rounded-t-lg transition-colors <?= ($_GET['action'] ?? '') == 'catalogo' ? 'bg-white/20' : '' ?>">
                            <i class="fas fa-th-large mr-2"></i><span>Catálogo</span>
                        </a>
                    </li>
                    
                    <!-- Dropdown Categorías -->
                    <li class="relative dropdown">
                        <button class="flex items-center px-4 py-3 hover:bg-white/20 rounded-t-lg transition-colors">
                            <i class="fas fa-tags mr-2"></i><span>Categorías</span><i class="fas fa-chevron-down ml-2 text-xs"></i>
                        </button>
                        <div class="dropdown-menu hidden absolute left-0 mt-0 w-48 bg-white rounded-b-lg shadow-xl py-2 z-50">
                            <?php 
                            try {
                                $db = Database::getInstance();
                                $cats = $db->fetchAll("SELECT id, nombre FROM categorias WHERE estado = 1 ORDER BY nombre LIMIT 8");
                                foreach ($cats as $cat): 
                            ?>
                            <a href="<?= BASE_URL ?>/index.php?module=publico&action=categoria&id=<?= $cat['id'] ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </a>
                            <?php endforeach; } catch (Exception $e) {} ?>
                            <hr class="my-1 border-gray-200">
                            <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" class="block px-4 py-2 text-sm text-indigo-600 font-medium hover:bg-indigo-50 transition-colors">
                                Ver todas <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </li>
                    
                    <li>
                        <a href="#contacto" class="flex items-center px-4 py-3 hover:bg-white/20 rounded-t-lg transition-colors">
                            <i class="fas fa-envelope mr-2"></i><span>Contacto</span>
                        </a>
                    </li>
                </ul>
                
                <div class="hidden lg:flex items-center space-x-4 text-sm">
                    <span class="flex items-center"><i class="fas fa-truck mr-2"></i>Envíos a todo Panamá</span>
                    <span class="flex items-center"><i class="fab fa-whatsapp mr-2"></i>+507 6123-4567</span>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main -->
    <main class="flex-1">
        <?php
        // CORRECCIÓN: Verificar si flash_message es un array y acceder correctamente
        if (isset($_SESSION['flash_message'])):
            // Soportar ambos formatos: array con type/message o string directo
            if (is_array($_SESSION['flash_message'])) {
                $type = $_SESSION['flash_message']['type'] ?? 'info';
                $message = $_SESSION['flash_message']['message'] ?? '';
            } else {
                // Formato antiguo: string directo
                $type = $_SESSION['flash_type'] ?? 'info';
                $message = $_SESSION['flash_message'];
            }
            
            $alertConfig = [
                'success' => ['bg' => 'bg-green-100', 'border' => 'border-green-400', 'text' => 'text-green-700', 'icon' => 'check-circle'],
                'error' => ['bg' => 'bg-red-100', 'border' => 'border-red-400', 'text' => 'text-red-700', 'icon' => 'exclamation-circle'],
                'warning' => ['bg' => 'bg-yellow-100', 'border' => 'border-yellow-400', 'text' => 'text-yellow-700', 'icon' => 'exclamation-triangle'],
                'info' => ['bg' => 'bg-blue-100', 'border' => 'border-blue-400', 'text' => 'text-blue-700', 'icon' => 'info-circle']
            ];
            $config = $alertConfig[$type] ?? $alertConfig['info'];
        ?>
            <div class="container mx-auto px-4 mt-4">
                <div class="flash-message <?= $config['bg'] ?> border <?= $config['border'] ?> <?= $config['text'] ?> px-4 py-3 rounded-lg flex items-center justify-between" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-<?= $config['icon'] ?> mr-3"></i>
                        <span><?= htmlspecialchars($message) ?></span>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-xl leading-none hover:opacity-75">&times;</button>
                </div>
            </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); endif; ?>

    <script>
        function toggleMobileMenu() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        }
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobile-menu');
            const button = document.getElementById('mobile-menu-button');
            if (menu && button && !menu.contains(event.target) && !button.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>