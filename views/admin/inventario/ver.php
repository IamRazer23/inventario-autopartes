<?php
/**
 * Vista: Ver Detalle de Autoparte
 */

// Incluir header del admin
require_once VIEWS_PATH . '/layouts/header.php';
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

    <!-- Título y acciones -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-cog me-2"></i><?= htmlspecialchars($autoparte['nombre']) ?>
        </h1>
        <div>
            <?php if (hasPermission('inventario', 'actualizar')): ?>
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-editar&id=<?= $autoparte['id'] ?>" class="btn btn-primary me-2">
                <i class="fas fa-edit me-1"></i> Editar
            </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=inventario" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver al Inventario
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Columna de imagen -->
        <div class="col-lg-5 mb-4">
            <div class="card">
                <div class="card-body p-0">
                    <?php if ($autoparte['imagen_grande']): ?>
                        <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_grande']) ?>" 
                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                             class="img-fluid w-100 rounded-top"
                             style="max-height: 400px; object-fit: contain; background: #f8f9fa;">
                    <?php elseif ($autoparte['imagen_thumb']): ?>
                        <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_thumb']) ?>" 
                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                             class="img-fluid w-100 rounded-top"
                             style="max-height: 400px; object-fit: contain; background: #f8f9fa;">
                    <?php else: ?>
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 300px;">
                            <div class="text-center text-muted">
                                <i class="fas fa-image fa-5x mb-3"></i>
                                <p>Sin imagen disponible</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Miniaturas -->
                <?php if ($autoparte['imagen_thumb'] && $autoparte['imagen_grande']): ?>
                <div class="card-footer bg-light">
                    <div class="row g-2">
                        <div class="col-3">
                            <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_thumb']) ?>" 
                                 class="img-thumbnail" alt="Miniatura"
                                 style="width: 100%; height: 60px; object-fit: cover; cursor: pointer;">
                        </div>
                        <div class="col-3">
                            <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_grande']) ?>" 
                                 class="img-thumbnail" alt="Grande"
                                 style="width: 100%; height: 60px; object-fit: cover; cursor: pointer;">
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Columna de información -->
        <div class="col-lg-7">
            <!-- Estado y precio -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <span class="badge <?= $autoparte['estado'] == 1 ? 'bg-success' : 'bg-secondary' ?> fs-6 mb-2">
                                <?= $autoparte['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                            </span>
                            <h2 class="text-primary mb-0">$<?= number_format($autoparte['precio'], 2) ?></h2>
                            <small class="text-muted">Precio unitario</small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <?php 
                            $stockClass = 'text-success';
                            $stockIcon = 'fa-check-circle';
                            $stockText = 'En stock';
                            
                            if ($autoparte['stock'] <= 0) {
                                $stockClass = 'text-danger';
                                $stockIcon = 'fa-times-circle';
                                $stockText = 'Agotado';
                            } elseif ($autoparte['stock'] <= 5) {
                                $stockClass = 'text-warning';
                                $stockIcon = 'fa-exclamation-circle';
                                $stockText = 'Stock bajo';
                            }
                            ?>
                            <div class="<?= $stockClass ?>">
                                <i class="fas <?= $stockIcon ?> fa-2x"></i>
                                <h3 class="mb-0"><?= $autoparte['stock'] ?> unidades</h3>
                                <small><?= $stockText ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información del vehículo -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-car me-2"></i>Información del Vehículo</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Marca</label>
                            <p class="mb-0 fw-bold"><?= htmlspecialchars($autoparte['marca']) ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Modelo</label>
                            <p class="mb-0 fw-bold"><?= htmlspecialchars($autoparte['modelo']) ?></p>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="text-muted small">Año</label>
                            <p class="mb-0 fw-bold"><?= $autoparte['anio'] ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Clasificación -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Clasificación</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">Categoría</label>
                            <p class="mb-0">
                                <span class="badge bg-info fs-6">
                                    <?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Descripción -->
            <?php if ($autoparte['descripcion']): ?>
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-align-left me-2"></i>Descripción</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?= nl2br(htmlspecialchars($autoparte['descripcion'])) ?></p>
                </div>
            </div>
            <?php endif; ?>

            <!-- Información del registro -->
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información del Registro</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">ID:</td>
                                    <td><strong>#<?= $autoparte['id'] ?></strong></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Fecha de creación:</td>
                                    <td><?= date('d/m/Y H:i', strtotime($autoparte['fecha_creacion'])) ?></td>
                                </tr>
                                <?php if ($autoparte['fecha_actualizacion']): ?>
                                <tr>
                                    <td class="text-muted">Última actualización:</td>
                                    <td><?= date('d/m/Y H:i', strtotime($autoparte['fecha_actualizacion'])) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless mb-0">
                                <?php if (!empty($autoparte['usuario_nombre'] ?? null)): ?>
<tr>
    <td class="text-muted">Registrado por:</td>
    <td><?= htmlspecialchars($autoparte['usuario_nombre']) ?></td>
</tr>
<?php endif; ?>
                                <tr>
                                    <td class="text-muted">Valor en inventario:</td>
                                    <td><strong>$<?= number_format($autoparte['precio'] * $autoparte['stock'], 2) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Acciones Rápidas</h5>
            <div class="row g-3">
                <?php if (hasPermission('inventario', 'actualizar')): ?>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#modalAgregarStock">
                        <i class="fas fa-plus-circle me-2"></i>Agregar Stock
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#modalRestarStock">
                        <i class="fas fa-minus-circle me-2"></i>Restar Stock
                    </button>
                </div>
                <?php endif; ?>
                <div class="col-md-3">
                    <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $autoparte['id'] ?>" 
                       class="btn btn-outline-info w-100" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Ver en Catálogo
                    </a>
                </div>
                <?php if (hasPermission('inventario', 'eliminar')): ?>
                <div class="col-md-3">
                    <?php if ($autoparte['estado'] == 1): ?>
                    <button type="button" class="btn btn-outline-danger w-100" id="btnDesactivar">
                        <i class="fas fa-ban me-2"></i>Desactivar
                    </button>
                    <?php else: ?>
                    <button type="button" class="btn btn-outline-success w-100" id="btnActivar">
                        <i class="fas fa-check me-2"></i>Activar
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Stock -->
<div class="modal fade" id="modalAgregarStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Agregar Stock</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Stock actual: <strong><?= $autoparte['stock'] ?></strong> unidades</p>
                <div class="mb-3">
                    <label for="cantidadAgregar" class="form-label">Cantidad a agregar</label>
                    <input type="number" class="form-control" id="cantidadAgregar" min="1" value="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarAgregar">
                    <i class="fas fa-check me-1"></i>Agregar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Restar Stock -->
<div class="modal fade" id="modalRestarStock" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-minus-circle me-2"></i>Restar Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Stock actual: <strong><?= $autoparte['stock'] ?></strong> unidades</p>
                <div class="mb-3">
                    <label for="cantidadRestar" class="form-label">Cantidad a restar</label>
                    <input type="number" class="form-control" id="cantidadRestar" min="1" max="<?= $autoparte['stock'] ?>" value="1">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning" id="btnConfirmarRestar">
                    <i class="fas fa-check me-1"></i>Restar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const autoparteId = <?= $autoparte['id'] ?>;
    
    // Función para actualizar stock
    function actualizarStock(cantidad, tipo) {
        const formData = new FormData();
        formData.append('id', autoparteId);
        formData.append('cantidad', cantidad);
        formData.append('tipo', tipo);
        
        fetch('<?= BASE_URL ?>/index.php?module=admin&action=autoparte-actualizar-stock', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error al actualizar stock');
            }
        })
        .catch(error => {
            alert('Error de conexión');
        });
    }
    
    // Agregar stock
    document.getElementById('btnConfirmarAgregar')?.addEventListener('click', function() {
        const cantidad = parseInt(document.getElementById('cantidadAgregar').value);
        if (cantidad > 0) {
            actualizarStock(cantidad, 'agregar');
        }
    });
    
    // Restar stock
    document.getElementById('btnConfirmarRestar')?.addEventListener('click', function() {
        const cantidad = parseInt(document.getElementById('cantidadRestar').value);
        if (cantidad > 0) {
            actualizarStock(cantidad, 'restar');
        }
    });
    
    // Desactivar
    document.getElementById('btnDesactivar')?.addEventListener('click', function() {
        if (confirm('¿Estás seguro de desactivar esta autoparte?')) {
            const formData = new FormData();
            formData.append('id', autoparteId);
            
            fetch('<?= BASE_URL ?>/index.php?module=admin&action=autoparte-desactivar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al desactivar');
                }
            });
        }
    });
    
    // Activar
    document.getElementById('btnActivar')?.addEventListener('click', function() {
        if (confirm('¿Estás seguro de activar esta autoparte?')) {
            const formData = new FormData();
            formData.append('id', autoparteId);
            
            fetch('<?= BASE_URL ?>/index.php?module=admin&action=autoparte-activar', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error al activar');
                }
            });
        }
    });
});
</script>

<?php
// Incluir footer del admin
require_once VIEWS_PATH . '/layouts/footer.php';
?>
