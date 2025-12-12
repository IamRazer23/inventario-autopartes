<?php
/**
 * Vista: Historial de Compras
 * Muestra las compras realizadas por el cliente
 * 
 * @author Grupo 1SF131
 */

$pageTitle = $pageTitle ?? 'Historial de Compras';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li><a href="<?= BASE_URL ?>/index.php?module=cliente&action=dashboard" class="hover:text-indigo-600">Mi Panel</a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium">Historial de Compras</li>
        </ol>
    </nav>

    <!-- Título -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-history text-indigo-600 mr-3"></i>Historial de Compras
        </h1>
        <p class="text-gray-600">Revisa todas tus compras realizadas</p>
    </div>

    <?php if (empty($compras)): ?>
        <!-- Sin Compras -->
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-shopping-bag text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">No tienes compras todavía</h3>
            <p class="text-gray-500 mb-6">Cuando realices tu primera compra, aparecerá aquí.</p>
            <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
               class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                <i class="fas fa-search mr-2"></i>Explorar Catálogo
            </a>
        </div>
    <?php else: ?>
        <!-- Lista de Compras -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <!-- Header de la tabla -->
            <div class="hidden md:grid grid-cols-12 gap-4 bg-gray-50 px-6 py-4 font-semibold text-gray-600 text-sm border-b">
                <div class="col-span-2">N° Orden</div>
                <div class="col-span-3">Fecha</div>
                <div class="col-span-2 text-center">Productos</div>
                <div class="col-span-2 text-center">Total</div>
                <div class="col-span-2 text-center">Estado</div>
                <div class="col-span-1 text-center">Acción</div>
            </div>
            
            <!-- Lista de compras -->
            <div class="divide-y">
                <?php foreach ($compras as $compra): ?>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 px-6 py-4 hover:bg-gray-50 transition items-center">
                    <!-- N° Orden -->
                    <div class="md:col-span-2">
                        <span class="md:hidden text-gray-500 mr-2">Orden:</span>
                        <span class="font-bold text-indigo-600">#<?= str_pad($compra['id'], 6, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    
                    <!-- Fecha -->
                    <div class="md:col-span-3">
                        <span class="md:hidden text-gray-500 mr-2">Fecha:</span>
                        <span class="text-gray-700">
                            <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>
                            <?= formatDateTime($compra['fecha_venta']) ?>
                        </span>
                    </div>
                    
                    <!-- Productos -->
                    <div class="md:col-span-2 text-center">
                        <span class="md:hidden text-gray-500 mr-2">Productos:</span>
                        <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                            <?= $compra['total_items'] ?> item(s)
                        </span>
                    </div>
                    
                    <!-- Total -->
                    <div class="md:col-span-2 text-center">
                        <span class="md:hidden text-gray-500 mr-2">Total:</span>
                        <span class="font-bold text-gray-800"><?= formatCurrency($compra['total']) ?></span>
                    </div>
                    
                    <!-- Estado -->
                    <div class="md:col-span-2 text-center">
                        <span class="md:hidden text-gray-500 mr-2">Estado:</span>
                        <?php if ($compra['estado'] === 'completada'): ?>
                            <span class="inline-flex items-center bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-check-circle mr-1"></i>Completada
                            </span>
                        <?php elseif ($compra['estado'] === 'cancelada'): ?>
                            <span class="inline-flex items-center bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-times-circle mr-1"></i>Cancelada
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-medium">
                                <i class="fas fa-clock mr-1"></i>Pendiente
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Acción -->
                    <div class="md:col-span-1 text-center">
                        <a href="<?= BASE_URL ?>/index.php?module=carrito&action=detalle_compra&id=<?= $compra['id'] ?>" 
                           class="inline-flex items-center justify-center bg-indigo-100 hover:bg-indigo-200 text-indigo-600 p-2 rounded-lg transition"
                           title="Ver detalle">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Resumen -->
        <div class="mt-6 bg-white rounded-xl shadow-md p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-4 bg-indigo-50 rounded-lg">
                    <p class="text-3xl font-bold text-indigo-600"><?= count($compras) ?></p>
                    <p class="text-gray-600">Compras Realizadas</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <?php 
                    $totalGastado = array_sum(array_column($compras, 'total'));
                    ?>
                    <p class="text-3xl font-bold text-green-600"><?= formatCurrency($totalGastado) ?></p>
                    <p class="text-gray-600">Total Gastado</p>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <?php 
                    $totalProductos = array_sum(array_column($compras, 'total_items'));
                    ?>
                    <p class="text-3xl font-bold text-blue-600"><?= $totalProductos ?></p>
                    <p class="text-gray-600">Productos Comprados</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>