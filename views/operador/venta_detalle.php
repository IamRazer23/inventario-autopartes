<?php
/**
 * Vista: Detalle de Venta - Operador (Solo Lectura)
 * Muestra el detalle completo de una venta
 */

$pageTitle = $pageTitle ?? 'Detalle de Venta';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-receipt text-blue-600 mr-2"></i>
                Venta #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?>
            </h1>
            <p class="text-gray-600">
                <i class="fas fa-lock text-yellow-500 mr-1"></i>
                Vista de solo lectura
            </p>
        </div>
        <div class="flex gap-2 mt-4 md:mt-0">
            <a href="<?= BASE_URL ?>/index.php?module=operador&action=ventas" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i> Volver
            </a>
            <button onclick="window.print()" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-print mr-2"></i> Imprimir
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información de la Venta -->
        <div class="lg:col-span-2">
            <!-- Detalles de productos -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Productos de la Venta
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Producto</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($detalles as $detalle): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <?php if (!empty($detalle['thumbnail'])): ?>
                                                <img src="<?= e($detalle['thumbnail']) ?>" 
                                                     alt="<?= e($detalle['autoparte_nombre']) ?>"
                                                     class="w-12 h-12 object-cover rounded-lg mr-4">
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg mr-4 flex items-center justify-center">
                                                    <i class="fas fa-car text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="font-medium text-gray-900">
                                                    <?= e($detalle['autoparte_nombre'] ?? 'Producto') ?>
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    <?= e($detalle['marca'] ?? '') ?> <?= e($detalle['modelo'] ?? '') ?>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?= $detalle['cantidad'] ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-900">
                                        <?= formatCurrency($detalle['precio_unitario'] ?? 0) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                        <?= formatCurrency(($detalle['precio_unitario'] ?? 0) * ($detalle['cantidad'] ?? 0)) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Totales -->
                <div class="bg-gray-50 px-6 py-4">
                    <div class="flex justify-end">
                        <div class="w-64">
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">
                                    <?= formatCurrency(($venta['total'] ?? 0) / (1 + ITBMS_RATE)) ?>
                                </span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">ITBMS (<?= ITBMS_RATE * 100 ?>%):</span>
                                <span class="font-medium">
                                    <?= formatCurrency(($venta['total'] ?? 0) - (($venta['total'] ?? 0) / (1 + ITBMS_RATE))) ?>
                                </span>
                            </div>
                            <div class="flex justify-between py-2 border-t-2 border-gray-300 text-lg">
                                <span class="font-bold text-gray-800">Total:</span>
                                <span class="font-bold text-green-600">
                                    <?= formatCurrency($venta['total'] ?? 0) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Panel Lateral -->
        <div class="lg:col-span-1">
            <!-- Info de la Venta -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Información de la Venta
                </h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID Venta:</span>
                        <span class="font-medium">#<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Fecha:</span>
                        <span class="font-medium"><?= formatDateTime($venta['fecha_venta'] ?? '') ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Estado:</span>
                        <?php
                        $estado = $venta['estado'] ?? 'completada';
                        $estadoClases = [
                            'pendiente' => 'bg-yellow-100 text-yellow-800',
                            'procesando' => 'bg-blue-100 text-blue-800',
                            'completada' => 'bg-green-100 text-green-800',
                            'cancelada' => 'bg-red-100 text-red-800'
                        ];
                        $clase = $estadoClases[$estado] ?? 'bg-gray-100 text-gray-800';
                        ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $clase ?>">
                            <?= ucfirst($estado) ?>
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Items:</span>
                        <span class="font-medium"><?= count($detalles) ?> productos</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t">
                        <span class="text-gray-800 font-semibold">Total:</span>
                        <span class="text-lg font-bold text-green-600">
                            <?= formatCurrency($venta['total'] ?? 0) ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Info del Cliente -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-user text-purple-600 mr-2"></i>
                    Información del Cliente
                </h3>
                
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-800">
                            <?= e($venta['cliente_nombre'] ?? 'Cliente') ?>
                        </p>
                        <p class="text-sm text-gray-500">Cliente</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-envelope text-gray-400 w-5"></i>
                        <span class="ml-2 text-gray-600">
                            <?= e($venta['cliente_email'] ?? 'Sin email') ?>
                        </span>
                    </div>
                    <?php if (!empty($venta['cliente_telefono'])): ?>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-phone text-gray-400 w-5"></i>
                            <span class="ml-2 text-gray-600">
                                <?= e($venta['cliente_telefono']) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estilos para impresión -->
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white; }
        .shadow-md { box-shadow: none !important; }
    }
</style>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
