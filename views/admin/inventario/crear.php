<?php
/**
 * Vista para crear una nueva autoparte en el inventario
 */

require_once VIEWS_PATH . '/layouts/header.php';

// Obtener datos anteriores si hay error
$old = $_SESSION['old'] ?? [];
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['old'], $_SESSION['errors']);
?>

<div class="container-fluid py-4">
    <!-- Breadcrumbs -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <?php foreach ($breadcrumbs as $index => $crumb): ?>
                <?php if ($index === count($breadcrumbs) - 1): ?>
                    <li class="breadcrumb-item active"><?= htmlspecialchars($crumb['text']) ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item">
                        <a href="<?= htmlspecialchars($crumb['url']) ?>"><?= htmlspecialchars($crumb['text']) ?></a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>

    <!-- Título -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-plus-circle me-2"></i>Agregar Autoparte
        </h1>
        <a href="<?= BASE_URL ?>/index.php?module=admin&action=inventario" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Volver al Inventario
        </a>
    </div>

    <!-- Mostrar errores globales -->
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle me-2"></i>Por favor corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Formulario -->
    <form action="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-store" method="POST" enctype="multipart/form-data" id="formAutoparte">
        <div class="row">
            <!-- Columna principal -->
            <div class="col-lg-8">
                <!-- Información básica -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información Básica</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-12">
                                <label for="nombre" class="form-label">Nombre de la Autoparte <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>" 
                                       id="nombre" name="nombre" 
                                       value="<?= htmlspecialchars($old['nombre'] ?? '') ?>"
                                       placeholder="Ej: Puerta delantera derecha"
                                       required maxlength="150">
                                <?php if (isset($errors['nombre'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['nombre']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Descripción -->
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" 
                                          rows="3" placeholder="Descripción detallada de la autoparte..."><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
                            </div>

                            <!-- Marca -->
                            <div class="col-md-4">
                                <label for="marca" class="form-label">Marca del Vehículo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['marca']) ? 'is-invalid' : '' ?>" 
                                       id="marca" name="marca" list="listaMarcas"
                                       value="<?= htmlspecialchars($old['marca'] ?? '') ?>"
                                       placeholder="Ej: Toyota" required maxlength="50">
                                <datalist id="listaMarcas">
                                    <?php foreach ($marcas as $marca): ?>
                                        <option value="<?= htmlspecialchars($marca) ?>">
                                    <?php endforeach; ?>
                                </datalist>
                                <?php if (isset($errors['marca'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['marca']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Modelo -->
                            <div class="col-md-4">
                                <label for="modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control <?= isset($errors['modelo']) ? 'is-invalid' : '' ?>" 
                                       id="modelo" name="modelo"
                                       value="<?= htmlspecialchars($old['modelo'] ?? '') ?>"
                                       placeholder="Ej: Corolla" required maxlength="50">
                                <?php if (isset($errors['modelo'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['modelo']) ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Año -->
                            <div class="col-md-4">
                                <label for="anio" class="form-label">Año <span class="text-danger">*</span></label>
                                <input type="number" class="form-control <?= isset($errors['anio']) ? 'is-invalid' : '' ?>" 
                                       id="anio" name="anio"
                                       value="<?= htmlspecialchars($old['anio'] ?? date('Y')) ?>"
                                       min="1900" max="<?= date('Y') + 1 ?>" required>
                                <?php if (isset($errors['anio'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['anio']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Precio y Stock -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Precio e Inventario</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <!-- Precio -->
                            <div class="col-md-6">
                                <label for="precio" class="form-label">Precio (USD) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control <?= isset($errors['precio']) ? 'is-invalid' : '' ?>" 
                                           id="precio" name="precio"
                                           value="<?= htmlspecialchars($old['precio'] ?? '0.00') ?>"
                                           min="0" step="0.01" required>
                                    <?php if (isset($errors['precio'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['precio']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="col-md-6">
                                <label for="stock" class="form-label">Stock Disponible <span class="text-danger">*</span></label>
                                <input type="number" class="form-control <?= isset($errors['stock']) ? 'is-invalid' : '' ?>" 
                                       id="stock" name="stock"
                                       value="<?= htmlspecialchars($old['stock'] ?? '1') ?>"
                                       min="0" required>
                                <?php if (isset($errors['stock'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['stock']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imágenes -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-images me-2"></i>Imágenes</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <!-- Thumbnail -->
                            <div class="col-md-6">
                                <label class="form-label">Imagen Miniatura (Thumbnail)</label>
                                <div class="border rounded p-3 text-center" id="thumbPreviewContainer">
                                    <div id="thumbPreview" class="mb-2">
                                        <i class="fas fa-image fa-4x text-muted"></i>
                                        <p class="text-muted small mt-2">300x300 px recomendado</p>
                                    </div>
                                    <input type="file" class="form-control <?= isset($errors['imagen_thumb']) ? 'is-invalid' : '' ?>" 
                                           id="imagen_thumb" name="imagen_thumb"
                                           accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted">JPG, PNG o WEBP. Máx 2MB</small>
                                    <?php if (isset($errors['imagen_thumb'])): ?>
                                        <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['imagen_thumb']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Imagen grande -->
                            <div class="col-md-6">
                                <label class="form-label">Imagen Grande (Detalle)</label>
                                <div class="border rounded p-3 text-center" id="grandePreviewContainer">
                                    <div id="grandePreview" class="mb-2">
                                        <i class="fas fa-image fa-4x text-muted"></i>
                                        <p class="text-muted small mt-2">800x800 px recomendado</p>
                                    </div>
                                    <input type="file" class="form-control <?= isset($errors['imagen_grande']) ? 'is-invalid' : '' ?>" 
                                           id="imagen_grande" name="imagen_grande"
                                           accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted">JPG, PNG o WEBP. Máx 5MB</small>
                                    <?php if (isset($errors['imagen_grande'])): ?>
                                        <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['imagen_grande']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna lateral -->
            <div class="col-lg-4">
                <!-- Clasificación -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Clasificación</h5>
                    </div>
                    <div class="card-body">
                        <!-- Categoría -->
                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['categoria']) ? 'is-invalid' : '' ?>" 
                                    id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccionar categoría...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($old['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['categoria'])): ?>
                                <div class="invalid-feedback"><?= htmlspecialchars($errors['categoria']) ?></div>
                            <?php endif; ?>
                        </div>

                <!-- Estado -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-toggle-on me-2"></i>Estado</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="estado" name="estado"
                                   <?= !isset($old['estado']) || $old['estado'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="estado">
                                Autoparte activa (visible en catálogo)
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Guardar Autoparte
                            </button>
                            <a href="<?= BASE_URL ?>/index.php?module=admin&action=inventario" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Preview de imágenes
    function setupImagePreview(inputId, previewId) {
        const input = document.getElementById(inputId);
        const preview = document.getElementById(previewId);
        
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = '<img src="' + e.target.result + '" class="img-fluid rounded" style="max-height: 150px;">';
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    setupImagePreview('imagen_thumb', 'thumbPreview');
    setupImagePreview('imagen_grande', 'grandePreview');
    
    // Validación del formulario
    document.getElementById('formAutoparte').addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validar campos requeridos
        const required = ['nombre', 'marca', 'modelo', 'anio', 'precio', 'stock', 'categoria_id'];
        
        required.forEach(function(fieldName) {
            const field = document.getElementById(fieldName) || document.querySelector('[name="' + fieldName + '"]');
            if (field && !field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else if (field) {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor completa todos los campos requeridos');
        }
    });
});
</script>

<?php
require_once VIEWS_PATH . '/layouts/footer.php';
?>