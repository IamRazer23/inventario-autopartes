<?php
/**
 * Vista para editar una autoparte en el inventario
 */
require_once VIEWS_PATH . '/layouts/header.php';

// Obtener datos anteriores si hay error
$old = $_SESSION['old'] ?? $autoparte;
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
            <i class="fas fa-edit me-2"></i>Editar Autoparte
        </h1>
        <div>
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-ver&id=<?= $autoparte['id'] ?>" class="btn btn-outline-info me-2">
                <i class="fas fa-eye me-1"></i> Ver Detalle
            </a>
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=inventario" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al Inventario
            </a>
        </div>
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
    <form action="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-update" method="POST" enctype="multipart/form-data" id="formAutoparte">
        <input type="hidden" name="id" value="<?= $autoparte['id'] ?>">
        
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
                                       value="<?= htmlspecialchars($old['anio'] ?? '') ?>"
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
                                           value="<?= htmlspecialchars($old['precio'] ?? '') ?>"
                                           min="0" step="0.01" required>
                                    <?php if (isset($errors['precio'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['precio']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="col-md-6">
                                <label for="stock" class="form-label">Stock Disponible <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" class="form-control <?= isset($errors['stock']) ? 'is-invalid' : '' ?>" 
                                           id="stock" name="stock"
                                           value="<?= htmlspecialchars($old['stock'] ?? '') ?>"
                                           min="0" required>
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAjustarStock">
                                        <i class="fas fa-sliders-h"></i> Ajustar
                                    </button>
                                    <?php if (isset($errors['stock'])): ?>
                                        <div class="invalid-feedback"><?= htmlspecialchars($errors['stock']) ?></div>
                                    <?php endif; ?>
                                </div>
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
                                <div class="border rounded p-3 text-center">
                                    <div id="thumbPreview" class="mb-2">
                                        <?php if ($autoparte['imagen_thumb']): ?>
                                            <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_thumb']) ?>" 
                                                 class="img-fluid rounded" style="max-height: 150px;">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                            <p class="text-muted small mt-2">Sin imagen</p>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" class="form-control <?= isset($errors['imagen_thumb']) ? 'is-invalid' : '' ?>" 
                                           id="imagen_thumb" name="imagen_thumb"
                                           accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted">Dejar vacío para mantener la imagen actual</small>
                                    <?php if (isset($errors['imagen_thumb'])): ?>
                                        <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['imagen_thumb']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Imagen grande -->
                            <div class="col-md-6">
                                <label class="form-label">Imagen Grande (Detalle)</label>
                                <div class="border rounded p-3 text-center">
                                    <div id="grandePreview" class="mb-2">
                                        <?php if ($autoparte['imagen_grande']): ?>
                                            <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_grande']) ?>" 
                                                 class="img-fluid rounded" style="max-height: 150px;">
                                        <?php else: ?>
                                            <i class="fas fa-image fa-4x text-muted"></i>
                                            <p class="text-muted small mt-2">Sin imagen</p>
                                        <?php endif; ?>
                                    </div>
                                    <input type="file" class="form-control <?= isset($errors['imagen_grande']) ? 'is-invalid' : '' ?>" 
                                           id="imagen_grande" name="imagen_grande"
                                           accept="image/jpeg,image/png,image/webp">
                                    <small class="text-muted">Dejar vacío para mantener la imagen actual</small>
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
                <!-- Info del registro -->
                <div class="card mb-4 bg-light">
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Información del Registro</h6>
                        <p class="mb-1"><small><strong>ID:</strong> #<?= $autoparte['id'] ?></small></p>
                        <p class="mb-1"><small><strong>Creado:</strong> <?= date('d/m/Y H:i', strtotime($autoparte['fecha_creacion'])) ?></small></p>
                        <?php if ($autoparte['fecha_actualizacion']): ?>
                        <p class="mb-1"><small><strong>Última actualización:</strong> <?= date('d/m/Y H:i', strtotime($autoparte['fecha_actualizacion'])) ?></small></p>
                        <?php endif; ?>
                        <?php if ($autoparte['usuario_nombre']): ?>
                        <p class="mb-0"><small><strong>Creado por:</strong> <?= htmlspecialchars($autoparte['usuario_nombre']) ?></small></p>
                        <?php endif; ?>
                    </div>
                </div>

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
                                   <?= ($old['estado'] ?? 0) == 1 ? 'checked' : '' ?>>
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
                                <i class="fas fa-save me-2"></i>Guardar Cambios
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

<!-- Modal para ajustar stock -->
<div class="modal fade" id="modalAjustarStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajustar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Stock actual: <strong id="stockActualModal"><?= $autoparte['stock'] ?></strong></p>
                <div class="mb-3">
                    <label class="form-label">Tipo de ajuste</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="tipoAjuste" id="tipoAgregar" value="agregar" checked>
                        <label class="btn btn-outline-success" for="tipoAgregar">
                            <i class="fas fa-plus me-1"></i> Agregar
                        </label>
                        <input type="radio" class="btn-check" name="tipoAjuste" id="tipoRestar" value="restar">
                        <label class="btn btn-outline-danger" for="tipoRestar">
                            <i class="fas fa-minus me-1"></i> Restar
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="cantidadAjuste" class="form-label">Cantidad</label>
                    <input type="number" class="form-control" id="cantidadAjuste" min="1" value="1">
                </div>
                <p>Nuevo stock: <strong id="nuevoStockPreview"><?= $autoparte['stock'] + 1 ?></strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnAplicarAjuste">Aplicar al formulario</button>
            </div>
        </div>
    </div>
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
    
    // Modal de ajuste de stock
    const stockInput = document.getElementById('stock');
    const cantidadAjuste = document.getElementById('cantidadAjuste');
    const nuevoStockPreview = document.getElementById('nuevoStockPreview');
    const stockActualModal = document.getElementById('stockActualModal');
    
    function actualizarPreviewStock() {
        const stockActual = parseInt(stockInput.value) || 0;
        const cantidad = parseInt(cantidadAjuste.value) || 0;
        const tipo = document.querySelector('input[name="tipoAjuste"]:checked').value;
        
        stockActualModal.textContent = stockActual;
        
        let nuevoStock;
        if (tipo === 'agregar') {
            nuevoStock = stockActual + cantidad;
        } else {
            nuevoStock = Math.max(0, stockActual - cantidad);
        }
        
        nuevoStockPreview.textContent = nuevoStock;
    }
    
    cantidadAjuste.addEventListener('input', actualizarPreviewStock);
    document.querySelectorAll('input[name="tipoAjuste"]').forEach(function(radio) {
        radio.addEventListener('change', actualizarPreviewStock);
    });
    
    // Al abrir el modal, actualizar valores
    document.getElementById('modalAjustarStock').addEventListener('show.bs.modal', actualizarPreviewStock);
    
    // Aplicar ajuste
    document.getElementById('btnAplicarAjuste').addEventListener('click', function() {
        stockInput.value = nuevoStockPreview.textContent;
        bootstrap.Modal.getInstance(document.getElementById('modalAjustarStock')).hide();
    });
});
</script>

<?php
require_once VIEWS_PATH . '/layouts/footer.php';
?>