<?php
/**
 * Vista: Listado de Autopartes (Inventario)
 * 
 * @author Grupo 1SF131
 */

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-warehouse text-indigo-600 mr-2"></i>
                Inventario de Autopartes
            </h1>
            <p class="text-gray-600 mt-1">Gestiona el inventario de autopartes del sistema</p>
        </div>
        
        <?php if (hasPermission('inventario', 'crear')): ?>
        <div class="mt-4 md:mt-0">
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-crear" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold inline-flex items-center shadow-md hover:shadow-lg transition-all">
                <i class="fas fa-plus mr-2"></i>
                Nueva Autoparte
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Estadísticas Rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-600 text-sm font-medium">Total</p>
                    <p class="text-2xl font-bold text-blue-700"><?= number_format($totalAutopartes ?? 0) ?></p>
                </div>
                <i class="fas fa-boxes text-blue-400 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-600 text-sm font-medium">Activas</p>
                    <p class="text-2xl font-bold text-green-700"><?= number_format($totalActivas ?? 0) ?></p>
                </div>
                <i class="fas fa-check-circle text-green-400 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-600 text-sm font-medium">Inactivas</p>
                    <p class="text-2xl font-bold text-red-700"><?= number_format($totalInactivas ?? 0) ?></p>
                </div>
                <i class="fas fa-ban text-red-400 text-3xl"></i>
            </div>
        </div>
        
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-600 text-sm font-medium">Stock Bajo</p>
                    <p class="text-2xl font-bold text-yellow-700"><?= number_format($totalStockBajo ?? 0) ?></p>
                </div>
                <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl"></i>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="<?= BASE_URL ?>/index.php" class="space-y-4">
            <input type="hidden" name="module" value="admin">
            <input type="hidden" name="action" value="autopartes">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Búsqueda -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-search text-gray-400 mr-1"></i>
                        Buscar
                    </label>
                    <input 
                        type="text" 
                        name="buscar" 
                        value="<?= e($filtros['buscar'] ?? '') ?>"
                        placeholder="Nombre, descripción, marca..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>
                
                <!-- Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-folder text-gray-400 mr-1"></i>
                        Categoría
                    </label>
                    <select 
                        name="categoria" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Todas las categorías</option>
                        <?php if (!empty($categorias)): ?>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($filtros['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= e($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-toggle-on text-gray-400 mr-1"></i>
                        Estado
                    </label>
                    <select 
                        name="estado" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Todos</option>
                        <option value="1" <?= ($filtros['estado'] ?? '') === '1' ? 'selected' : '' ?>>Activas</option>
                        <option value="0" <?= ($filtros['estado'] ?? '') === '0' ? 'selected' : '' ?>>Inactivas</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Marca -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
                    <select 
                        name="marca" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Todas las marcas</option>
                        <?php if (!empty($marcas)): ?>
                            <?php foreach ($marcas as $marca): ?>
                                <option value="<?= e($marca) ?>" <?= ($filtros['marca'] ?? '') == $marca ? 'selected' : '' ?>>
                                    <?= e($marca) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- Año -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                    <select 
                        name="anio" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                    >
                        <option value="">Todos los años</option>
                        <?php if (!empty($anios)): ?>
                            <?php foreach ($anios as $anio): ?>
                                <option value="<?= $anio ?>" <?= ($filtros['anio'] ?? '') == $anio ? 'selected' : '' ?>>
                                    <?= $anio ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <!-- Stock bajo -->
                <div class="flex items-end">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="stock_bajo" 
                            value="1"
                            <?= !empty($filtros['stock_bajo']) ? 'checked' : '' ?>
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                        >
                        <span class="ml-2 text-sm text-gray-700">Solo stock bajo</span>
                    </label>
                </div>
                
                <!-- Botones -->
                <div class="flex items-end gap-2">
                    <button 
                        type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold transition"
                    >
                        <i class="fas fa-search mr-2"></i>
                        Buscar
                    </button>
                    <a 
                        href="<?= BASE_URL ?>/index.php?module=admin&action=autopartes"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold transition"
                    >
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabla de Autopartes -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <?php if (!empty($autopartes)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Autoparte
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Categoría
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Marca / Modelo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($autopartes as $autoparte): ?>
                            <tr class="hover:bg-gray-50">
                                <!-- Autoparte -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 flex-shrink-0">
                                            <?php if (!empty($autoparte['thumbnail'])): ?>
                                                <img 
                                                    class="h-12 w-12 rounded-lg object-cover" 
                                                    src="<?= UPLOADS_URL ?>/<?= e($autoparte['thumbnail']) ?>" 
                                                    alt="<?= e($autoparte['nombre']) ?>"
                                                >
                                            <?php else: ?>
                                                <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <i class="fas fa-cog text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= e($autoparte['nombre']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ID: <?= $autoparte['id'] ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                
                                <!-- Categoría -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= e($autoparte['categoria_nombre'] ?? 'Sin categoría') ?>
                                    </span>
                                </td>
                                
                                <!-- Marca / Modelo -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= e($autoparte['marca']) ?></div>
                                    <div class="text-sm text-gray-500"><?= e($autoparte['modelo']) ?> (<?= $autoparte['anio'] ?>)</div>
                                </td>
                                
                                <!-- Precio -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        <?= formatCurrency($autoparte['precio']) ?>
                                    </div>
                                </td>
                                
                                <!-- Stock -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($autoparte['stock'] <= 5): ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>
                                            <?= $autoparte['stock'] ?>
                                        </span>
                                    <?php elseif ($autoparte['stock'] <= 10): ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <?= $autoparte['stock'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <?= $autoparte['stock'] ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Estado -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($autoparte['estado'] == 1): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activa
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactiva
                                        </span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Acciones -->
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-2">
                                        <!-- Ver -->
                                        <button 
                                            onclick="verDetalle(<?= $autoparte['id'] ?>)"
                                            class="text-blue-600 hover:text-blue-900"
                                            title="Ver detalle"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if (hasPermission('inventario', 'actualizar')): ?>
                                            <!-- Editar -->
                                            <a 
                                                href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-editar&id=<?= $autoparte['id'] ?>"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                title="Editar"
                                            >
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (hasPermission('inventario', 'eliminar')): ?>
                                            <!-- Activar/Desactivar -->
                                            <?php if ($autoparte['estado'] == 1): ?>
                                                <button 
                                                    onclick="toggleEstado(<?= $autoparte['id'] ?>, 0, '<?= e($autoparte['nombre']) ?>')"
                                                    class="text-red-600 hover:text-red-900"
                                                    title="Desactivar"
                                                >
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <button 
                                                    onclick="toggleEstado(<?= $autoparte['id'] ?>, 1, '<?= e($autoparte['nombre']) ?>')"
                                                    class="text-green-600 hover:text-green-900"
                                                    title="Activar"
                                                >
                                                    <i class="fas fa-check-circle"></i>
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
            
            <!-- Paginación -->
            <?php if (isset($totalPaginas) && $totalPaginas > 1): ?>
            <div class="bg-gray-50 px-6 py-4 border-t">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Mostrando <span class="font-medium"><?= count($autopartes) ?></span> de 
                        <span class="font-medium"><?= $totalAutopartes ?></span> autopartes
                    </div>
                    <nav class="flex space-x-2">
                        <?php if ($pagina > 1): ?>
                            <a href="<?= BASE_URL ?>/index.php?module=admin&action=autopartes&pagina=<?= $pagina - 1 ?><?= http_build_query(array_filter($filtros, fn($v) => $v !== '')) ? '&' . http_build_query(array_filter($filtros, fn($v) => $v !== '')) : '' ?>" 
                               class="px-3 py-2 rounded-lg bg-white border hover:bg-gray-50">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++): ?>
                            <a href="<?= BASE_URL ?>/index.php?module=admin&action=autopartes&pagina=<?= $i ?>" 
                               class="px-3 py-2 rounded-lg <?= $i === $pagina ? 'bg-indigo-600 text-white' : 'bg-white border hover:bg-gray-50' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($pagina < $totalPaginas): ?>
                            <a href="<?= BASE_URL ?>/index.php?module=admin&action=autopartes&pagina=<?= $pagina + 1 ?>" 
                               class="px-3 py-2 rounded-lg bg-white border hover:bg-gray-50">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            </div>
            <?php endif; ?>
            
        <?php else: ?>
            <!-- Estado Vacío -->
            <div class="text-center py-12">
                <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No hay autopartes</h3>
                <p class="text-gray-500 mb-6">
                    <?php if (!empty($filtros['buscar']) || !empty($filtros['categoria_id']) || ($filtros['estado'] ?? '') !== ''): ?>
                        No se encontraron autopartes con los filtros seleccionados
                    <?php else: ?>
                        Aún no hay autopartes registradas en el sistema
                    <?php endif; ?>
                </p>
                <?php if (hasPermission('inventario', 'crear')): ?>
                    <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-crear" 
                       class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                        <i class="fas fa-plus mr-2"></i>
                        Agregar Primera Autoparte
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<!-- Modal de Detalle -->
<div id="modalDetalle" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">Detalle de Autoparte</h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="contenidoDetalle">
                <!-- Se llena con AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
// Ver detalle de autoparte
function verDetalle(id) {
    const modal = document.getElementById('modalDetalle');
    const contenido = document.getElementById('contenidoDetalle');
    
    contenido.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-4xl text-indigo-600"></i></div>';
    modal.classList.remove('hidden');
    
    fetch('<?= BASE_URL ?>/index.php?module=admin&action=autoparte-detalle&id=' + id, {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const a = data.autoparte;
            contenido.innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center space-x-4 pb-4 border-b">
                        ${a.thumbnail 
                            ? `<img src="<?= UPLOADS_URL ?>/${a.thumbnail}" class="h-20 w-20 rounded-lg object-cover" alt="${a.nombre}">`
                            : `<div class="h-20 w-20 rounded-lg bg-gray-200 flex items-center justify-center"><i class="fas fa-cog text-gray-400 text-2xl"></i></div>`
                        }
                        <div>
                            <h4 class="text-xl font-bold text-gray-800">${a.nombre}</h4>
                            <p class="text-gray-600">${a.marca} ${a.modelo} (${a.anio})</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Categoría</p>
                            <p class="font-semibold text-gray-800">${a.categoria_nombre || 'Sin categoría'}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Precio</p>
                            <p class="font-semibold text-green-600">$${parseFloat(a.precio).toFixed(2)}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Stock</p>
                            <p class="font-semibold ${a.stock <= 5 ? 'text-red-600' : 'text-gray-800'}">${a.stock} unidades</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Estado</p>
                            <p class="font-semibold ${a.estado == 1 ? 'text-green-600' : 'text-red-600'}">
                                ${a.estado == 1 ? 'Activa' : 'Inactiva'}
                            </p>
                        </div>
                    </div>
                    
                    ${a.descripcion ? `
                    <div class="pt-4 border-t">
                        <p class="text-sm text-gray-600 mb-1">Descripción</p>
                        <p class="text-gray-800">${a.descripcion}</p>
                    </div>
                    ` : ''}
                    
                    <div class="pt-4 border-t flex gap-2">
                        <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-editar&id=${a.id}" 
                           class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-center font-semibold transition">
                            <i class="fas fa-edit mr-2"></i>Editar
                        </a>
                        <button onclick="cerrarModal()" 
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-semibold transition">
                            Cerrar
                        </button>
                    </div>
                </div>
            `;
        } else {
            contenido.innerHTML = '<p class="text-red-600">Error: ' + data.message + '</p>';
        }
    })
    .catch(error => {
        contenido.innerHTML = '<p class="text-red-600">Error al cargar el detalle</p>';
    });
}

// Toggle Estado (Activar/Desactivar)
function toggleEstado(id, nuevoEstado, nombre) {
    const accion = nuevoEstado == 1 ? 'activar' : 'desactivar';
    const mensaje = nuevoEstado == 1 
        ? `¿Activar la autoparte "${nombre}"?` 
        : `¿Desactivar la autoparte "${nombre}"?`;
    
    if (!confirm(mensaje)) return;
    
    const formData = new FormData();
    formData.append('id', id);
    
    fetch('<?= BASE_URL ?>/index.php?module=admin&action=autoparte-' + (nuevoEstado == 1 ? 'activar' : 'eliminar'), {
        method: 'POST',
        body: formData,
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}

// Cerrar Modal
function cerrarModal() {
    document.getElementById('modalDetalle').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalDetalle').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>