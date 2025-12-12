<?php
/**
 * Vista: Detalle de Autoparte (Público)
 * Cumple con requisito 9: Detalle con imagen, costo, unidades y comentarios
 */

$pageTitle = htmlspecialchars($autoparte['nombre']) . ' - AutoPartes Pro';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li><a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" class="hover:text-indigo-600">Catálogo</a></li>
            <?php if ($autoparte['categoria_nombre']): ?>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li><a href="<?= BASE_URL ?>/index.php?module=publico&action=categoria&id=<?= $autoparte['categoria_id'] ?>" class="hover:text-indigo-600"><?= htmlspecialchars($autoparte['categoria_nombre']) ?></a></li>
            <?php endif; ?>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium truncate max-w-xs"><?= htmlspecialchars($autoparte['nombre']) ?></li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Columna de imagen -->
        <div>
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="relative">
                    <?php if ($autoparte['imagen_grande']): ?>
                        <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_grande']) ?>" 
                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                             class="w-full h-96 object-contain bg-gray-100 cursor-zoom-in"
                             id="imagen-principal"
                             onclick="abrirModal()">
                    <?php elseif ($autoparte['imagen_thumb']): ?>
                        <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_thumb']) ?>" 
                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                             class="w-full h-96 object-contain bg-gray-100">
                    <?php else: ?>
                        <div class="w-full h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                            <i class="fas fa-car text-gray-400 text-8xl"></i>
                        </div>
                    <?php endif; ?>
                    
                    <span class="absolute top-4 left-4 bg-indigo-600 text-white text-sm font-bold px-3 py-1 rounded-full">
                        <?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?>
                    </span>
                </div>
                
                <?php if ($autoparte['imagen_thumb'] && $autoparte['imagen_grande']): ?>
                <div class="p-4 bg-gray-50 flex gap-2">
                    <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_thumb']) ?>" 
                         class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-500 transition"
                         onclick="cambiarImagen('<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_thumb']) ?>')">
                    <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_grande']) ?>" 
                         class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-500 transition"
                         onclick="cambiarImagen('<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_grande']) ?>')">
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna de información -->
        <div class="space-y-6">
            <!-- Título -->
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-3"><?= htmlspecialchars($autoparte['nombre']) ?></h1>
                <div class="flex flex-wrap gap-2">
                    <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-car mr-1"></i><?= htmlspecialchars($autoparte['marca']) ?>
                    </span>
                    <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-medium">
                        <?= htmlspecialchars($autoparte['modelo']) ?>
                    </span>
                    <span class="bg-gray-800 text-white px-3 py-1 rounded-full text-sm font-medium">
                        <?= $autoparte['anio'] ?>
                    </span>
                </div>
            </div>

            <!-- Precio y Stock -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Precio</p>
                        <p class="text-4xl font-bold text-indigo-600">$<?= number_format($autoparte['precio'], 2) ?></p>
                        <p class="text-xs text-gray-500">ITBMS no incluido</p>
                    </div>
                    <div class="text-right">
                        <?php if ($autoparte['stock'] > 0): ?>
                            <div class="text-green-600">
                                <i class="fas fa-check-circle text-3xl"></i>
                                <p class="font-bold">Disponible</p>
                                <p class="text-sm"><?= $autoparte['stock'] ?> en stock</p>
                            </div>
                        <?php else: ?>
                            <div class="text-red-600">
                                <i class="fas fa-times-circle text-3xl"></i>
                                <p class="font-bold">Agotado</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Agregar al carrito -->
            <?php if ($autoparte['stock'] > 0): ?>
            <div class="bg-white rounded-xl shadow-md p-6">
                <?php if (isAuthenticated() && hasRole(ROL_CLIENTE)): ?>
                    <div class="space-y-4">
                        <input type="hidden" id="autoparte-id" value="<?= $autoparte['id'] ?>">
                        <input type="hidden" id="stock-max" value="<?= $autoparte['stock'] ?>">
                        
                        <div class="flex items-center gap-4">
                            <label class="text-gray-700 font-medium">Cantidad:</label>
                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                <button type="button" id="btn-decrementar" 
                                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition focus:outline-none">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="cantidad" value="1" min="1" max="<?= $autoparte['stock'] ?>"
                                       class="w-16 text-center border-0 focus:ring-0 focus:outline-none"
                                       readonly>
                                <button type="button" id="btn-incrementar"
                                        class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition focus:outline-none">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        
                        <button type="button" id="btn-agregar-carrito"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-cart-plus mr-2 text-xl"></i>Agregar al Carrito
                        </button>
                    </div>
                    <div id="mensaje-carrito" class="mt-3"></div>
                <?php elseif (isAuthenticated()): ?>
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">
                            <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                            Solo los clientes pueden agregar productos al carrito
                        </p>
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">
                            <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                            Inicia sesión para agregar al carrito
                        </p>
                        <a href="<?= BASE_URL ?>/index.php?module=auth&action=login" 
                           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg transition">
                            <i class="fas fa-sign-in-alt mr-2"></i>Iniciar Sesión
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="bg-red-50 border border-red-200 rounded-xl p-6 text-center">
                <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-2"></i>
                <p class="text-red-700 font-medium">Este producto no está disponible actualmente</p>
            </div>
            <?php endif; ?>

            <!-- Descripción -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="font-bold text-lg text-gray-800 mb-3">
                    <i class="fas fa-info-circle text-indigo-500 mr-2"></i>Descripción
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    <?= nl2br(htmlspecialchars($autoparte['descripcion'] ?? 'Sin descripción disponible.')) ?>
                </p>
            </div>

            <!-- Especificaciones -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="font-bold text-lg text-gray-800 mb-3">
                    <i class="fas fa-list-ul text-indigo-500 mr-2"></i>Especificaciones
                </h2>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="py-2 text-gray-500">Marca del vehículo</td>
                            <td class="py-2 text-gray-800 text-right font-medium"><?= htmlspecialchars($autoparte['marca']) ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-500">Modelo</td>
                            <td class="py-2 text-gray-800 text-right font-medium"><?= htmlspecialchars($autoparte['modelo']) ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-500">Año</td>
                            <td class="py-2 text-gray-800 text-right font-medium"><?= $autoparte['anio'] ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-500">Categoría</td>
                            <td class="py-2 text-gray-800 text-right font-medium"><?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?></td>
                        </tr>
                        <tr>
                            <td class="py-2 text-gray-500">Stock disponible</td>
                            <td class="py-2 text-right font-medium <?= $autoparte['stock'] > 0 ? 'text-green-600' : 'text-red-600' ?>">
                                <?= $autoparte['stock'] ?> unidades
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sección de Comentarios -->
    <div class="mt-12 bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="font-bold text-xl text-gray-800">
                <i class="fas fa-comments text-indigo-500 mr-2"></i>Comentarios y Opiniones
            </h2>
        </div>
        
        <div class="p-6">
            <!-- Formulario de comentario -->
            <?php if (isAuthenticated()): ?>
            <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-4">Deja tu opinión</h3>
                <form id="form-comentario" class="space-y-4">
                    <input type="hidden" name="autoparte_id" value="<?= $autoparte['id'] ?>">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Calificación</label>
                        <div class="rating-stars flex gap-1">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="calificacion" value="<?= $i ?>" id="star<?= $i ?>" class="hidden" <?= $i == 5 ? 'checked' : '' ?>>
                            <label for="star<?= $i ?>" class="cursor-pointer text-2xl text-gray-300 hover:text-yellow-400 transition">
                                <i class="fas fa-star"></i>
                            </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div>
                        <textarea name="comentario" rows="3" required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Escribe tu comentario..."></textarea>
                    </div>
                    
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        <i class="fas fa-paper-plane mr-2"></i>Enviar Comentario
                    </button>
                </form>
                <div id="mensaje-comentario" class="mt-3"></div>
            </div>
            <?php else: ?>
            <div class="mb-8 p-4 bg-gray-50 rounded-lg text-center">
                <p class="text-gray-600">
                    <a href="<?= BASE_URL ?>/index.php?module=auth&action=login" class="text-indigo-600 hover:underline font-medium">Inicia sesión</a> 
                    para dejar un comentario.
                </p>
            </div>
            <?php endif; ?>

            <!-- Lista de comentarios -->
            <?php if (empty($comentarios)): ?>
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-comment-slash text-4xl mb-3 text-gray-300"></i>
                <p>Aún no hay comentarios. ¡Sé el primero en opinar!</p>
            </div>
            <?php else: ?>
            <div class="space-y-6">
                <?php foreach ($comentarios as $comentario): ?>
                <div class="flex gap-4">
                    <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white font-bold"><?= strtoupper(substr($comentario['usuario_nombre'] ?? 'A', 0, 1)) ?></span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($comentario['usuario_nombre'] ?? 'Anónimo') ?></p>
                                <div class="flex text-yellow-400 text-sm">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?= $i <= ($comentario['calificacion'] ?? 0) ? '' : 'text-gray-300' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <span class="text-sm text-gray-500"><?= date('d/m/Y', strtotime($comentario['fecha_creacion'])) ?></span>
                        </div>
                        <p class="text-gray-600"><?= nl2br(htmlspecialchars($comentario['comentario'])) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Productos relacionados -->
    <?php if (!empty($relacionadas)): ?>
    <div class="mt-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
            <i class="fas fa-th-large text-indigo-500 mr-2"></i>Productos Relacionados
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($relacionadas as $rel): ?>
            <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $rel['id'] ?>" 
               class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all overflow-hidden group">
                <?php if ($rel['imagen_thumb']): ?>
                    <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($rel['imagen_thumb']) ?>" 
                         alt="<?= htmlspecialchars($rel['nombre']) ?>"
                         class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300">
                <?php else: ?>
                    <div class="w-full h-40 bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-car text-gray-400 text-3xl"></i>
                    </div>
                <?php endif; ?>
                <div class="p-4">
                    <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition line-clamp-1"><?= htmlspecialchars($rel['nombre']) ?></h3>
                    <p class="text-lg font-bold text-indigo-600 mt-2">$<?= number_format($rel['precio'], 2) ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Modal de imagen -->
