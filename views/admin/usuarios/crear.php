<?php
/**
 * Vista: Formulario de Autoparte (Crear/Editar)
 * MODIFICADO: Usa URLs de imágenes en lugar de subir archivos
 * 
 * Usar para: views/admin/autopartes/crear.php y editar.php
 */

$esEdicion = isset($autoparte) && !empty($autoparte);
$titulo = $esEdicion ? 'Editar Autoparte' : 'Nueva Autoparte';
$urlAction = $esEdicion 
    ? BASE_URL . '/index.php?module=admin&action=autoparte-update'
    : BASE_URL . '/index.php?module=admin&action=autoparte-store';

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-<?= $esEdicion ? 'edit' : 'plus-circle' ?> text-indigo-600 mr-2"></i>
            <?= $titulo ?>
        </h1>
        <p class="text-gray-600 mt-1">
            <?= $esEdicion ? 'Modifica la información de la autoparte' : 'Completa el formulario para agregar una nueva autoparte' ?>
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Formulario Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                
                <form action="<?= $urlAction ?>" method="POST" id="formAutoparte">
                    <?php if ($esEdicion): ?>
                        <input type="hidden" name="id" value="<?= $autoparte['id'] ?>">
                    <?php endif; ?>
                    
                    <!-- Información Básica -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                            Información Básica
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Nombre -->
                            <div class="md:col-span-2">
                                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nombre de la Autoparte <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="nombre" name="nombre" required maxlength="150"
                                    class="w-full px-4 py-3 border <?= isset($errors['nombre']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ej: Pastillas de freno delanteras"
                                    value="<?= $esEdicion ? e($autoparte['nombre']) : e($old['nombre'] ?? '') ?>">
                                <?php if (isset($errors['nombre'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= e($errors['nombre']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Marca -->
                            <div>
                                <label for="marca" class="block text-sm font-medium text-gray-700 mb-2">
                                    Marca <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="marca" name="marca" required maxlength="50"
                                    class="w-full px-4 py-3 border <?= isset($errors['marca']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ej: Toyota, Honda, Ford..."
                                    list="marcas-list"
                                    value="<?= $esEdicion ? e($autoparte['marca']) : e($old['marca'] ?? '') ?>">
                                <datalist id="marcas-list">
                                    <?php if (!empty($marcas)): ?>
                                        <?php foreach ($marcas as $m): ?>
                                            <option value="<?= e($m) ?>">
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </datalist>
                                <?php if (isset($errors['marca'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= e($errors['marca']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Modelo -->
                            <div>
                                <label for="modelo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Modelo <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="modelo" name="modelo" required maxlength="50"
                                    class="w-full px-4 py-3 border <?= isset($errors['modelo']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Ej: Corolla, Civic, F-150..."
                                    value="<?= $esEdicion ? e($autoparte['modelo']) : e($old['modelo'] ?? '') ?>">
                                <?php if (isset($errors['modelo'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= e($errors['modelo']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Año -->
                            <div>
                                <label for="anio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Año <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="anio" name="anio" required min="1900" max="<?= date('Y') + 1 ?>"
                                    class="w-full px-4 py-3 border <?= isset($errors['anio']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="<?= date('Y') ?>"
                                    value="<?= $esEdicion ? e($autoparte['anio']) : e($old['anio'] ?? date('Y')) ?>">
                                <?php if (isset($errors['anio'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= e($errors['anio']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Categoría -->
                            <div>
                                <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Categoría <span class="text-red-500">*</span>
                                </label>
                                <select id="categoria_id" name="categoria_id" required
                                    class="w-full px-4 py-3 border <?= isset($errors['categoria']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleccionar categoría...</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" 
                                            <?= ($esEdicion && $autoparte['categoria_id'] == $cat['id']) || ($old['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                            <?= e($cat['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['categoria'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= e($errors['categoria']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Descripción -->
                            <div class="md:col-span-2">
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                    Descripción
                                </label>
                                <textarea id="descripcion" name="descripcion" rows="3" maxlength="1000"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                    placeholder="Describe las características de la autoparte..."><?= $esEdicion ? e($autoparte['descripcion']) : e($old['descripcion'] ?? '') ?></textarea>
                            </div>
                            
                        </div>
                    </div>
                    
                    <!-- Precios y Stock -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-dollar-sign text-green-500 mr-2"></i>
                            Precio y Stock
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- Precio -->
                            <div>
                                <label for="precio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Precio (USD) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                    <input type="number" id="precio" name="precio" required min="0" step="0.01"
                                        class="w-full pl-8 pr-4 py-3 border <?= isset($errors['precio']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                        placeholder="0.00"
                                        value="<?= $esEdicion ? e($autoparte['precio']) : e($old['precio'] ?? '') ?>">
                                </div>
                                <?php if (isset($errors['precio'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= e($errors['precio']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Stock -->
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                                    Stock <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="stock" name="stock" required min="0"
                                    class="w-full px-4 py-3 border <?= isset($errors['stock']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="0"
                                    value="<?= $esEdicion ? e($autoparte['stock']) : e($old['stock'] ?? '0') ?>">
                                <?php if (isset($errors['stock'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= e($errors['stock']) ?></p>
                                <?php endif; ?>
                            </div>
                            
                        </div>
                    </div>
                    
                    <!-- IMÁGENES - URLs -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-image text-purple-500 mr-2"></i>
                            Imágenes (URLs)
                        </h3>
                        
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4 rounded">
                            <div class="flex">
                                <i class="fas fa-info-circle text-blue-500 mt-1 mr-2"></i>
                                <p class="text-sm text-blue-700">
                                    Ingresa las URLs de las imágenes del producto. Puedes usar servicios como 
                                    <strong>Imgur</strong>, <strong>ImgBB</strong>, <strong>Cloudinary</strong>, 
                                    o cualquier otro hosting de imágenes.
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- URL Thumbnail -->
                            <div>
                                <label for="imagen_thumb_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-compress text-gray-400 mr-1"></i>
                                    URL Imagen Miniatura (Thumbnail)
                                </label>
                                <input type="url" id="imagen_thumb_url" name="imagen_thumb_url"
                                    class="w-full px-4 py-3 border <?= isset($errors['imagen_thumb_url']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="https://ejemplo.com/imagen-pequena.jpg"
                                    value="<?= $esEdicion ? e($autoparte['thumbnail'] ?? '') : e($old['imagen_thumb_url'] ?? '') ?>"
                                    onchange="previewImageUrl(this, 'preview-thumb')">
                                <?php if (isset($errors['imagen_thumb_url'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= e($errors['imagen_thumb_url']) ?></p>
                                <?php endif; ?>
                                <p class="mt-1 text-xs text-gray-500">Recomendado: 300x300 px</p>
                                
                                <!-- Preview Thumbnail -->
                                <div id="preview-thumb-container" class="mt-3 <?= ($esEdicion && !empty($autoparte['thumbnail'])) ? '' : 'hidden' ?>">
                                    <img id="preview-thumb" 
                                         src="<?= $esEdicion && !empty($autoparte['thumbnail']) ? e($autoparte['thumbnail']) : '' ?>" 
                                         alt="Preview thumbnail"
                                         class="w-24 h-24 object-cover rounded-lg border border-gray-300"
                                         onerror="this.parentElement.classList.add('hidden')">
                                </div>
                            </div>
                            
                            <!-- URL Imagen Grande -->
                            <div>
                                <label for="imagen_grande_url" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-expand text-gray-400 mr-1"></i>
                                    URL Imagen Grande
                                </label>
                                <input type="url" id="imagen_grande_url" name="imagen_grande_url"
                                    class="w-full px-4 py-3 border <?= isset($errors['imagen_grande_url']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="https://ejemplo.com/imagen-grande.jpg"
                                    value="<?= $esEdicion ? e($autoparte['imagen_grande'] ?? '') : e($old['imagen_grande_url'] ?? '') ?>"
                                    onchange="previewImageUrl(this, 'preview-grande')">
                                <?php if (isset($errors['imagen_grande_url'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><?= e($errors['imagen_grande_url']) ?></p>
                                <?php endif; ?>
                                <p class="mt-1 text-xs text-gray-500">Recomendado: 800x800 px o mayor</p>
                                
                                <!-- Preview Grande -->
                                <div id="preview-grande-container" class="mt-3 <?= ($esEdicion && !empty($autoparte['imagen_grande'])) ? '' : 'hidden' ?>">
                                    <img id="preview-grande" 
                                         src="<?= $esEdicion && !empty($autoparte['imagen_grande']) ? e($autoparte['imagen_grande']) : '' ?>" 
                                         alt="Preview imagen grande"
                                         class="w-32 h-32 object-cover rounded-lg border border-gray-300"
                                         onerror="this.parentElement.classList.add('hidden')">
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    
                    <!-- Estado -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                            <i class="fas fa-toggle-on text-blue-500 mr-2"></i>
                            Estado
                        </h3>
                        
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="estado" value="1"
                                <?= ($esEdicion && $autoparte['estado'] == 1) || (!$esEdicion) ? 'checked' : '' ?>
                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                            <span class="ml-3 text-sm font-medium text-gray-700">
                                Autoparte activa (visible en el catálogo)
                            </span>
                        </label>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                        <a href="<?= BASE_URL ?>/index.php?module=admin&action=inventario" 
                           class="text-gray-600 hover:text-gray-800 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Volver al inventario
                        </a>
                        
                        <div class="flex gap-3">
                            <button type="reset" 
                                class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-semibold transition">
                                <i class="fas fa-redo mr-2"></i>
                                Limpiar
                            </button>
                            <button type="submit" 
                                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold shadow-md transition">
                                <i class="fas fa-<?= $esEdicion ? 'save' : 'plus-circle' ?> mr-2"></i>
                                <?= $esEdicion ? 'Guardar Cambios' : 'Crear Autoparte' ?>
                            </button>
                        </div>
                    </div>
                    
                </form>
            </div>
        </div>
        
        <!-- Panel Lateral -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Info en edición -->
            <?php if ($esEdicion): ?>
                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-md p-6 text-white">
                    <h3 class="text-lg font-semibold mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        Información
                    </h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-indigo-100">ID:</p>
                            <p class="font-semibold">#<?= $autoparte['id'] ?></p>
                        </div>
                        <div>
                            <p class="text-indigo-100">Creado:</p>
                            <p class="font-semibold"><?= formatDate($autoparte['fecha_creacion']) ?></p>
                        </div>
                        <?php if (!empty($autoparte['fecha_actualizacion'])): ?>
                        <div>
                            <p class="text-indigo-100">Última actualización:</p>
                            <p class="font-semibold"><?= formatDateTime($autoparte['fecha_actualizacion']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Ayuda -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">
                    <i class="fas fa-question-circle mr-2"></i>
                    Ayuda
                </h3>
                <ul class="space-y-3 text-sm text-blue-800">
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>El nombre debe ser descriptivo</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>Las URLs de imágenes deben ser públicas</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>Puedes usar Imgur o ImgBB para subir imágenes</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>El stock 0 marcará el producto como agotado</span>
                    </li>
                </ul>
            </div>
            
            <!-- Servicios de imágenes recomendados -->
            <div class="bg-purple-50 border border-purple-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-purple-900 mb-4">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>
                    Hosting de Imágenes
                </h3>
                <div class="space-y-3 text-sm">
                    <a href="https://imgur.com/upload" target="_blank" 
                       class="flex items-center text-purple-700 hover:text-purple-900">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Imgur (gratis)
                    </a>
                    <a href="https://imgbb.com/" target="_blank" 
                       class="flex items-center text-purple-700 hover:text-purple-900">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        ImgBB (gratis)
                    </a>
                    <a href="https://cloudinary.com/" target="_blank" 
                       class="flex items-center text-purple-700 hover:text-purple-900">
                        <i class="fas fa-external-link-alt mr-2"></i>
                        Cloudinary (gratis limitado)
                    </a>
                </div>
            </div>
            
        </div>
        
    </div>
</div>

<script>
// Preview de imagen desde URL
function previewImageUrl(input, previewId) {
    const url = input.value.trim();
    const container = document.getElementById(previewId + '-container');
    const preview = document.getElementById(previewId);
    
    if (url && isValidUrl(url)) {
        preview.src = url;
        container.classList.remove('hidden');
        
        // Verificar si la imagen carga correctamente
        preview.onerror = function() {
            container.classList.add('hidden');
        };
    } else {
        container.classList.add('hidden');
    }
}

// Validar URL
function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

// Validación del formulario
document.getElementById('formAutoparte').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const precio = document.getElementById('precio').value;
    const stock = document.getElementById('stock').value;
    
    if (nombre.length < 3) {
        e.preventDefault();
        alert('El nombre debe tener al menos 3 caracteres');
        return false;
    }
    
    if (parseFloat(precio) < 0) {
        e.preventDefault();
        alert('El precio no puede ser negativo');
        return false;
    }
    
    if (parseInt(stock) < 0) {
        e.preventDefault();
        alert('El stock no puede ser negativo');
        return false;
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>