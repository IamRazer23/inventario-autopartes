<?php
/**
 * Vista: Carrito de Compras
 * Muestra los productos en el carrito del cliente
 * 
 * @author Grupo 1SF131
 */

$pageTitle = $pageTitle ?? 'Mi Carrito de Compras';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium">Mi Carrito</li>
        </ol>
    </nav>

    <!-- Título -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-shopping-cart text-indigo-600 mr-3"></i>Mi Carrito de Compras
        </h1>
        <p class="text-gray-600">
            <?php if (!empty($items)): ?>
                Tienes <?= count($items) ?> producto(s) en tu carrito
            <?php else: ?>
                Tu carrito está vacío
            <?php endif; ?>
        </p>
    </div>

    <?php if (empty($items)): ?>
        <!-- Carrito Vacío -->
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-shopping-cart text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">Tu carrito está vacío</h3>
            <p class="text-gray-500 mb-6">Agrega productos desde nuestro catálogo</p>
            <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
               class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-lg transition">
                <i class="fas fa-search mr-2"></i>Explorar Catálogo
            </a>
        </div>
    <?php else: ?>
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Lista de Productos -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <!-- Header de la tabla -->
                    <div class="hidden md:grid grid-cols-12 gap-4 bg-gray-50 px-6 py-4 font-semibold text-gray-600 text-sm border-b">
                        <div class="col-span-5">Producto</div>
                        <div class="col-span-2 text-center">Precio</div>
                        <div class="col-span-2 text-center">Cantidad</div>
                        <div class="col-span-2 text-center">Subtotal</div>
                        <div class="col-span-1 text-center">Acción</div>
                    </div>
                    
                    <!-- Items del carrito -->
                    <div id="cart-items">
                        <?php foreach ($items as $item): ?>
                        <div class="cart-item grid grid-cols-1 md:grid-cols-12 gap-4 px-6 py-4 border-b hover:bg-gray-50 transition items-center" 
                             data-autoparte-id="<?= $item['autoparte_id'] ?>">
                            <!-- Producto -->
                            <div class="md:col-span-5 flex items-center space-x-4">
                                <?php if ($item['imagen_thumb']): ?>
                                    <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($item['imagen_thumb']) ?>" 
                                         alt="<?= htmlspecialchars($item['nombre']) ?>" 
                                         class="w-20 h-20 object-cover rounded-lg border">
                                <?php else: ?>
                                    <div class="w-20 h-20 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-car text-gray-400 text-2xl"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $item['autoparte_id'] ?>" 
                                       class="font-semibold text-gray-800 hover:text-indigo-600 transition block">
                                        <?= htmlspecialchars($item['nombre']) ?>
                                    </a>
                                    <p class="text-sm text-gray-500">
                                        <?= htmlspecialchars($item['marca']) ?> <?= htmlspecialchars($item['modelo']) ?>
                                        <span class="bg-gray-200 text-xs px-2 py-0.5 rounded ml-1"><?= $item['anio'] ?></span>
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        <i class="fas fa-tag mr-1"></i><?= htmlspecialchars($item['categoria_nombre'] ?? 'Sin categoría') ?>
                                    </p>
                                    <?php if ($item['stock'] <= 5): ?>
                                        <span class="text-xs text-orange-600"><i class="fas fa-exclamation-triangle mr-1"></i>Solo <?= $item['stock'] ?> en stock</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Precio unitario -->
                            <div class="md:col-span-2 text-center">
                                <span class="md:hidden text-gray-500 mr-2">Precio:</span>
                                <span class="font-semibold text-gray-700"><?= formatCurrency($item['precio']) ?></span>
                            </div>
                            
                            <!-- Cantidad -->
                            <div class="md:col-span-2 text-center">
                                <div class="inline-flex items-center border rounded-lg overflow-hidden">
                                    <button type="button" 
                                            class="btn-cantidad px-3 py-2 bg-gray-100 hover:bg-gray-200 transition"
                                            data-action="decrease"
                                            data-autoparte-id="<?= $item['autoparte_id'] ?>">
                                        <i class="fas fa-minus text-xs"></i>
                                    </button>
                                    <input type="number" 
                                           class="input-cantidad w-14 text-center py-2 border-x focus:outline-none"
                                           value="<?= $item['cantidad'] ?>"
                                           min="1" max="<?= $item['stock'] ?>"
                                           data-autoparte-id="<?= $item['autoparte_id'] ?>"
                                           data-precio="<?= $item['precio'] ?>">
                                    <button type="button" 
                                            class="btn-cantidad px-3 py-2 bg-gray-100 hover:bg-gray-200 transition"
                                            data-action="increase"
                                            data-autoparte-id="<?= $item['autoparte_id'] ?>"
                                            data-max="<?= $item['stock'] ?>">
                                        <i class="fas fa-plus text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Subtotal -->
                            <div class="md:col-span-2 text-center">
                                <span class="md:hidden text-gray-500 mr-2">Subtotal:</span>
                                <span class="item-subtotal font-bold text-indigo-600"><?= formatCurrency($item['subtotal']) ?></span>
                            </div>
                            
                            <!-- Eliminar -->
                            <div class="md:col-span-1 text-center">
                                <button type="button" 
                                        class="btn-eliminar text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition"
                                        data-autoparte-id="<?= $item['autoparte_id'] ?>"
                                        title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Footer del carrito -->
                    <div class="px-6 py-4 bg-gray-50 flex flex-col sm:flex-row justify-between items-center gap-4">
                        <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
                           class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium transition">
                            <i class="fas fa-arrow-left mr-2"></i>Seguir Comprando
                        </a>
                        <button type="button" id="btn-vaciar-carrito"
                                class="inline-flex items-center text-red-600 hover:text-red-800 font-medium transition">
                            <i class="fas fa-trash-alt mr-2"></i>Vaciar Carrito
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Resumen del Pedido -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-xl shadow-md overflow-hidden sticky top-24">
                    <div class="bg-indigo-600 text-white px-6 py-4">
                        <h2 class="font-bold text-lg"><i class="fas fa-receipt mr-2"></i>Resumen del Pedido</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal (<span id="total-items"><?= $totales['total_productos'] ?></span> productos)</span>
                            <span id="cart-subtotal" class="font-medium"><?= formatCurrency($totales['subtotal']) ?></span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>ITBMS (7%)</span>
                            <span id="cart-itbms" class="font-medium"><?= formatCurrency($totales['itbms']) ?></span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-800">Total</span>
                            <span id="cart-total" class="text-indigo-600"><?= formatCurrency($totales['total']) ?></span>
                        </div>
                        
                        <!-- Botón Checkout -->
                        <a href="<?= BASE_URL ?>/index.php?module=carrito&action=checkout" 
                           class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg text-center transition mt-4">
                            <i class="fas fa-lock mr-2"></i>Proceder al Pago
                        </a>
                        
                        <!-- Información adicional -->
                        <div class="mt-4 space-y-2 text-sm text-gray-500">
                            <p class="flex items-center">
                                <i class="fas fa-shield-alt text-green-500 mr-2"></i>Compra 100% segura
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-truck text-blue-500 mr-2"></i>Envío a todo Panamá
                            </p>
                            <p class="flex items-center">
                                <i class="fas fa-undo text-orange-500 mr-2"></i>Garantía de devolución
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal de Confirmación para Vaciar -->
<div id="modal-vaciar" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 transform transition-all">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">¿Vaciar el carrito?</h3>
            <p class="text-gray-600 mb-6">Esta acción eliminará todos los productos de tu carrito. ¿Estás seguro?</p>
            <div class="flex justify-center space-x-4">
                <button type="button" id="btn-cancelar-vaciar"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                    Cancelar
                </button>
                <button type="button" id="btn-confirmar-vaciar"
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">
                    <i class="fas fa-trash-alt mr-2"></i>Sí, vaciar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ITBMS_RATE = 0.07;
    
    // Funciones de utilidad
    function formatCurrency(amount) {
        return '$' + parseFloat(amount).toFixed(2);
    }
    
    function updateCartCount(count) {
        const cartCountEl = document.getElementById('cart-count');
        if (cartCountEl) {
            cartCountEl.textContent = count;
        }
    }
    
    function updateTotals(totales) {
        document.getElementById('cart-subtotal').textContent = totales.subtotal;
        document.getElementById('cart-itbms').textContent = totales.itbms;
        document.getElementById('cart-total').textContent = totales.total;
    }
    
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white font-medium animate-slide-down`;
        notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'times'}-circle mr-2"></i>${message}`;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Actualizar cantidad
    function actualizarCantidad(autoparteId, cantidad) {
        fetch('<?= BASE_URL ?>/index.php?module=carrito&action=actualizar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `autoparte_id=${autoparteId}&cantidad=${cantidad}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                updateTotals(data.totales);
                
                // Actualizar subtotal del item
                const item = document.querySelector(`.cart-item[data-autoparte-id="${autoparteId}"]`);
                if (item) {
                    const precio = parseFloat(item.querySelector('.input-cantidad').dataset.precio);
                    const subtotal = precio * cantidad;
                    item.querySelector('.item-subtotal').textContent = formatCurrency(subtotal);
                }
                
                showNotification(data.message);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error al actualizar', 'error');
        });
    }
    
    // Eliminar item
    function eliminarItem(autoparteId) {
        fetch('<?= BASE_URL ?>/index.php?module=carrito&action=eliminar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `autoparte_id=${autoparteId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
                
                // Eliminar el elemento del DOM
                const item = document.querySelector(`.cart-item[data-autoparte-id="${autoparteId}"]`);
                if (item) {
                    item.style.transition = 'all 0.3s';
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(100px)';
                    setTimeout(() => {
                        item.remove();
                        
                        // Si no hay más items, recargar la página
                        if (document.querySelectorAll('.cart-item').length === 0) {
                            location.reload();
                        } else {
                            updateTotals(data.totales);
                        }
                    }, 300);
                }
                
                showNotification(data.message);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Error al eliminar', 'error');
        });
    }
    
    // Event Listeners para botones de cantidad
    document.querySelectorAll('.btn-cantidad').forEach(btn => {
        btn.addEventListener('click', function() {
            const autoparteId = this.dataset.autoparteId;
            const input = document.querySelector(`.input-cantidad[data-autoparte-id="${autoparteId}"]`);
            let cantidad = parseInt(input.value);
            const max = parseInt(this.dataset.max || 99);
            
            if (this.dataset.action === 'increase') {
                if (cantidad < max) {
                    cantidad++;
                    input.value = cantidad;
                    actualizarCantidad(autoparteId, cantidad);
                }
            } else {
                if (cantidad > 1) {
                    cantidad--;
                    input.value = cantidad;
                    actualizarCantidad(autoparteId, cantidad);
                }
            }
        });
    });
    
    // Event Listeners para inputs de cantidad
    document.querySelectorAll('.input-cantidad').forEach(input => {
        let timeout;
        input.addEventListener('change', function() {
            const autoparteId = this.dataset.autoparteId;
            let cantidad = parseInt(this.value);
            const max = parseInt(this.max);
            
            if (cantidad < 1) cantidad = 1;
            if (cantidad > max) cantidad = max;
            
            this.value = cantidad;
            
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                actualizarCantidad(autoparteId, cantidad);
            }, 500);
        });
    });
    
    // Event Listeners para botones de eliminar
    document.querySelectorAll('.btn-eliminar').forEach(btn => {
        btn.addEventListener('click', function() {
            const autoparteId = this.dataset.autoparteId;
            if (confirm('¿Eliminar este producto del carrito?')) {
                eliminarItem(autoparteId);
            }
        });
    });
    
    // Modal Vaciar Carrito
    const modalVaciar = document.getElementById('modal-vaciar');
    const btnVaciar = document.getElementById('btn-vaciar-carrito');
    const btnCancelar = document.getElementById('btn-cancelar-vaciar');
    const btnConfirmar = document.getElementById('btn-confirmar-vaciar');
    
    if (btnVaciar) {
        btnVaciar.addEventListener('click', () => {
            modalVaciar.classList.remove('hidden');
        });
    }
    
    if (btnCancelar) {
        btnCancelar.addEventListener('click', () => {
            modalVaciar.classList.add('hidden');
        });
    }
    
    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', () => {
            fetch('<?= BASE_URL ?>/index.php?module=carrito&action=vaciar', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    showNotification(data.message, 'error');
                }
            });
        });
    }
    
    // Cerrar modal al hacer clic fuera
    modalVaciar?.addEventListener('click', (e) => {
        if (e.target === modalVaciar) {
            modalVaciar.classList.add('hidden');
        }
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>