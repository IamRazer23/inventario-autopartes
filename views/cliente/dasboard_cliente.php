<?php
/**
 * Vista: Dashboard del Cliente
 * Panel principal para clientes con resumen de compras
 */

$pageTitle = 'Mi Panel';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header del Dashboard -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-user-circle text-blue-600 mr-2"></i>
            Bienvenido, <?= e($_SESSION['usuario_nombre']) ?>
        </h1>
        <p class="text-gray-600">
            <span class="text-sm"><?= date('l, d \d\e F \d\e Y') ?></span>
        </p>
    </div>

    <!-- Cards de Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        
        <!-- Total Compras -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Compras</p>
                    <h3 class="text-4xl font-bold"><?= $totalCompras ?></h3>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-shopping-bag mr-1"></i>
                        Pedidos realizados
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-shopping-bag text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Gastado -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Total Gastado</p>
                    <h3 class="text-4xl font-bold">$<?= number_format($totalGastado, 2) ?></h3>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        En todas tus compras
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-wallet text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Carrito -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">En el Carrito</p>
                    <h3 class="text-4xl font-bold"><?= $itemsCarrito ?></h3>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-shopping-cart mr-1"></i>
                        Productos pendientes
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-shopping-cart text-3xl"></i>
                </div>
            </div>
            <?php if ($itemsCarrito > 0): ?>
                <a href="<?= BASE_URL ?>?controller=carrito" 
                   class="block mt-4 text-center bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg text-sm font-semibold transition">
                    Ver Carrito →
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Grid de 2 Columnas -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Últimas Compras -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Últimas Compras
                </h3>
            </div>
            
            <div class="p-6">
                <?php if (!empty($ultimasCompras)): ?>
                    <div class="space-y-3">
                        <?php foreach ($ultimasCompras as $compra): ?>
                            <a href="<?= BASE_URL ?>?controller=cliente&action=pedido&id=<?= $compra['id'] ?>" 
                               class="block p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-gray-800">
                                            Pedido #<?= str_pad($compra['id'], 8, '0', STR_PAD_LEFT) ?>
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($compra['fecha_venta'])) ?>
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            <?= $compra['total_items'] ?> producto<?= $compra['total_items'] > 1 ? 's' : '' ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-bold text-green-600">
                                            $<?= number_format($compra['total'], 2) ?>
                                        </span>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="<?= BASE_URL ?>?controller=cliente&action=pedidos" 
                           class="text-blue-600 hover:text-blue-800 font-semibold text-sm flex items-center justify-center">
                            Ver todas las compras
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-shopping-bag text-5xl opacity-50 mb-3"></i>
                        <p class="font-semibold">No tienes compras aún</p>
                        <p class="text-sm">¡Explora nuestro catálogo!</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Productos Más Comprados -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-star mr-2"></i>
                    Tus Productos Favoritos
                </h3>
            </div>
            
            <div class="p-6">
                <?php if (!empty($autopartesTop)): ?>
                    <div class="space-y-3">
                        <?php foreach ($autopartesTop as $autoparte): ?>
                            <a href="<?= BASE_URL ?>?controller=catalogo&action=detalle&id=<?= $autoparte['id'] ?>" 
                               class="flex items-center space-x-4 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <?php if ($autoparte['thumbnail']): ?>
                                    <img src="<?= UPLOADS_URL ?>/thumbs/<?= e($autoparte['thumbnail']) ?>" 
                                         alt="<?= e($autoparte['nombre']) ?>"
                                         class="w-12 h-12 object-cover rounded-lg">
                                <?php else: ?>
                                    <div class="w-12 h-12 bg-gray-300 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-car-side text-gray-500"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 truncate">
                                        <?= e($autoparte['nombre']) ?>
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        <?= e($autoparte['marca']) ?> <?= e($autoparte['modelo']) ?>
                                    </p>
                                </div>
                                
                                <div class="text-right">
                                    <span class="text-sm text-gray-500">
                                        Comprado <?= $autoparte['total_comprado'] ?>x
                                    </span>
                                    <p class="text-green-600 font-semibold">
                                        $<?= number_format($autoparte['precio'], 2) ?>
                                    </p>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-star text-5xl opacity-50 mb-3"></i>
                        <p class="font-semibold">Sin productos favoritos</p>
                        <p class="text-sm">Realiza tu primera compra</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Accesos Rápidos -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-semibold text-white mb-6">
            <i class="fas fa-bolt mr-2"></i>
            Accesos Rápidos
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="<?= BASE_URL ?>?controller=catalogo" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-search text-3xl mb-2"></i>
                <p class="font-semibold">Ver Catálogo</p>
            </a>
            
            <a href="<?= BASE_URL ?>?controller=carrito" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                <p class="font-semibold">Mi Carrito</p>
            </a>
            
            <a href="<?= BASE_URL ?>?controller=cliente&action=pedidos" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-history text-3xl mb-2"></i>
                <p class="font-semibold">Mis Pedidos</p>
            </a>
            
            <a href="<?= BASE_URL ?>?controller=cliente&action=perfil" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-user-cog text-3xl mb-2"></i>
                <p class="font-semibold">Mi Perfil</p>
            </a>
        </div>
    </div>
</div>
