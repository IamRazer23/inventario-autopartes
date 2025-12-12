<?php
/**
 * Vista: Historial de Pedidos del Cliente
 * Muestra todas las compras realizadas por el cliente
 */

$pageTitle = 'Mis Pedidos';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-shopping-bag text-blue-600 mr-2"></i>
            Mis Pedidos
        </h1>
        <a href="<?= BASE_URL ?>?controller=catalogo" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors">
            <i class="fas fa-plus mr-2"></i>Nueva Compra
        </a>
    </div>

    <?php if (empty($pedidos)): ?>
        <!-- Sin Pedidos -->
        <div class="bg-white rounded-xl shadow-lg p-12 text-center">
            <div class="mb-6">
                <i class="fas fa-box-open text-gray-300 text-8xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-700 mb-4">No tienes pedidos aún</h2>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                ¡Explora nuestro catálogo y encuentra las autopartes que necesitas!
            </p>
            <a href="<?= BASE_URL ?>?controller=catalogo" 
               class="inline-flex items-center px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors shadow-lg">
                <i class="fas fa-search mr-2"></i>
                Explorar Catálogo
            </a>
        </div>
    <?php else: ?>
        
        <!-- Estadísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm">Total de Pedidos</p>
                        <p class="text-3xl font-bold"><?= count($pedidos) ?></p>
                    </div>
                    <i class="fas fa-shopping-bag text-4xl text-blue-200"></i>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm">Total Gastado</p>
                        <p class="text-3xl font-bold">$<?= number_format($totalGastado, 2) ?></p>
                    </div>
                    <i class="fas fa-dollar-sign text-4xl text-green-200"></i>
                </div>
            </div>
            
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm">Último Pedido</p>
                        <p class="text-xl font-bold"><?= date('d/m/Y', strtotime($pedidos[0]['fecha_venta'])) ?></p>
                    </div>
                    <i class="fas fa-calendar text-4xl text-purple-200"></i>
                </div>
            </div>
        </div>

        <!-- Lista de Pedidos -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-history mr-2"></i>
                    Historial de Compras
                </h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                <?php foreach ($pedidos as $pedido): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="text-lg font-bold text-gray-800">
                                        Pedido #<?= str_pad($pedido['id'], 8, '0', STR_PAD_LEFT) ?>
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                                        <?php if ($pedido['estado'] === 'completada'): ?>
                                            bg-green-100 text-green-700
                                        <?php elseif ($pedido['estado'] === 'cancelada'): ?>
                                            bg-red-100 text-red-700
                                        <?php else: ?>
                                            bg-yellow-100 text-yellow-700
                                        <?php endif; ?>">
                                        <?= ucfirst($pedido['estado']) ?>
                                    </span>
                                </div>
                                
                                <p class="text-gray-600 text-sm">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    <?= date('d \d\e F \d\e Y, H:i', strtotime($pedido['fecha_venta'])) ?>
                                </p>
                                
                                <p class="text-gray-500 text-sm mt-1">
                                    <i class="fas fa-box mr-1"></i>
                                    <?= $pedido['total_items'] ?> producto<?= $pedido['total_items'] > 1 ? 's' : '' ?>
                                </p>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">
                                    $<?= number_format($pedido['total'], 2) ?>
                                </p>
                                <p class="text-gray-500 text-xs">
                                    Subtotal: $<?= number_format($pedido['subtotal'], 2) ?> + ITBMS: $<?= number_format($pedido['itbms'], 2) ?>
                                </p>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="<?= BASE_URL ?>?controller=cliente&action=pedido&id=<?= $pedido['id'] ?>" 
                                   class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                    <i class="fas fa-eye mr-1"></i>Ver Detalle
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Paginación -->
        <?php if ($totalPaginas > 1): ?>
            <div class="flex justify-center mt-8">
                <nav class="flex items-center space-x-2">
                    <?php if ($paginaActual > 1): ?>
                        <a href="<?= BASE_URL ?>?controller=cliente&action=pedidos&pagina=<?= $paginaActual - 1 ?>" 
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <a href="<?= BASE_URL ?>?controller=cliente&action=pedidos&pagina=<?= $i ?>" 
                           class="px-4 py-2 rounded-lg <?= $i === $paginaActual ? 'bg-blue-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($paginaActual < $totalPaginas): ?>
                        <a href="<?= BASE_URL ?>?controller=cliente&action=pedidos&pagina=<?= $paginaActual + 1 ?>" 
                           class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
        
    <?php endif; ?>
</div>
