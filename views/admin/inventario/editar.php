<?php
/**
 * Vista: Crear Autoparte - Panel de Operador
 * Formulario con Tailwind CSS para agregar nuevas autopartes
 */

require_once VIEWS_PATH . '/layouts/header.php';

// Obtener datos anteriores si hay error
$old = $_SESSION['old'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header con navegación -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    Agregar Autoparte
                </h1>
                <p class="text-gray-500 mt-2">Complete el formulario para registrar una nueva autoparte en el inventario</p>
            </div>
            <a href="<?= BASE_URL ?>/index.php?module=operador&action=inventario" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
                Volver al Inventario
            </a>
        </div>
    </div>

    <!-- Mostrar errores globales -->
    <?php if (!empty($errors)): ?>
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4">
        <div class="flex items-start gap-3">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
            </div>
            <div>
                <h3 class="text-red-800 font-semibold">Por favor corrige los siguientes errores:</h3>
                <ul class="mt-2 text-red-700 text-sm list-disc list-inside space-y-1">
                    <?php foreach ($errors as $field => $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form action="<?= BASE_URL ?>/index.php?module=operador&action=guardar-autoparte" method="POST" enctype="multipart/form-data" id="formAutoparte">
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Columna Principal (2/3) -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Card: Información Básica -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            Información Básica
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de la Autoparte <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                                   placeholder="Ej: Puerta delantera derecha"
                                   required 
                                   maxlength="150"
                                   class="w-full px-4 py-3 border <?= isset($errors['nombre']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                            <?php if (isset($errors['nombre'])): ?>
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['nombre']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                                Descripción
                            </label>
                            <textarea id="descripcion" 
                                      name="descripcion" 
                                      rows="4" 
                                      placeholder="Descripción detallada de la autoparte..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none resize-none"><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
                        </div>

                        <!-- Marca, Modelo, Año -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Marca -->
                            <div>
                                <label for="marca" class="block text-sm font-medium text-gray-700 mb-2">
                                    Marca del Vehículo <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="marca" 
                                       name="marca" 
                                       list="listaMarcas"
                                       value="<?= htmlspecialchars($old['marca'] ?? '') ?>"
                                       placeholder="Ej: Toyota"
                                       required 
                                       maxlength="50"
                                       class="w-full px-4 py-3 border <?= isset($errors['marca']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                                <datalist id="listaMarcas">
                                    <?php if (!empty($marcas)): ?>
                                        <?php foreach ($marcas as $marca): ?>
                                            <option value="<?= htmlspecialchars($marca) ?>">
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </datalist>
                                <?php if (isset($errors['marca'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['marca']) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Modelo -->
                            <div>
                                <label for="modelo" class="block text-sm font-medium text-gray-700 mb-2">
                                    Modelo <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="modelo" 
                                       name="modelo"
                                       value="<?= htmlspecialchars($old['modelo'] ?? '') ?>"
                                       placeholder="Ej: Corolla"
                                       required 
                                       maxlength="50"
                                       class="w-full px-4 py-3 border <?= isset($errors['modelo']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                                <?php if (isset($errors['modelo'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['modelo']) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Año -->
                            <div>
                                <label for="anio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Año <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       id="anio" 
                                       name="anio"
                                       value="<?= htmlspecialchars($old['anio'] ?? date('Y')) ?>"
                                       min="1900" 
                                       max="<?= date('Y') + 1 ?>"
                                       required
                                       class="w-full px-4 py-3 border <?= isset($errors['anio']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all outline-none">
                                <?php if (isset($errors['anio'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['anio']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Precio e Inventario -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                            <i class="fas fa-dollar-sign"></i>
                            Precio e Inventario
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Precio -->
                            <div>
                                <label for="precio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Precio (USD) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-medium">$</span>
                                    <input type="number" 
                                           id="precio" 
                                           name="precio"
                                           value="<?= htmlspecialchars($old['precio'] ?? '0.00') ?>"
                                           min="0" 
                                           step="0.01"
                                           required
                                           class="w-full pl-8 pr-4 py-3 border <?= isset($errors['precio']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none">
                                </div>
                                <?php if (isset($errors['precio'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['precio']) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Stock -->
                            <div>
                                <label for="stock" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cantidad en Stock <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                        <i class="fas fa-boxes"></i>
                                    </span>
                                    <input type="number" 
                                           id="stock" 
                                           name="stock"
                                           value="<?= htmlspecialchars($old['stock'] ?? '0') ?>"
                                           min="0"
                                           required
                                           class="w-full pl-10 pr-4 py-3 border <?= isset($errors['stock']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all outline-none">
                                </div>
                                <?php if (isset($errors['stock'])): ?>
                                    <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['stock']) ?></p>
                                <?php endif; ?>
                                <p class="mt-1 text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>Se mostrará alerta si el stock es menor o igual a 5 unidades
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card: Imágenes -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                            <i class="fas fa-images"></i>
                            Imágenes del Producto
                        </h2>
                    </div>
                    <div class="p-6 space-y-6">
                        
                        <!-- Thumbnail URL -->
                        <div>
                            <label for="imagen_thumb_url" class="block text-sm font-medium text-gray-700 mb-2">
                                URL del Thumbnail (imagen pequeña)
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-link"></i>
                                </span>
                                <input type="url" 
                                       id="imagen_thumb_url" 
                                       name="imagen_thumb_url"
                                       value="<?= htmlspecialchars($old['imagen_thumb_url'] ?? '') ?>"
                                       placeholder="https://ejemplo.com/imagen-thumb.jpg"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all outline-none">
                            </div>
                            <?php if (isset($errors['imagen_thumb_url'])): ?>
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['imagen_thumb_url']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Imagen Grande URL -->
                        <div>
                            <label for="imagen_grande_url" class="block text-sm font-medium text-gray-700 mb-2">
                                URL de la Imagen Grande
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="fas fa-image"></i>
                                </span>
                                <input type="url" 
                                       id="imagen_grande_url" 
                                       name="imagen_grande_url"
                                       value="<?= htmlspecialchars($old['imagen_grande_url'] ?? '') ?>"
                                       placeholder="https://ejemplo.com/imagen-grande.jpg"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all outline-none">
                            </div>
                            <?php if (isset($errors['imagen_grande_url'])): ?>
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['imagen_grande_url']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Preview de imágenes -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-2">Preview Thumbnail</p>
                                <div id="preview-thumb" class="w-full h-32 bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                                    <span class="text-gray-400 text-sm">Sin imagen</span>
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-2">Preview Imagen Grande</p>
                                <div id="preview-grande" class="w-full h-32 bg-gray-100 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center overflow-hidden">
                                    <span class="text-gray-400 text-sm">Sin imagen</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Lateral (1/3) -->
            <div class="space-y-6">
                
                <!-- Card: Clasificación -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                        <h2 class="text-white font-semibold text-lg flex items-center gap-2">
                            <i class="fas fa-tags"></i>
                            Clasificación
                        </h2>
                    </div>
                    <div class="p-6 space-y-5">
                        
                        <!-- Categoría -->
                        <div>
                            <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Categoría <span class="text-red-500">*</span>
                            </label>
                            <select id="categoria_id" 
                                    name="categoria_id" 
                                    required
                                    class="w-full px-4 py-3 border <?= isset($errors['categoria_id']) ? 'border-red-500 ring-2 ring-red-200' : 'border-gray-300' ?> rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all outline-none bg-white">
                                <option value="">Seleccionar categoría...</option>
                                <?php if (!empty($categorias)): ?>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>" <?= ($old['categoria_id'] ?? '') == $categoria['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categoria['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (isset($errors['categoria_id'])): ?>
                                <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i><?= htmlspecialchars($errors['categoria_id']) ?></p>
                            <?php endif; ?>
                        </div>

                        <!-- Estado -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Estado del Producto
                            </label>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" 
                                       name="estado" 
                                       value="1" 
                                       <?= ($old['estado'] ?? 1) ? 'checked' : '' ?>
                                       class="sr-only peer">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500"></div>
                                <span class="ms-3 text-sm font-medium text-gray-700 peer-checked:text-green-600">
                                    Activo
                                </span>
                            </label>
                            <p class="mt-2 text-xs text-gray-500">
                                Los productos inactivos no se mostrarán en el catálogo público
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card: Acciones -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 space-y-4">
                        
                        <!-- Botón Guardar -->
                        <button type="submit" 
                                class="w-full py-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                            <i class="fas fa-save"></i>
                            Guardar Autoparte
                        </button>

                        <!-- Botón Guardar y Agregar Otro -->
                        <button type="submit" 
                                name="agregar_otro" 
                                value="1"
                                class="w-full py-3 bg-blue-50 hover:bg-blue-100 text-blue-600 font-medium rounded-xl transition-all border border-blue-200 flex items-center justify-center gap-2">
                            <i class="fas fa-plus"></i>
                            Guardar y Agregar Otro
                        </button>

                        <!-- Botón Cancelar -->
                        <a href="<?= BASE_URL ?>/index.php?module=operador&action=inventario" 
                           class="w-full py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-medium rounded-xl transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </div>

                <!-- Card: Ayuda -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100 p-6">
                    <h3 class="font-semibold text-blue-800 flex items-center gap-2 mb-3">
                        <i class="fas fa-lightbulb text-yellow-500"></i>
                        Consejos
                    </h3>
                    <ul class="text-sm text-blue-700 space-y-2">
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            Use nombres descriptivos para facilitar la búsqueda
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            Agregue imágenes de buena calidad
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            Verifique el stock antes de guardar
                        </li>
                        <li class="flex items-start gap-2">
                            <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                            Use la marca y modelo correctos
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Script para preview de imágenes -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const thumbInput = document.getElementById('imagen_thumb_url');
    const grandeInput = document.getElementById('imagen_grande_url');
    const previewThumb = document.getElementById('preview-thumb');
    const previewGrande = document.getElementById('preview-grande');

    function updatePreview(input, previewDiv) {
        const url = input.value.trim();
        if (url) {
            previewDiv.innerHTML = `<img src="${url}" alt="Preview" class="w-full h-full object-cover" onerror="this.parentElement.innerHTML='<span class=\\'text-red-400 text-sm\\'>Error al cargar</span>'">`;
        } else {
            previewDiv.innerHTML = '<span class="text-gray-400 text-sm">Sin imagen</span>';
        }
    }

    thumbInput.addEventListener('input', () => updatePreview(thumbInput, previewThumb));
    grandeInput.addEventListener('input', () => updatePreview(grandeInput, previewGrande));

    // Cargar previews iniciales si hay valores
    if (thumbInput.value) updatePreview(thumbInput, previewThumb);
    if (grandeInput.value) updatePreview(grandeInput, previewGrande);
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
