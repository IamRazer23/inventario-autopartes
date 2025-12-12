<?php
/**
 * Vista: Detalle de Autoparte (Público)
 * MODIFICADO: Las imágenes se cargan directamente desde URLs externas
 */

$pageTitle = htmlspecialchars($autoparte['nombre']) . ' - AutoPartes Pro';

// Imagen por defecto
$defaultImage = 'https://via.placeholder.com/800x600?text=Sin+Imagen';

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
                    <?php if (!empty($autoparte['imagen_grande'])): ?>
                        <img src="<?= htmlspecialchars($autoparte['imagen_grande']) ?>" 
                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                             class="w-full h-96 object-contain bg-gray-100 cursor-zoom-in"
                             id="imagen-principal"
                             onclick="abrirModal()"
                             onerror="this.onerror=null; this.src='<?= $defaultImage ?>';">
                    <?php elseif (!empty($autoparte['thumbnail'])): ?>
                        <img src="<?= htmlspecialchars($autoparte['thumbnail']) ?>" 
                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                             class="w-full h-96 object-contain bg-gray-100"
                             id="imagen-principal"
                             onerror="this.onerror=null; this.src='<?= $defaultImage ?>';">
                    <?php else: ?>
                        <div class="w-full h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                            <i class="fas fa-car text-gray-400 text-8xl"></i>
                        </div>
                    <?php endif; ?>
                    
                    <span class="absolute top-4 left-4 bg-indigo-600 text-white text-sm font-bold px-3 py-1 rounded-full">
                        <?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?>
                    </span>
                </div>
                
                <?php if (!empty($autoparte['thumbnail']) && !empty($autoparte['imagen_grande'])): ?>
                <div class="p-4 bg-gray-50 flex gap-2">
                    <img src="<?= htmlspecialchars($autoparte['thumbnail']) ?>" 
                         class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-500 transition"
                         onclick="cambiarImagen('<?= htmlspecialchars($autoparte['thumbnail']) ?>')"
                         onerror="this.style.display='none'">
                    <img src="<?= htmlspecialchars($autoparte['imagen_grande']) ?>" 
                         class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-500 transition"
                         onclick="cambiarImagen('<?= htmlspecialchars($autoparte['imagen_grande']) ?>')"
                         onerror="this.style.display='none'">
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna de información -->
        <div class="space-y-6">
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

            <?php if ($autoparte['stock'] > 0): ?>
            <div class="bg-white rounded-xl shadow-md p-6">
                <?php if (isAuthenticated()): ?>
                    <form id="form-carrito" class="space-y-4">
                        <input type="hidden" name="autoparte_id" value="<?= $autoparte['id'] ?>">
                        <div class="flex items-center gap-4">
                            <label class="text-gray-700 font-medium">Cantidad:</label>
                            <div class="flex items-center border border-gray-300 rounded-lg">
                                <button type="button" onclick="cambiarCantidad(-1)" class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" name="cantidad" id="cantidad" value="1" min="1" max="<?= $autoparte['stock'] ?>"
                                       class="w-16 text-center border-0 focus:ring-0">
                                <button type="button" onclick="cambiarCantidad(1)" class="px-4 py-2 text-gray-600 hover:bg-gray-100 transition">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg transition flex items-center justify-center">
                            <i class="fas fa-cart-plus mr-2 text-xl"></i>Agregar al Carrito
                        </button>
                    </form>
                    <div id="mensaje-carrito" class="mt-3"></div>
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

            <?php if (!empty($autoparte['descripcion'])): ?>
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">
                    <i class="fas fa-info-circle text-indigo-500 mr-2"></i>Descripción
                </h3>
                <p class="text-gray-600 leading-relaxed">
                    <?= nl2br(htmlspecialchars($autoparte['descripcion'])) ?>
                </p>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-list text-indigo-500 mr-2"></i>Especificaciones
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="text-gray-500 block">Marca</span>
                        <span class="font-semibold text-gray-800"><?= htmlspecialchars($autoparte['marca']) ?></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="text-gray-500 block">Modelo</span>
                        <span class="font-semibold text-gray-800"><?= htmlspecialchars($autoparte['modelo']) ?></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="text-gray-500 block">Año</span>
                        <span class="font-semibold text-gray-800"><?= $autoparte['anio'] ?></span>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <span class="text-gray-500 block">Categoría</span>
                        <span class="font-semibold text-gray-800"><?= htmlspecialchars($autoparte['categoria_nombre']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comentarios -->
    <div class="mt-12">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-comments mr-2"></i>Comentarios (<?= count($comentarios) ?>)
                </h2>
            </div>
            
            <div class="p-6">
                <?php if (isAuthenticated()): ?>
                <div class="mb-8 pb-6 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-4">Deja tu comentario</h3>
                    <form id="form-comentario">
                        <input type="hidden" name="autoparte_id" value="<?= $autoparte['id'] ?>">
                        <textarea name="comentario" rows="3" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 resize-none"
                            placeholder="Comparte tu opinión..."></textarea>
                        <div class="flex justify-end mt-3">
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                                <i class="fas fa-paper-plane mr-2"></i>Enviar
                            </button>
                        </div>
                    </form>
                    <div id="mensaje-comentario" class="mt-3"></div>
                </div>
                <?php else: ?>
                <div class="mb-8 pb-6 border-b border-gray-200 text-center">
                    <p class="text-gray-600">
                        <a href="<?= BASE_URL ?>/index.php?module=auth&action=login" class="text-indigo-600 hover:underline font-semibold">Inicia sesión</a> para comentar
                    </p>
                </div>
                <?php endif; ?>
                
                <?php if (empty($comentarios)): ?>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-comment-slash text-4xl mb-3 opacity-50"></i>
                    <p>Aún no hay comentarios. ¡Sé el primero!</p>
                </div>
                <?php else: ?>
                <div class="space-y-6">
                    <?php foreach ($comentarios as $comentario): ?>
                    <div class="flex gap-4">
                        <div class="w-12 h-12 bg-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-white font-bold"><?= strtoupper(substr($comentario['usuario_nombre'], 0, 1)) ?></span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between items-start mb-2">
                                <p class="font-semibold text-gray-800"><?= htmlspecialchars($comentario['usuario_nombre']) ?></p>
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
                <?php if (!empty($rel['imagen_thumb'])): ?>
                    <img src="<?= htmlspecialchars($rel['imagen_thumb']) ?>" 
                         alt="<?= htmlspecialchars($rel['nombre']) ?>"
                         class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-300"
                         onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-full h-40 bg-gray-200 flex items-center justify-center\'><i class=\'fas fa-car text-gray-400 text-3xl\'></i></div>';">
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
        <img id="modal-img" src="" alt="Imagen ampliada" class="max-w-full max-h-[90vh] object-contain">
    </div>
    <button class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300">&times;</button>
</div>

<script>
function cambiarCantidad(delta) {
    const input = document.getElementById('cantidad');
    let valor = parseInt(input.value) + delta;
    if (valor < 1) valor = 1;
    if (valor > parseInt(input.max)) valor = parseInt(input.max);
    input.value = valor;
}

function cambiarImagen(src) {
    document.getElementById('imagen-principal').src = src;
}

function abrirModal() {
    const imgSrc = document.getElementById('imagen-principal').src;
    document.getElementById('modal-img').src = imgSrc;
    document.getElementById('modal-imagen').classList.remove('hidden');
    document.getElementById('modal-imagen').classList.add('flex');
}

function cerrarModal() {
    document.getElementById('modal-imagen').classList.add('hidden');
    document.getElementById('modal-imagen').classList.remove('flex');
}

// Carrito AJAX
document.getElementById('form-carrito')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= BASE_URL ?>/index.php?module=carrito&action=agregar', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const msg = document.getElementById('mensaje-carrito');
        if (data.success) {
            msg.innerHTML = '<div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg"><i class="fas fa-check-circle mr-2"></i>' + data.message + '</div>';
            if (document.getElementById('cart-count')) {
                document.getElementById('cart-count').textContent = data.total_items;
            }
        } else {
            msg.innerHTML = '<div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>';
        }
    });
});

// Comentarios AJAX
document.getElementById('form-comentario')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('<?= BASE_URL ?>/index.php?module=publico&action=comentar', {
        method: 'POST',
        body: formData,
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(response => response.json())
    .then(data => {
        const msg = document.getElementById('mensaje-comentario');
        if (data.success) {
            msg.innerHTML = '<div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg"><i class="fas fa-check-circle mr-2"></i>' + data.message + '</div>';
            this.querySelector('textarea').value = '';
        } else {
            msg.innerHTML = '<div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg"><i class="fas fa-exclamation-circle mr-2"></i>' + data.message + '</div>';
        }
    });
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>