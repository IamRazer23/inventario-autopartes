<?php
/**
 * Vista: Checkout - Finalizar Compra
 * Permite al cliente revisar y confirmar su pedido
 * 
 * @author Grupo 1SF131
 */

$pageTitle = $pageTitle ?? 'Finalizar Compra';
require_once VIEWS_PATH . '/layouts/header.php';

$usuario = currentUser();
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li><a href="<?= BASE_URL ?>/index.php?module=carrito&action=ver" class="hover:text-indigo-600">Carrito</a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium">Finalizar Compra</li>
        </ol>
    </nav>

    <!-- Pasos del Checkout -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center text-green-600">
                <div class="w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center text-sm font-bold">
                    <i class="fas fa-check"></i>
                </div>
                <span class="ml-2 font-medium hidden sm:inline">Carrito</span>
            </div>
            <div class="w-16 h-1 bg-indigo-600 rounded"></div>
            <div class="flex items-center text-indigo-600">
                <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-bold">2</div>
                <span class="ml-2 font-medium hidden sm:inline">Revisión</span>
            </div>
            <div class="w-16 h-1 bg-gray-300 rounded"></div>
            <div class="flex items-center text-gray-400">
                <div class="w-8 h-8 rounded-full bg-gray-300 text-white flex items-center justify-center text-sm font-bold">3</div>
                <span class="ml-2 font-medium hidden sm:inline">Confirmación</span>
            </div>
        </div>
    </div>

    <!-- Título -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-credit-card text-indigo-600 mr-3"></i>Finalizar Compra
        </h1>
        <p class="text-gray-600">Revisa tu pedido antes de confirmar</p>
    </div>

    <form action="<?= BASE_URL ?>/index.php?module=carrito&action=procesar" method="POST" id="form-checkout">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
        
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Información del Pedido -->
            <div class="lg:w-2/3 space-y-6">
                
                <!-- Datos del Cliente -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="font-bold text-lg text-gray-800">
                            <i class="fas fa-user text-indigo-600 mr-2"></i>Datos del Cliente
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                                <p class="text-gray-800 font-medium"><?= htmlspecialchars($usuario['nombre']) ?></p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Email</label>
                                <p class="text-gray-800"><?= htmlspecialchars($usuario['email']) ?></p>
                            </div>
                        </div>
                        <p class="mt-4 text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            La confirmación de tu pedido será enviada a tu correo electrónico.
                        </p>
                    </div>
                </div>

                <!-- Productos del Pedido -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="font-bold text-lg text-gray-800">
                            <i class="fas fa-box text-indigo-600 mr-2"></i>Productos (<?= count($items) ?>)
                        </h2>
                    </div>
                    <div class="divide-y">
                        <?php foreach ($items as $item): ?>
                        <div class="flex items-center p-4 hover:bg-gray-50">
                            <?php if ($item['imagen_thumb']): ?>
                                <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($item['imagen_thumb']) ?>" 
                                     alt="<?= htmlspecialchars($item['nombre']) ?>" 
                                     class="w-16 h-16 object-cover rounded-lg border mr-4">
                            <?php else: ?>
                                <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                    <i class="fas fa-car text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-800"><?= htmlspecialchars($item['nombre']) ?></h3>
                                <p class="text-sm text-gray-500">
                                    <?= htmlspecialchars($item['marca']) ?> <?= htmlspecialchars($item['modelo']) ?> (<?= $item['anio'] ?>)
                                </p>
                                <p class="text-sm text-gray-500">Cantidad: <?= $item['cantidad'] ?></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500"><?= formatCurrency($item['precio']) ?> c/u</p>
                                <p class="font-bold text-indigo-600"><?= formatCurrency($item['subtotal']) ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 text-right">
                        <a href="<?= BASE_URL ?>/index.php?module=carrito&action=ver" class="text-indigo-600 hover:text-indigo-800 font-medium">
                            <i class="fas fa-edit mr-1"></i>Modificar carrito
                        </a>
                    </div>
                </div>

                <!-- Notas del Pedido (Opcional) -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="bg-gray-50 px-6 py-4 border-b">
                        <h2 class="font-bold text-lg text-gray-800">
                            <i class="fas fa-comment text-indigo-600 mr-2"></i>Notas (Opcional)
                        </h2>
                    </div>
                    <div class="p-6">
                        <textarea name="notas" rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                                  placeholder="Agrega cualquier nota o instrucción especial para tu pedido..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Resumen del Pago -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-xl shadow-md overflow-hidden sticky top-24">
                    <div class="bg-indigo-600 text-white px-6 py-4">
                        <h2 class="font-bold text-lg"><i class="fas fa-receipt mr-2"></i>Resumen del Pago</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal (<?= $totales['total_productos'] ?> productos)</span>
                            <span class="font-medium"><?= formatCurrency($totales['subtotal']) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>ITBMS (7%)</span>
                            <span class="font-medium"><?= formatCurrency($totales['itbms']) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>Envío</span>
                            <span class="font-medium text-green-600">Gratis</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-xl font-bold">
                            <span class="text-gray-800">Total a Pagar</span>
                            <span class="text-indigo-600"><?= formatCurrency($totales['total']) ?></span>
                        </div>
                        
                        <!-- Método de Pago -->
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-700 mb-3">Método de Pago</p>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-indigo-50 transition">
                                    <input type="radio" name="metodo_pago" value="transferencia" class="text-indigo-600" checked>
                                    <span class="ml-3">
                                        <i class="fas fa-university text-indigo-600 mr-2"></i>Transferencia Bancaria
                                    </span>
                                </label>
                                <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-indigo-50 transition">
                                    <input type="radio" name="metodo_pago" value="efectivo" class="text-indigo-600">
                                    <span class="ml-3">
                                        <i class="fas fa-money-bill-wave text-green-600 mr-2"></i>Pago en Efectivo
                                    </span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Términos y Condiciones -->
                        <div class="mt-4">
                            <label class="flex items-start cursor-pointer">
                                <input type="checkbox" name="acepta_terminos" id="acepta_terminos" 
                                       class="mt-1 text-indigo-600 rounded" required>
                                <span class="ml-2 text-sm text-gray-600">
                                    Acepto los <a href="#" class="text-indigo-600 hover:underline">términos y condiciones</a> 
                                    y la <a href="#" class="text-indigo-600 hover:underline">política de privacidad</a>.
                                </span>
                            </label>
                        </div>
                        
                        <!-- Botón Confirmar -->
                        <button type="submit" id="btn-confirmar" 
                                class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold py-4 px-4 rounded-lg text-center transition mt-4">
                            <i class="fas fa-check-circle mr-2"></i>Confirmar Compra
                        </button>
                        
                        <!-- Volver -->
                        <a href="<?= BASE_URL ?>/index.php?module=carrito&action=ver" 
                           class="block w-full text-center text-gray-600 hover:text-gray-800 py-2 transition">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al Carrito
                        </a>
                        
                        <!-- Seguridad -->
                        <div class="mt-4 text-center text-sm text-gray-500">
                            <p><i class="fas fa-lock text-green-500 mr-1"></i>Transacción 100% segura</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('form-checkout');
    const btnConfirmar = document.getElementById('btn-confirmar');
    const checkboxTerminos = document.getElementById('acepta_terminos');
    
    // Habilitar/deshabilitar botón según términos
    checkboxTerminos.addEventListener('change', function() {
        btnConfirmar.disabled = !this.checked;
    });
    
    // Inicializar estado del botón
    btnConfirmar.disabled = !checkboxTerminos.checked;
    
    // Confirmación antes de enviar
    form.addEventListener('submit', function(e) {
        if (!checkboxTerminos.checked) {
            e.preventDefault();
            alert('Debe aceptar los términos y condiciones');
            return;
        }
        
        if (!confirm('¿Está seguro de confirmar esta compra?')) {
            e.preventDefault();
            return;
        }
        
        // Deshabilitar botón para evitar doble envío
        btnConfirmar.disabled = true;
        btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Procesando...';
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>