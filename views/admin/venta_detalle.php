<?php
/**
 * Vista: Detalle de Venta - Administrador
 */

$pageTitle = $pageTitle ?? 'Detalle de Venta';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>/index.php?module=admin&action=dashboard" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li><a href="<?= BASE_URL ?>/index.php?module=admin&action=ventas" class="hover:text-indigo-600">Ventas</a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium">#<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></li>
        </ol>
    </nav>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-receipt text-indigo-600 mr-3"></i>
                Orden #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?>
            </h1>
            <p class="text-gray-600">
                <?= formatDateTime($venta['fecha_venta']) ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            <button onclick="window.print()" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                <i class="fas fa-print mr-2"></i>Imprimir
            </button>
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=ventas" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Información del cliente -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-indigo-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-user mr-2"></i>Datos del Cliente
                    </h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 bg-indigo-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">
                                <?= strtoupper(substr($venta['cliente'], 0, 1)) ?>
                            </span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($venta['cliente']) ?></h4>
                            <p class="text-sm text-gray-500"><?= htmlspecialchars($venta['cliente_email']) ?></p>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Estado</span>
                            <?php
                            $estadoClass = match($venta['estado'] ?? 'completada') {
                                'completada' => 'bg-green-100 text-green-700',
                                'pendiente' => 'bg-yellow-100 text-yellow-700',
                                'cancelada' => 'bg-red-100 text-red-700',
                                default => 'bg-gray-100 text-gray-700'
                            };
                            ?>
                            <span class="px-3 py-1 rounded-full text-sm font-medium <?= $estadoClass ?>">
                                <?= ucfirst($venta['estado'] ?? 'completada') ?>
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Fecha</span>
                            <span class="font-medium"><?= formatDate($venta['fecha_venta']) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Hora</span>
                            <span class="font-medium"><?= date('H:i', strtotime($venta['fecha_venta'])) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Resumen de pago -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden mt-6">
                <div class="bg-green-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-calculator mr-2"></i>Resumen de Pago
                    </h3>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium"><?= formatCurrency($venta['subtotal']) ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">ITBMS (7%)</span>
                        <span class="font-medium"><?= formatCurrency($venta['itbms']) ?></span>
                    </div>
                    <hr>
                    <div class="flex justify-between text-lg">
                        <span class="font-bold text-gray-800">Total</span>
                        <span class="font-bold text-green-600"><?= formatCurrency($venta['total']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-box mr-2"></i>Productos (<?= count($detalles) ?>)
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <?php foreach ($detalles as $detalle): ?>
                            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                                <?php if ($detalle['thumbnail']): ?>
                                    <img src="<?= UPLOADS_URL ?>/<?= htmlspecialchars($detalle['thumbnail']) ?>" 
                                         alt="<?= htmlspecialchars($detalle['autoparte_nombre']) ?>"
                                         class="w-20 h-20 object-cover rounded-lg">
                                <?php else: ?>
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-car text-gray-400 text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($detalle['autoparte_nombre']) ?></h4>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($detalle['marca']) ?> <?= htmlspecialchars($detalle['modelo']) ?></p>
                                    <p class="text-sm text-indigo-600">
                                        <?= formatCurrency($detalle['precio_unitario']) ?> x <?= $detalle['cantidad'] ?>
                                    </p>
                                </div>
                                
                                <div class="text-right">
                                    <p class="font-bold text-gray-800"><?= formatCurrency($detalle['subtotal']) ?></p>
                                    <p class="text-sm text-gray-500"><?= $detalle['cantidad'] ?> unidad(es)</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Notas -->
            <?php if (!empty($venta['notas'])): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden mt-6">
                <div class="bg-gray-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-sticky-note mr-2"></i>Notas
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600"><?= nl2br(htmlspecialchars($venta['notas'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media print {
    nav, button, .no-print { display: none !important; }
    .shadow-md { box-shadow: none !important; }
}
</style>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>