<div id="modal-imagen" class="fixed inset-0 bg-black/80 z-50 hidden items-center justify-center p-4" onclick="cerrarModal()">
    <div class="max-w-4xl max-h-full">
        <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_grande'] ?? $autoparte['imagen_thumb'] ?? '') ?>" 
             alt="<?= htmlspecialchars($autoparte['nombre']) ?>" class="max-w-full max-h-[90vh] object-contain">
    </div>
    <button class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">&times;</button>
</div>

<style>
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #fbbf24 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = '<?= BASE_URL ?>';
    
    // =========================================================================
    // FUNCIONALIDAD DE CANTIDAD
    // =========================================================================
    const inputCantidad = document.getElementById('cantidad');
    const btnDecrementar = document.getElementById('btn-decrementar');
    const btnIncrementar = document.getElementById('btn-incrementar');
    const stockMax = parseInt(document.getElementById('stock-max')?.value || 1);
    
    if (btnDecrementar) {
        btnDecrementar.addEventListener('click', function() {
            let valor = parseInt(inputCantidad.value);
            if (valor > 1) {
                inputCantidad.value = valor - 1;
            }
        });
    }
    
    if (btnIncrementar) {
        btnIncrementar.addEventListener('click', function() {
            let valor = parseInt(inputCantidad.value);
            if (valor < stockMax) {
                inputCantidad.value = valor + 1;
            }
        });
    }
    
    // =========================================================================
    // AGREGAR AL CARRITO
    // =========================================================================
    const btnAgregarCarrito = document.getElementById('btn-agregar-carrito');
    
    if (btnAgregarCarrito) {
        btnAgregarCarrito.addEventListener('click', function() {
            const autoparteId = document.getElementById('autoparte-id').value;
            const cantidad = document.getElementById('cantidad').value;
            const mensajeDiv = document.getElementById('mensaje-carrito');
            
            // Deshabilitar botón mientras procesa
            btnAgregarCarrito.disabled = true;
            btnAgregarCarrito.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Agregando...';
            
            // Enviar petición AJAX
            fetch(BASE_URL + '/index.php?module=carrito&action=agregar', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'autoparte_id=' + autoparteId + '&cantidad=' + cantidad
            })
            .then(response => response.json())
            .then(data => {
                // Rehabilitar botón
                btnAgregarCarrito.disabled = false;
                btnAgregarCarrito.innerHTML = '<i class="fas fa-cart-plus mr-2 text-xl"></i>Agregar al Carrito';
                
                if (data.success) {
                    // Mostrar mensaje de éxito
                    mensajeDiv.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between">
                            <span><i class="fas fa-check-circle mr-2"></i>${data.message}</span>
                            <a href="${BASE_URL}/index.php?module=carrito&action=ver" class="text-green-800 font-semibold hover:underline">
                                Ver carrito <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    `;
                    
                    // Actualizar contador del carrito en el header
                    const cartCount = document.getElementById('cart-count');
                    if (cartCount) {
                        cartCount.textContent = data.cart_count;
                        cartCount.classList.remove('hidden');
                        // Animación
                        cartCount.classList.add('animate-bounce');
                        setTimeout(() => cartCount.classList.remove('animate-bounce'), 500);
                    }
                } else {
                    // Mostrar mensaje de error
                    mensajeDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>${data.message}
                        </div>
                    `;
                }
                
                // Ocultar mensaje después de 5 segundos
                setTimeout(() => {
                    mensajeDiv.innerHTML = '';
                }, 5000);
            })
            .catch(error => {
                console.error('Error:', error);
                btnAgregarCarrito.disabled = false;
                btnAgregarCarrito.innerHTML = '<i class="fas fa-cart-plus mr-2 text-xl"></i>Agregar al Carrito';
                
                mensajeDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>Error de conexión. Intente nuevamente.
                    </div>
                `;
            });
        });
    }
    
    // =========================================================================
    // COMENTARIOS
    // =========================================================================
    const formComentario = document.getElementById('form-comentario');
    
    if (formComentario) {
        formComentario.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const mensajeDiv = document.getElementById('mensaje-comentario');
            const submitBtn = this.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';
            
            fetch(BASE_URL + '/index.php?module=publico&action=comentar', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Enviar Comentario';
                
                if (data.success) {
                    mensajeDiv.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i>${data.message}
                        </div>
                    `;
                    formComentario.reset();
                    // Recargar página para ver el comentario (si está aprobado automáticamente)
                    setTimeout(() => location.reload(), 2000);
                } else {
                    mensajeDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Enviar Comentario';
                mensajeDiv.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>Error de conexión
                    </div>
                `;
            });
        });
    }
});

// =========================================================================
// FUNCIONES DE IMAGEN
// =========================================================================
function cambiarImagen(src) {
    document.getElementById('imagen-principal').src = src;
}

function abrirModal() {
    const modal = document.getElementById('modal-imagen');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModal() {
    const modal = document.getElementById('modal-imagen');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>