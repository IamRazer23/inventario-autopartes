<?php
/**
 * Vista: Ver Detalle de Autoparte
 * Diseño con Tailwind CSS
 */

require_once VIEWS_PATH . '/layouts/header.php';

// Determinar URL de retorno
$returnUrl = $returnUrl ?? (isset($esOperador) && $esOperador 
    ? '/index.php?module=operador&action=inventario' 
    : '/index.php?module=admin&action=inventario');

$editUrl = isset($esOperador) && $esOperador 
    ? '/index.php?module=operador&action=editar-autoparte&id=' . $autoparte['id']
    : '/index.php?module=admin&action=autoparte-editar&id=' . $autoparte['id'];
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li><a href="<?= BASE_URL . $returnUrl ?>" class="hover:text-indigo-600">Inventario</a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium truncate max-w-xs"><?= htmlspecialchars($autoparte['nombre']) ?></li>
        </ol>
    </nav>

    <!-- Header con acciones -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-cog text-white text-xl"></i>
                </div>
                <?= htmlspecialchars($autoparte['nombre']) ?>
            </h1>
            <p class="text-gray-500 mt-2">Detalle completo de la autoparte</p>
        </div>
        <div class="flex gap-3">
            <?php if (hasPermission('inventario', 'actualizar')): ?>
            <a href="<?= BASE_URL . $editUrl ?>" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i class="fas fa-edit"></i>
                Editar
            </a>
            <?php endif; ?>
            <a href="<?= BASE_URL . $returnUrl ?>" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
                Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Columna de Imagen -->
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="relative">
                    <?php if (!empty($autoparte['imagen_grande'])): ?>
                        <img src="<?= htmlspecialchars($autoparte['imagen_grande']) ?>" 
                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                             class="w-full h-96 object-contain bg-gray-100 cursor-zoom-in"
                             id="imagen-principal"
                             onclick="abrirModal()">
                    <?php elseif (!empty($autoparte['thumbnail'])): ?>
                        <img src="<?= htmlspecialchars($autoparte['thumbnail']) ?>" 
                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                             class="w-full h-96 object-contain bg-gray-100"
                             id="imagen-principal">
                    <?php else: ?>
                        <div class="w-full h-96 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-image text-gray-400 text-6xl mb-3"></i>
                                <p class="text-gray-500">Sin imagen disponible</p>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Badge de estado -->
                    <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-sm font-bold <?= $autoparte['estado'] == 1 ? 'bg-green-500 text-white' : 'bg-gray-500 text-white' ?>">
                        <?= $autoparte['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                    </span>
                    
                    <!-- Badge de categoría -->
                    <?php if (!empty($autoparte['categoria_nombre'])): ?>
                    <span class="absolute top-4 right-4 bg-indigo-600 text-white text-sm font-bold px-3 py-1 rounded-full">
                        <?= htmlspecialchars($autoparte['categoria_nombre']) ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <!-- Miniaturas -->
                <?php if (!empty($autoparte['thumbnail']) || !empty($autoparte['imagen_grande'])): ?>
                <div class="p-4 bg-gray-50 flex gap-2">
                    <?php if (!empty($autoparte['thumbnail'])): ?>
                    <img src="<?= htmlspecialchars($autoparte['thumbnail']) ?>" 
                         class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-500 transition"
                         onclick="cambiarImagen('<?= htmlspecialchars($autoparte['thumbnail']) ?>')">
                    <?php endif; ?>
                    <?php if (!empty($autoparte['imagen_grande'])): ?>
                    <img src="<?= htmlspecialchars($autoparte['imagen_grande']) ?>" 
                         class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 border-transparent hover:border-indigo-500 transition"
                         onclick="cambiarImagen('<?= htmlspecialchars($autoparte['imagen_grande']) ?>')">
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna de Información -->
        <div class="space-y-6">
            
            <!-- Precio y Stock -->
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl p-6">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Precio</p>
                        <p class="text-4xl font-bold text-indigo-600">$<?= number_format($autoparte['precio'], 2) ?></p>
                        <p class="text-xs text-gray-500 mt-1">ITBMS no incluido</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500 mb-1">Stock Disponible</p>
                        <?php if ($autoparte['stock'] > 5): ?>
                            <p class="text-3xl font-bold text-green-600"><?= $autoparte['stock'] ?></p>
                            <p class="text-sm text-green-600"><i class="fas fa-check-circle mr-1"></i>Disponible</p>
                        <?php elseif ($autoparte['stock'] > 0): ?>
                            <p class="text-3xl font-bold text-amber-600"><?= $autoparte['stock'] ?></p>
                            <p class="text-sm text-amber-600"><i class="fas fa-exclamation-triangle mr-1"></i>Stock bajo</p>
                        <?php else: ?>
                            <p class="text-3xl font-bold text-red-600">0</p>
                            <p class="text-sm text-red-600"><i class="fas fa-times-circle mr-1"></i>Sin stock</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Información del Vehículo -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-car"></i>
                        Información del Vehículo
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-xl text-center">
                            <p class="text-xs text-gray-500 mb-1">Marca</p>
                            <p class="font-bold text-gray-800"><?= htmlspecialchars($autoparte['marca']) ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl text-center">
                            <p class="text-xs text-gray-500 mb-1">Modelo</p>
                            <p class="font-bold text-gray-800"><?= htmlspecialchars($autoparte['modelo']) ?></p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl text-center">
                            <p class="text-xs text-gray-500 mb-1">Año</p>
                            <p class="font-bold text-gray-800"><?= $autoparte['anio'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <?php if (!empty($autoparte['descripcion'])): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-align-left"></i>
                        Descripción
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($autoparte['descripcion'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Información Adicional -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-500 to-gray-600 px-6 py-4">
                    <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                        <i class="fas fa-info-circle"></i>
                        Información Adicional
                    </h2>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">ID</dt>
                            <dd class="font-medium text-gray-800">#<?= $autoparte['id'] ?></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Estado</dt>
                            <dd>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $autoparte['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' ?>">
                                    <?= $autoparte['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Categoría</dt>
                            <dd class="font-medium text-gray-800"><?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?></dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Fecha de Creación</dt>
                            <dd class="font-medium text-gray-800"><?= date('d/m/Y H:i', strtotime($autoparte['fecha_creacion'])) ?></dd>
                        </div>
                        <?php if (!empty($autoparte['fecha_actualizacion'])): ?>
                        <div class="col-span-2">
                            <dt class="text-gray-500">Última Actualización</dt>
                            <dd class="font-medium text-gray-800"><?= date('d/m/Y H:i', strtotime($autoparte['fecha_actualizacion'])) ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para imagen ampliada -->
<div id="modal-imagen" class="fixed inset-0 bg-black bg-opacity-75 hidden items-center justify-center z-50" onclick="cerrarModal()">
    <div class="max-w-4xl max-h-[90vh] p-4">
        <img id="modal-img" src="" alt="Imagen ampliada" class="max-w-full max-h-full object-contain rounded-lg">
    </div>
    <button class="absolute top-4 right-4 text-white text-3xl hover:text-gray-300" onclick="cerrarModal()">
        <i class="fas fa-times"></i>
    </button>
</div>

<script>
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

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
