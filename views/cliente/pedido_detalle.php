<?php
/**
 * Vista: Detalle de Pedido
 * Muestra la información completa de un pedido específico
 */

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm">
        <ol class="flex items-center space-x-2">
            <li><a href="<?= BASE_URL ?>" class="text-blue-600 hover:text-blue-800"><i class="fas fa-home"></i></a></li>
            <li><span class="text-gray-400 mx-2">/</span></li>
            <li><a href="<?= BASE_URL ?>?controller=cliente" class="text-blue-600 hover:text-blue-800">Mi Panel</a></li>
            <li><span class="text-gray-400 mx-2">/</span></li>
            <li><a href="<?= BASE_URL ?>?controller=cliente&action=pedidos" class="text-blue-600 hover:text-blue-800">Mis Pedidos</a></li>
            <li><span class="text-gray-400 mx-2">/</span></li>
            <li class="text-gray-600">Pedido #<?= str_pad($venta['id'], 8, '0', STR_PAD_LEFT) ?></li>
        </ol>
    </nav>

    <div class="max-w-4xl mx-auto">
        
        <!-- Header del Pedido -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-white">
                        <i class="fas fa-receipt mr-2"></i>
                        Pedido #<?= str_pad($venta['id'], 8, '0', STR_PAD_LEFT) ?>
                    </h1>
                    <span class="px-4 py-2 rounded-full text-sm font-semibold
                        <?php if ($venta['estado'] === 'completada'): ?>
                            bg-green-100 text-green-700
                        <?php elseif ($venta['estado'] === 'cancelada'): ?>
                            bg-red-100 text-red-700
                        <?php else: ?>
                            bg-yellow-100 text-yellow-700
                        <?php endif; ?>">
                        <i class="fas fa-<?= $venta['estado'] === 'completada' ? 'check-circle' : ($venta['estado'] === 'cancelada' ? 'times-circle' : 'clock') ?> mr-1"></i>
                        <?= ucfirst($venta['estado']) ?>
                    </span>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-gray-500 text-sm">Fecha del Pedido</p>
                        <p class="font-semibold text-gray-800">
                            <i class="fas fa-calendar text-blue-500 mr-1"></i>
                            <?= date('d \d\e F \d\e Y', strtotime($venta['fecha_venta'])) ?>
                        </p>
                        <p class="text-gray-600 text-sm"><?= date('H:i', strtotime($venta['fecha_venta'])) ?> hrs</p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total de Productos</p>
                        <p class="font-semibold text-gray-800">
                            <i class="fas fa-box text-blue-500 mr-1"></i>
                            <?= count($detalles) ?> producto<?= count($detalles) > 1 ? 's' : '' ?>
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Pagado</p>
                        <p class="text-2xl font-bold text-green-600">
                            $<?= number_format($venta['total'], 2) ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos del Pedido -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-box-open text-blue-500 mr-2"></i>
                    Productos del Pedido
                </h2>
            </div>
            
            <div class="divide-y divide-gray-200">
                <?php foreach ($detalles as $detalle): ?>
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-4">
                            <?php if ($detalle['thumbnail']): ?>
                                <img src="<?= UPLOADS_URL ?>/thumbs/<?= e($detalle['thumbnail']) ?>" 
                                     alt="<?= e($detalle['nombre']) ?>"
                                     class="w-20 h-20 object-cover rounded-lg shadow">
                            <?php else: ?>
                                <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-car-side text-gray-400 text-2xl"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="flex-1">
                                <a href="<?= BASE_URL ?>?controller=catalogo&action=detalle&id=<?= $detalle['autoparte_id'] ?>" 
                                   class="text-lg font-semibold text-gray-800 hover:text-blue-600 transition-colors">
                                    <?= e($detalle['nombre']) ?>
                                </a>
                                <p class="text-gray-600 text-sm">
                                    <?= e($detalle['marca']) ?> <?= e($detalle['modelo']) ?> (<?= $detalle['anio'] ?>)
                                </p>
                                <p class="text-gray-500 text-xs">
                                    <i class="fas fa-tag mr-1"></i><?= e($detalle['categoria']) ?>
                                </p>
                            </div>
                            
                            <div class="text-center px-4">
                                <p class="text-gray-500 text-sm">Precio Unit.</p>
                                <p class="font-semibold text-gray-800">$<?= number_format($detalle['precio_unitario'], 2) ?></p>
                            </div>
                            
                            <div class="text-center px-4">
                                <p class="text-gray-500 text-sm">Cantidad</p>
                                <p class="font-semibold text-gray-800"><?= $detalle['cantidad'] ?></p>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-gray-500 text-sm">Subtotal</p>
                                <p class="text-lg font-bold text-gray-800">$<?= number_format($detalle['subtotal'], 2) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Resumen de Totales -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                    <i class="fas fa-calculator text-blue-500 mr-2"></i>
                    Resumen del Pago
                </h2>
            </div>
            
            <div class="p-6">
                <div class="max-w-xs ml-auto space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal:</span>
                        <span>$<?= number_format($venta['subtotal'], 2) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>ITBMS (7%):</span>
                        <span>$<?= number_format($venta['itbms'], 2) ?></span>
                    </div>
                    <hr class="my-2">
                    <div class="flex justify-between text-xl font-bold text-gray-800">
                        <span>Total:</span>
                        <span class="text-green-600">$<?= number_format($venta['total'], 2) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="<?= BASE_URL ?>?controller=cliente&action=pedidos" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Volver a Mis Pedidos
            </a>
            <button onclick="window.print()" 
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                <i class="fas fa-print mr-2"></i>
                Imprimir Recibo
            </button>
            <a href="<?= BASE_URL ?>?controller=catalogo" 
               class="inline-flex items-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Seguir Comprando
            </a>
        </div>
    </div>
</div>

<!-- Estilos para impresión -->
<style>
@media print {
    header, footer, nav, .no-print {
        display: none !important;
    }
    body {
        background: white;
    }
    .container {
        max-width: 100%;
        padding: 0;
    }
    .shadow-lg {
        box-shadow: none;
    }
    a {
        color: inherit !important;
        text-decoration: none !important;
    }
}
</style>
