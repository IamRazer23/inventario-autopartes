<?php
/**
 * Vista: Listado de Autopartes (Inventario)
 */

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
            <i class="fas fa-warehouse me-2"></i>Inventario de Autopartes
        </h1>
        <?php if (hasPermission('inventario', 'crear')): ?>
        <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-crear" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Agregar Autoparte
        </a>
        <?php endif; ?>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Total Activas</h6>
                            <h2 class="mb-0"><?= number_format($totalActivas) ?></h2>
                        </div>
                        <i class="fas fa-cogs fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Stock Bajo</h6>
                            <h2 class="mb-0"><?= number_format($totalStockBajo) ?></h2>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-secondary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Inactivas</h6>
                            <h2 class="mb-0"><?= number_format($totalInactivas) ?></h2>
                        </div>
                        <i class="fas fa-ban fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1 opacity-75">Valor Total</h6>
                            <h2 class="mb-0">$<?= number_format($valorInventario, 2) ?></h2>
                        </div>
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Filtros de Búsqueda
                <button class="btn btn-sm btn-link float-end" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </h5>
        </div>
        <div class="collapse show" id="filtrosCollapse">
            <div class="card-body">
                <form method="GET" action="" id="formFiltros">
                    <input type="hidden" name="module" value="admin">
                    <input type="hidden" name="action" value="inventario">
                    
                    <div class="row g-3">
                        <!-- Búsqueda general -->
                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" name="buscar" 
                                   value="<?= htmlspecialchars($filtros['buscar'] ?? '') ?>"
                                   placeholder="Nombre, descripción, marca...">
                        </div>
                        
                        <!-- Categoría -->
                        <div class="col-md-2">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="categoria">
                                <option value="">Todas</option>
                                <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($filtros['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Marca -->
                        <div class="col-md-2">
                            <label class="form-label">Marca</label>
                            <select class="form-select" name="marca" id="selectMarca">
                                <option value="">Todas</option>
                                <?php foreach ($marcas as $marca): ?>
                                <option value="<?= htmlspecialchars($marca) ?>" <?= ($filtros['marca'] ?? '') == $marca ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($marca) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Año -->
                        <div class="col-md-2">
                            <label class="form-label">Año</label>
                            <select class="form-select" name="anio">
                                <option value="">Todos</option>
                                <?php foreach ($anios as $anio): ?>
                                <option value="<?= $anio ?>" <?= ($filtros['anio'] ?? '') == $anio ? 'selected' : '' ?>>
                                    <?= $anio ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Rango de precio -->
                        <div class="col-md-2">
                            <label class="form-label">Precio Mín</label>
                            <input type="number" class="form-control" name="precio_min" 
                                   value="<?= htmlspecialchars($filtros['precio_min'] ?? '') ?>"
                                   min="0" step="0.01" placeholder="$0.00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Precio Máx</label>
                            <input type="number" class="form-control" name="precio_max" 
                                   value="<?= htmlspecialchars($filtros['precio_max'] ?? '') ?>"
                                   min="0" step="0.01" placeholder="$999.99">
                        </div>
                        
                        <!-- Estado -->
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="estado">
                                <option value="">Todos</option>
                                <option value="1" <?= ($filtros['estado'] ?? '') === '1' ? 'selected' : '' ?>>Activo</option>
                                <option value="0" <?= ($filtros['estado'] ?? '') === '0' ? 'selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                        
                        <!-- Stock bajo -->
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="stock_bajo" id="stockBajo"
                                       <?= isset($filtros['stock_bajo']) && $filtros['stock_bajo'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="stockBajo">
                                    Solo stock bajo
                                </label>
                            </div>
                        </div>
                        
                        <!-- Ordenar -->
                        <div class="col-md-2">
                            <label class="form-label">Ordenar por</label>
                            <select class="form-select" name="orden">
                                <option value="fecha_creacion" <?= ($filtros['orden'] ?? '') === 'fecha_creacion' ? 'selected' : '' ?>>Fecha</option>
                                <option value="nombre" <?= ($filtros['orden'] ?? '') === 'nombre' ? 'selected' : '' ?>>Nombre</option>
                                <option value="precio" <?= ($filtros['orden'] ?? '') === 'precio' ? 'selected' : '' ?>>Precio</option>
                                <option value="stock" <?= ($filtros['orden'] ?? '') === 'stock' ? 'selected' : '' ?>>Stock</option>
                                <option value="marca" <?= ($filtros['orden'] ?? '') === 'marca' ? 'selected' : '' ?>>Marca</option>
                            </select>
                        </div>
                        
                        <!-- Botones -->
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search"></i> Buscar
                            </button>
                            <a href="<?= BASE_URL ?>/index.php?module=admin&action=inventario" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de autopartes -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <span>
                <strong><?= number_format($totalAutopartes) ?></strong> autopartes encontradas
            </span>
            <div class="btn-group btn-group-sm">
                <button type="button" class="btn btn-outline-secondary" id="btnVistaTabla" title="Vista tabla">
                    <i class="fas fa-list"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary" id="btnVistaGrid" title="Vista grid">
                    <i class="fas fa-th"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($autopartes)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron autopartes</h5>
                    <p class="text-muted">Intenta ajustar los filtros o agrega una nueva autoparte</p>
                    <?php if (hasPermission('inventario', 'crear')): ?>
                    <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-crear" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Agregar Autoparte
                    </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Vista Tabla -->
                <div class="table-responsive" id="vistaTabla">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 80px;">Imagen</th>
                                <th>Nombre</th>
                                <th>Marca / Modelo</th>
                                <th>Año</th>
                                <th>Categoría</th>
                                <th class="text-end">Precio</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center" style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($autopartes as $autoparte): ?>
                            <tr>
                                <td>
                                    <?php if ($autoparte['imagen_thumb']): ?>
                                        <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_thumb']) ?>" 
                                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                                             class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($autoparte['nombre']) ?></strong>
                                    <?php if ($autoparte['descripcion']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars(substr($autoparte['descripcion'], 0, 50)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($autoparte['marca']) ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($autoparte['modelo']) ?></small>
                                </td>
                                <td><?= $autoparte['anio'] ?></td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?></span>
                                </td>
                                <td class="text-end">
                                    <strong>$<?= number_format($autoparte['precio'], 2) ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php 
                                    $stockClass = 'bg-success';
                                    if ($autoparte['stock'] <= 0) {
                                        $stockClass = 'bg-danger';
                                    } elseif ($autoparte['stock'] <= 5) {
                                        $stockClass = 'bg-warning text-dark';
                                    }
                                    ?>
                                    <span class="badge <?= $stockClass ?>"><?= $autoparte['stock'] ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($autoparte['estado'] == 1): ?>
                                        <span class="badge bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-ver&id=<?= $autoparte['id'] ?>" 
                                           class="btn btn-outline-info" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php if (hasPermission('inventario', 'actualizar')): ?>
                                        <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-editar&id=<?= $autoparte['id'] ?>" 
                                           class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (hasPermission('inventario', 'eliminar')): ?>
                                            <?php if ($autoparte['estado'] == 1): ?>
                                            <button type="button" class="btn btn-outline-danger btn-desactivar" 
                                                    data-id="<?= $autoparte['id'] ?>" 
                                                    data-nombre="<?= htmlspecialchars($autoparte['nombre']) ?>"
                                                    title="Desactivar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                            <?php else: ?>
                                            <button type="button" class="btn btn-outline-success btn-activar" 
                                                    data-id="<?= $autoparte['id'] ?>" 
                                                    data-nombre="<?= htmlspecialchars($autoparte['nombre']) ?>"
                                                    title="Activar">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Vista Grid (oculta por defecto) -->
                <div class="row d-none" id="vistaGrid">
                    <?php foreach ($autopartes as $autoparte): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <?php if ($autoparte['imagen_thumb']): ?>
                                <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_thumb']) ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                                     style="height: 180px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="card-body">
                                <h6 class="card-title"><?= htmlspecialchars($autoparte['nombre']) ?></h6>
                                <p class="card-text text-muted small mb-2">
                                    <?= htmlspecialchars($autoparte['marca']) ?> - <?= htmlspecialchars($autoparte['modelo']) ?> (<?= $autoparte['anio'] ?>)
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong class="text-primary">$<?= number_format($autoparte['precio'], 2) ?></strong>
                                    <span class="badge <?= $autoparte['stock'] <= 5 ? 'bg-warning text-dark' : 'bg-success' ?>">
                                        Stock: <?= $autoparte['stock'] ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group btn-group-sm w-100">
                                    <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-ver&id=<?= $autoparte['id'] ?>" 
                                       class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if (hasPermission('inventario', 'actualizar')): ?>
                                    <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-editar&id=<?= $autoparte['id'] ?>" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                <nav aria-label="Paginación" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <!-- Anterior -->
                        <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $this->buildPaginationUrl($pagina - 1, $filtros) ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        
                        <!-- Números de página -->
                        <?php
                        $inicio = max(1, $pagina - 2);
                        $fin = min($totalPaginas, $pagina + 2);
                        
                        if ($inicio > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $this->buildPaginationUrl(1, $filtros) ?>">1</a>
                            </li>
                            <?php if ($inicio > 2): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                            <li class="page-item <?= $i === $pagina ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $this->buildPaginationUrl($i, $filtros) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($fin < $totalPaginas): ?>
                            <?php if ($fin < $totalPaginas - 1): ?>
                                <li class="page-item disabled"><span class="page-link">...</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="<?= $this->buildPaginationUrl($totalPaginas, $filtros) ?>"><?= $totalPaginas ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <!-- Siguiente -->
                        <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $this->buildPaginationUrl($pagina + 1, $filtros) ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="modalConfirmar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarTitulo">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="modalConfirmarMensaje"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarAccion">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cambio de vista
    const btnVistaTabla = document.getElementById('btnVistaTabla');
    const btnVistaGrid = document.getElementById('btnVistaGrid');
    const vistaTabla = document.getElementById('vistaTabla');
    const vistaGrid = document.getElementById('vistaGrid');
    
    btnVistaTabla.addEventListener('click', function() {
        vistaTabla.classList.remove('d-none');
        vistaGrid.classList.add('d-none');
        btnVistaTabla.classList.add('active');
        btnVistaGrid.classList.remove('active');
    });
    
    btnVistaGrid.addEventListener('click', function() {
        vistaGrid.classList.remove('d-none');
        vistaTabla.classList.add('d-none');
        btnVistaGrid.classList.add('active');
        btnVistaTabla.classList.remove('active');
    });
    
    // Modal de confirmación
    const modalConfirmar = new bootstrap.Modal(document.getElementById('modalConfirmar'));
    let accionActual = null;
    let idActual = null;
    
    // Desactivar
    document.querySelectorAll('.btn-desactivar').forEach(btn => {
        btn.addEventListener('click', function() {
            idActual = this.dataset.id;
            accionActual = 'desactivar';
            document.getElementById('modalConfirmarTitulo').textContent = 'Desactivar Autoparte';
            document.getElementById('modalConfirmarMensaje').textContent = 
                `¿Estás seguro de desactivar la autoparte "${this.dataset.nombre}"?`;
            document.getElementById('btnConfirmarAccion').className = 'btn btn-danger';
            modalConfirmar.show();
        });
    });
    
    // Activar
    document.querySelectorAll('.btn-activar').forEach(btn => {
        btn.addEventListener('click', function() {
            idActual = this.dataset.id;
            accionActual = 'activar';
            document.getElementById('modalConfirmarTitulo').textContent = 'Activar Autoparte';
            document.getElementById('modalConfirmarMensaje').textContent = 
                `¿Estás seguro de activar la autoparte "${this.dataset.nombre}"?`;
            document.getElementById('btnConfirmarAccion').className = 'btn btn-success';
            modalConfirmar.show();
        });
    });
    
    // Confirmar acción
    document.getElementById('btnConfirmarAccion').addEventListener('click', function() {
        const formData = new FormData();
        formData.append('id', idActual);
        
        const url = `<?= BASE_URL ?>/index.php?module=admin&action=autoparte-${accionActual}`;
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            modalConfirmar.hide();
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error al procesar la acción');
            }
        })
        .catch(error => {
            modalConfirmar.hide();
            alert('Error de conexión');
        });
    });
});
</script>

<?php
// Función helper para construir URL de paginación
function buildPaginationUrl($pagina, $filtros) {
    $params = ['module' => 'admin', 'action' => 'inventario', 'pagina' => $pagina];
    
    foreach ($filtros as $key => $value) {
        if (!empty($value) && $key !== 'limite' && $key !== 'offset') {
            $params[$key] = $value;
        }
    }
    
    return BASE_URL . '/index.php?' . http_build_query($params);
}

// Incluir footer del admin
require_once VIEWS_PATH . '/layouts/footer.php';
?>