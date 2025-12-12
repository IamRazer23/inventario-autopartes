<?php
/**
 * Vista: Confirmación de Compra
 * Muestra la confirmación exitosa del pedido
 * 
 * @author Grupo 1SF131
 */

$pageTitle = $pageTitle ?? 'Compra Confirmada';
require_once VIEWS_PATH . '/layouts/header.php';

$venta = $compra['venta'];
$detalle = $compra['detalle'];
?>

<div class="container mx-auto px-4 py-8">
    <!-- Pasos del Checkout -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center text-green-600">
                <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-check"></i>
                </div>
                <span class="ml-2 font-medium hidden sm:inline">Carrito</span>
            </div>
            <div class="w-16 h-1 bg-green-600 rounded"></div>
            <div class="flex items-center text-green-600">
                <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-check"></i>
                </div>
                <span class="ml-2 font-medium hidden sm:inline">Revisión</span>
            </div>
            <div class="w-16 h-1 bg-green-600 rounded"></div>
            <div class="flex items-center text-green-600">
                <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-check"></i>
                </div>
                <span class="ml-2 font-medium hidden sm:inline">Confirmación</span>
            </div>
        </div>
    </div>

    <!-- Mensaje de Éxito -->
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header de Éxito -->
            <div class="bg-gradient-to-r from-green-500 to-green-600 text-white text-center py-8">
                <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-check text-green-500 text-4xl"></i>
                </div>
                <h1 class="text-3xl font-bold mb-2">¡Compra Exitosa!</h1>
                <p class="text-green-100">Tu pedido ha sido procesado correctamente</p>
            </div>
            
            <!-- Detalles del Pedido -->
            <div class="p-6 md:p-8">
                <!-- Número de Orden -->
                <div class="text-center mb-8 p-4 bg-gray-50 rounded-lg">
                    <p class="text-gray-600 text-sm mb-1">Número de Orden</p>
                    <p class="text-3xl font-bold text-indigo-600">#<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?></p>
                    <p class="text-gray-500 text-sm mt-2">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        <?= formatDateTime($venta['fecha_venta']) ?>
                    </p>
                </div>
                
                <!-- Resumen de Productos -->
                <div class="mb-6">
                    <h2 class="font-bold text-gray-800 mb-4">
                        <i class="fas fa-box text-indigo-600 mr-2"></i>Productos Adquiridos
                    </h2>
                    <div class="border rounded-lg divide-y">
                        <?php foreach ($detalle as $item): ?>
                        <div class="flex items-center p-4">
                            <?php if ($item['imagen_thumb']): ?>
                                <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($item['imagen_thumb']) ?>" 
                                     alt="<?= htmlspecialchars($item['nombre']) ?>" 
                                     class="w-14 h-14 object-cover rounded-lg border mr-4">
                            <?php else: ?>
                                <div class="w-14 h-14 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-car text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800"><?= htmlspecialchars($item['nombre']) ?></h3>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($item['marca']) ?> <?= htmlspecialchars($item['modelo']) ?> (<?= $item['anio'] ?>)
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500"><?= $item['cantidad'] ?> x <?= formatCurrency($item['precio_unitario']) ?></p>
                                <p class="font-bold text-gray-800"><?= formatCurrency($item['subtotal']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Resumen de Pago -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h2 class="font-bold text-gray-800 mb-4">
                        <i class="fas fa-receipt text-indigo-600 mr-2"></i>Resumen de Pago
                    </h2>
                    <div class="space-y-2">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span><?= formatCurrency($venta['subtotal']) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>ITBMS (7%)</span>
                            <span><?= formatCurrency($venta['itbms']) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Envío</span>
                            <span class="text-green-600">Gratis</span>
                        </div>
                        <hr class="border-gray-300">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-800">Total Pagado</span>
                            <span class="text-indigo-600"><?= formatCurrency($venta['total']) ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Información Importante -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-bold text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-2"></i>Información Importante
                    </h3>
                    <ul class="text-blue-700 text-sm space-y-2">
                        <li><i class="fas fa-envelope mr-2"></i>Recibirás un correo de confirmación con los detalles de tu pedido.</li>
                        <li><i class="fas fa-phone mr-2"></i>Nos comunicaremos contigo para coordinar la entrega.</li>
                        <li><i class="fas fa-truck mr-2"></i>Tiempo estimado de entrega: 2-5 días hábiles.</li>
                    </ul>
                </div>
                
                <!-- Botones de Acción -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="<?= BASE_URL ?>/index.php?module=carrito&action=historial" 
                       class="inline-flex items-center justify-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-history mr-2"></i>Ver Mis Compras
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
                       class="inline-flex items-center justify-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg transition">
                        <i class="fas fa-shopping-bag mr-2"></i>Seguir Comprando
                    </a>
                </div>
                
                <!-- Imprimir -->
                <div class="text-center mt-6">
                    <button onclick="window.print()" class="text-gray-500 hover:text-gray-700 text-sm">
                        <i class="fas fa-print mr-1"></i>Imprimir Comprobante
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Contacto -->
        <div class="text-center mt-6 text-gray-500 text-sm">
            <p>¿Tienes alguna pregunta sobre tu pedido?</p>
            <p><i class="fas fa-phone mr-1"></i>+507 6123-4567 | <i class="fas fa-envelope mr-1"></i>info@autopartes.com</p>
        </div>
    </div>
</div>

<!-- Estilos para impresión -->
<style>
@media print {
    header, nav, footer, .no-print {
        display: none !important;
    }
    body {
        background: white;
    }
    .shadow-lg, .shadow-md {
        box-shadow: none !important;
    }
}
</style>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>