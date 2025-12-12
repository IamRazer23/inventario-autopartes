<?php
/**
 * Vista: Detalle de Compra
 * Muestra el detalle completo de una compra específica
 */

$pageTitle = $pageTitle ?? 'Detalle de Compra';
require_once VIEWS_PATH . '/layouts/header.php';

$venta = $compra['venta'];
$detalle = $compra['detalle'];
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li><a href="<?= BASE_URL ?>/index.php?module=carrito&action=historial" class="hover:text-indigo-600">Historial</a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium">Orden #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></li>
        </ol>
    </nav>

    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-file-invoice text-indigo-600 mr-3"></i>Orden #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?>
                </h1>
                <p class="text-gray-600 mt-1">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    <?= formatDateTime($venta['fecha_venta']) ?>
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="window.print()" class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded-lg transition">
                    <i class="fas fa-print mr-2"></i>Imprimir
                </button>
                <a href="<?= BASE_URL ?>/index.php?module=carrito&action=historial" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Volver
                </a>
            </div>
        </div>

        <!-- Estado de la Orden -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <?php if ($venta['estado'] === 'completada'): ?>
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-check-circle text-green-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800">Orden Completada</h2>
                                <p class="text-gray-500 text-sm">Tu pedido ha sido procesado exitosamente</p>
                            </div>
                        <?php elseif ($venta['estado'] === 'cancelada'): ?>
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800">Orden Cancelada</h2>
                                <p class="text-gray-500 text-sm">Esta orden fue cancelada</p>
                            </div>
                        <?php else: ?>
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-gray-800">Orden Pendiente</h2>
                                <p class="text-gray-500 text-sm">Tu pedido está siendo procesado</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if ($venta['estado'] === 'completada'): ?>
                        <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-bold">
                            Completada
                        </span>
                    <?php elseif ($venta['estado'] === 'cancelada'): ?>
                        <span class="bg-red-100 text-red-700 px-4 py-2 rounded-full text-sm font-bold">
                            Cancelada
                        </span>
                    <?php else: ?>
                        <span class="bg-yellow-100 text-yellow-700 px-4 py-2 rounded-full text-sm font-bold">
                            Pendiente
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="font-bold text-lg text-gray-800">
                    <i class="fas fa-box text-indigo-600 mr-2"></i>Productos (<?= count($detalle) ?>)
                </h2>
            </div>
            <div class="divide-y">
                <?php foreach ($detalle as $item): ?>
                <div class="flex items-center p-4 hover:bg-gray-50 transition">
                    <?php if ($item['imagen_thumb']): ?>
                        <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($item['imagen_thumb']) ?>" 
                             alt="<?= htmlspecialchars($item['nombre']) ?>" 
                             class="w-16 h-16 object-cover rounded-lg border mr-4">
                    <?php else: ?>
                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-car text-gray-400 text-xl"></i>
                        </div>
                    <?php endif; ?>
                    <div class="flex-1">
                        <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $item['autoparte_id'] ?>" 
                           class="font-semibold text-gray-800 hover:text-indigo-600 transition">
                            <?= htmlspecialchars($item['nombre']) ?>
                        </a>
                        <p class="text-sm text-gray-500">
                            <?= htmlspecialchars($item['marca']) ?> <?= htmlspecialchars($item['modelo']) ?> (<?= $item['anio'] ?>)
                        </p>
                        <p class="text-sm text-gray-500">
                            Cantidad: <?= $item['cantidad'] ?> × <?= formatCurrency($item['precio_unitario']) ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-lg text-indigo-600"><?= formatCurrency($item['subtotal']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Resumen de Pago -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="font-bold text-lg text-gray-800">
                    <i class="fas fa-receipt text-indigo-600 mr-2"></i>Resumen de Pago
                </h2>
            </div>
            <div class="p-6">
                <div class="max-w-sm ml-auto space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium"><?= formatCurrency($venta['subtotal']) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>ITBMS (7%)</span>
                        <span class="font-medium"><?= formatCurrency($venta['itbms']) ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Envío</span>
                        <span class="font-medium text-green-600">Gratis</span>
                    </div>
                    <hr class="border-gray-200">
                    <div class="flex justify-between text-xl font-bold">
                        <span class="text-gray-800">Total</span>
                        <span class="text-indigo-600"><?= formatCurrency($venta['total']) ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Contacto -->
        <div class="mt-6 text-center text-gray-500 text-sm">
            <p>¿Tienes alguna pregunta sobre tu pedido?</p>
            <p class="mt-1">
                <i class="fas fa-phone mr-1"></i>+507 6008-6038 | 
                <i class="fas fa-envelope mr-1"></i>info@autopartes.com
            </p>
        </div>
    </div>
</div>

<!-- Estilos para impresión -->
<style>
@media print {
    header, nav, footer, .no-print, button, a[href*="historial"] {
        display: none !important;
    }
    body {
        background: white;
    }
    .shadow-md, .shadow-lg {
        box-shadow: none !important;
        border: 1px solid #e5e7eb;
    }
}
</style>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>