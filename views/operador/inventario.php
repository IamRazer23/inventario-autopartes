<?php
/**
 * Vista: Inventario de Autopartes - Panel de Operador
 * Diseño con Tailwind CSS
 */

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-warehouse text-white text-xl"></i>
                </div>
                Inventario de Autopartes
            </h1>
            <p class="text-gray-500 mt-2">Gestiona el inventario de productos disponibles</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?module=operador&action=crear-autoparte" 
           class="inline-flex items-center gap-2 px-5 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl transition-all shadow-lg hover:shadow-xl transform hover:scale-105">
            <i class="fas fa-plus"></i>
            Agregar Autoparte
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total Activas -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Activas</p>
                    <h3 class="text-4xl font-bold"><?= number_format($totalActivas ?? 0) ?></h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-cogs text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Stock Bajo -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium mb-1">Stock Bajo</p>
                    <h3 class="text-4xl font-bold"><?= number_format($totalStockBajo ?? 0) ?></h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-exclamation-triangle text-2xl"></i>
                </div>
            </div>
            <?php if (($totalStockBajo ?? 0) > 0): ?>
            <a href="<?= BASE_URL ?>/index.php?module=operador&action=inventario&stock_bajo=1" class="text-amber-100 text-xs mt-2 inline-block hover:text-white">
                Ver productos con stock bajo →
            </a>
            <?php endif; ?>
        </div>

        <!-- Inactivas -->
        <div class="bg-gradient-to-br from-gray-500 to-gray-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-200 text-sm font-medium mb-1">Inactivas</p>
                    <h3 class="text-4xl font-bold"><?= number_format($totalInactivas ?? 0) ?></h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-ban text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Valor Total -->
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Valor Total</p>
                    <h3 class="text-3xl font-bold">$<?= number_format($valorInventario ?? 0, 2) ?></h3>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-dollar-sign text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-8 overflow-hidden">
        <button onclick="toggleFiltros()" class="w-full px-6 py-4 flex items-center justify-between bg-gray-50 hover:bg-gray-100 transition-colors">
            <h2 class="font-semibold text-gray-700 flex items-center gap-2">
                <i class="fas fa-filter text-blue-500"></i>
                Filtros de Búsqueda
            </h2>
            <i id="filtros-icon" class="fas fa-chevron-down text-gray-400 transition-transform"></i>
        </button>
        
        <div id="filtros-container" class="p-6 border-t border-gray-100">
            <form action="<?= BASE_URL ?>/index.php" method="GET" id="form-filtros">
                <input type="hidden" name="module" value="operador">
                <input type="hidden" name="action" value="inventario">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    
                    <!-- Búsqueda -->
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   name="buscar" 
                                   value="<?= htmlspecialchars($filtros['buscar'] ?? '') ?>"
                                   placeholder="Nombre, marca, modelo..."
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                        <select name="categoria" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">Todas</option>
                            <?php if (!empty($categorias)): ?>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($filtros['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Marca -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Marca</label>
                        <select name="marca" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">Todas</option>
                            <?php if (!empty($marcas)): ?>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?= htmlspecialchars($marca) ?>" <?= ($filtros['marca'] ?? '') == $marca ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($marca) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
                    
                    <!-- Año -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Año</label>
                        <select name="anio" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">Todos</option>
                            <?php if (!empty($anios)): ?>
                                <?php foreach ($anios as $anio): ?>
                                    <option value="<?= $anio ?>" <?= ($filtros['anio'] ?? '') == $anio ? 'selected' : '' ?>>
                                        <?= $anio ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Precio Mínimo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio Mín.</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                            <input type="number" 
                                   name="precio_min" 
                                   value="<?= htmlspecialchars($filtros['precio_min'] ?? '') ?>"
                                   placeholder="0.00"
                                   min="0"
                                   step="0.01"
                                   class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    <!-- Precio Máximo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Precio Máx.</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">$</span>
                            <input type="number" 
                                   name="precio_max" 
                                   value="<?= htmlspecialchars($filtros['precio_max'] ?? '') ?>"
                                   placeholder="999.99"
                                   min="0"
                                   step="0.01"
                                   class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    <!-- Estado -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select name="estado" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                            <option value="">Todos</option>
                            <option value="1" <?= ($filtros['estado'] ?? '') === '1' ? 'selected' : '' ?>>Activo</option>
                            <option value="0" <?= ($filtros['estado'] ?? '') === '0' ? 'selected' : '' ?>>Inactivo</option>
                        </select>
                    </div>

                    <!-- Stock Bajo -->
                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer py-2.5">
                            <input type="checkbox" 
                                   name="stock_bajo" 
                                   value="1"
                                   <?= isset($filtros['stock_bajo']) && $filtros['stock_bajo'] ? 'checked' : '' ?>
                                   class="w-5 h-5 text-amber-500 border-gray-300 rounded focus:ring-amber-500">
                            <span class="text-sm text-gray-700">Solo stock bajo</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="px-6 py-2.5 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-colors flex items-center gap-2">
                        <i class="fas fa-search"></i>
                        Filtrar
                    </button>
                    <a href="<?= BASE_URL ?>/index.php?module=operador&action=inventario" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors flex items-center gap-2">
                        <i class="fas fa-times"></i>
                        Limpiar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Resultados -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        <!-- Header de resultados -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <span class="text-gray-600">
                    <i class="fas fa-list mr-2"></i>
                    Mostrando <span class="font-semibold text-gray-800"><?= count($autopartes ?? []) ?></span> de 
                    <span class="font-semibold text-gray-800"><?= number_format($totalAutopartes ?? 0) ?></span> resultados
                </span>
            </div>
            <div class="flex items-center gap-3">
                <label class="text-sm text-gray-600">Ordenar por:</label>
                <select onchange="ordenarPor(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                    <option value="fecha_creacion-DESC" <?= ($filtros['orden'] ?? '') == 'fecha_creacion' && ($filtros['direccion'] ?? '') == 'DESC' ? 'selected' : '' ?>>Más recientes</option>
                    <option value="fecha_creacion-ASC" <?= ($filtros['orden'] ?? '') == 'fecha_creacion' && ($filtros['direccion'] ?? '') == 'ASC' ? 'selected' : '' ?>>Más antiguos</option>
                    <option value="nombre-ASC" <?= ($filtros['orden'] ?? '') == 'nombre' && ($filtros['direccion'] ?? '') == 'ASC' ? 'selected' : '' ?>>Nombre A-Z</option>
                    <option value="nombre-DESC" <?= ($filtros['orden'] ?? '') == 'nombre' && ($filtros['direccion'] ?? '') == 'DESC' ? 'selected' : '' ?>>Nombre Z-A</option>
                    <option value="precio-ASC" <?= ($filtros['orden'] ?? '') == 'precio' && ($filtros['direccion'] ?? '') == 'ASC' ? 'selected' : '' ?>>Precio menor</option>
                    <option value="precio-DESC" <?= ($filtros['orden'] ?? '') == 'precio' && ($filtros['direccion'] ?? '') == 'DESC' ? 'selected' : '' ?>>Precio mayor</option>
                    <option value="stock-ASC" <?= ($filtros['orden'] ?? '') == 'stock' && ($filtros['direccion'] ?? '') == 'ASC' ? 'selected' : '' ?>>Stock menor</option>
                    <option value="stock-DESC" <?= ($filtros['orden'] ?? '') == 'stock' && ($filtros['direccion'] ?? '') == 'DESC' ? 'selected' : '' ?>>Stock mayor</option>
                </select>
            </div>
        </div>

        <!-- Tabla de autopartes -->
        <?php if (!empty($autopartes)): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Producto</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Categoría</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Vehículo</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Precio</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($autopartes as $autoparte): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <!-- Producto -->
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 flex-shrink-0">
                                    <?php if (!empty($autoparte['thumbnail'])): ?>
                                        <img src="<?= htmlspecialchars($autoparte['thumbnail']) ?>" 
                                             alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                                             class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <i class="fas fa-image text-2xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800 hover:text-blue-600 transition-colors">
                                        <a href="<?= BASE_URL ?>/index.php?module=operador&action=ver-autoparte&id=<?= $autoparte['id'] ?>">
                                            <?= htmlspecialchars($autoparte['nombre']) ?>
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500">ID: #<?= $autoparte['id'] ?></p>
                                </div>
                            </div>
                        </td>

                        <!-- Categoría -->
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                <?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?>
                            </span>
                        </td>

                        <!-- Vehículo -->
                        <td class="px-6 py-4">
                            <div class="text-sm">
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($autoparte['marca']) ?></p>
                                <p class="text-gray-500"><?= htmlspecialchars($autoparte['modelo']) ?> (<?= $autoparte['anio'] ?>)</p>
                            </div>
                        </td>

                        <!-- Precio -->
                        <td class="px-6 py-4 text-center">
                            <span class="text-lg font-bold text-green-600">$<?= number_format($autoparte['precio'], 2) ?></span>
                        </td>

                        <!-- Stock -->
                        <td class="px-6 py-4 text-center">
                            <?php if ($autoparte['stock'] <= 5): ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-bold bg-red-100 text-red-700">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= $autoparte['stock'] ?>
                                </span>
                            <?php elseif ($autoparte['stock'] <= 10): ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-amber-100 text-amber-700">
                                    <?= $autoparte['stock'] ?>
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-green-100 text-green-700">
                                    <?= $autoparte['stock'] ?>
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Estado -->
                        <td class="px-6 py-4 text-center">
                            <?php if ($autoparte['estado']): ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Acciones -->
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= BASE_URL ?>/index.php?module=operador&action=ver-autoparte&id=<?= $autoparte['id'] ?>" 
                                   class="w-9 h-9 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors"
                                   title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= BASE_URL ?>/index.php?module=operador&action=editar-autoparte&id=<?= $autoparte['id'] ?>" 
                                   class="w-9 h-9 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100 transition-colors"
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="actualizarStock(<?= $autoparte['id'] ?>, <?= $autoparte['stock'] ?>, '<?= htmlspecialchars(addslashes($autoparte['nombre'])) ?>')" 
                                        class="w-9 h-9 flex items-center justify-center rounded-lg bg-green-50 text-green-600 hover:bg-green-100 transition-colors"
                                        title="Actualizar stock">
                                    <i class="fas fa-boxes"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <?php if (($totalPaginas ?? 1) > 1): ?>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <p class="text-sm text-gray-600">
                    Página <span class="font-semibold"><?= $pagina ?? 1 ?></span> de <span class="font-semibold"><?= $totalPaginas ?></span>
                </p>
                <div class="flex items-center gap-2">
                    <?php 
                    $queryParams = $_GET;
                    unset($queryParams['pagina']);
                    $queryString = http_build_query($queryParams);
                    ?>
                    
                    <!-- Anterior -->
                    <?php if (($pagina ?? 1) > 1): ?>
                        <a href="<?= BASE_URL ?>/index.php?<?= $queryString ?>&pagina=<?= $pagina - 1 ?>" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <!-- Números de página -->
                    <?php
                    $inicio = max(1, ($pagina ?? 1) - 2);
                    $fin = min($totalPaginas, ($pagina ?? 1) + 2);
                    
                    if ($inicio > 1): ?>
                        <a href="<?= BASE_URL ?>/index.php?<?= $queryString ?>&pagina=1" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">1</a>
                        <?php if ($inicio > 2): ?>
                            <span class="px-2 text-gray-400">...</span>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                        <a href="<?= BASE_URL ?>/index.php?<?= $queryString ?>&pagina=<?= $i ?>" 
                           class="px-4 py-2 rounded-lg transition-colors <?= $i == ($pagina ?? 1) ? 'bg-blue-500 text-white' : 'bg-white border border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php if ($fin < $totalPaginas): ?>
                        <?php if ($fin < $totalPaginas - 1): ?>
                            <span class="px-2 text-gray-400">...</span>
                        <?php endif; ?>
                        <a href="<?= BASE_URL ?>/index.php?<?= $queryString ?>&pagina=<?= $totalPaginas ?>" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"><?= $totalPaginas ?></a>
                    <?php endif; ?>
                    
                    <!-- Siguiente -->
                    <?php if (($pagina ?? 1) < $totalPaginas): ?>
                        <a href="<?= BASE_URL ?>/index.php?<?= $queryString ?>&pagina=<?= $pagina + 1 ?>" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <!-- Sin resultados -->
        <div class="py-16 text-center">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-box-open text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No se encontraron autopartes</h3>
            <p class="text-gray-500 mb-6">Intenta cambiar los filtros de búsqueda o agrega nuevos productos</p>
            <a href="<?= BASE_URL ?>/index.php?module=operador&action=crear-autoparte" 
               class="inline-flex items-center gap-2 px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-xl transition-colors">
                <i class="fas fa-plus"></i>
                Agregar Primera Autoparte
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal para actualizar stock -->
<div id="modal-stock" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden transform transition-all">
        <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
            <h3 class="text-white font-semibold text-lg flex items-center gap-2">
                <i class="fas fa-boxes"></i>
                Actualizar Stock
            </h3>
        </div>
        <form action="<?= BASE_URL ?>/index.php?module=operador&action=actualizar-stock" method="POST" class="p-6">
            <input type="hidden" name="id" id="stock-id">
            <input type="hidden" name="redirect" value="inventario">
            
            <p class="text-gray-600 mb-4">
                Producto: <span id="stock-nombre" class="font-semibold text-gray-800"></span>
            </p>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nueva cantidad en stock</label>
                <input type="number" 
                       name="stock" 
                       id="stock-cantidad" 
                       min="0" 
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-center text-2xl font-bold">
            </div>
            
            <div class="flex gap-3">
                <button type="button" 
                        onclick="cerrarModalStock()"
                        class="flex-1 px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl transition-colors">
                    Cancelar
                </button>
                <button type="submit" 
                        class="flex-1 px-4 py-3 bg-green-500 hover:bg-green-600 text-white font-medium rounded-xl transition-colors">
                    <i class="fas fa-save mr-2"></i>Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle filtros
function toggleFiltros() {
    const container = document.getElementById('filtros-container');
    const icon = document.getElementById('filtros-icon');
    container.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

// Ordenar
function ordenarPor(value) {
    const [orden, direccion] = value.split('-');
    const url = new URL(window.location.href);
    url.searchParams.set('orden', orden);
    url.searchParams.set('direccion', direccion);
    window.location.href = url.toString();
}

// Modal stock
function actualizarStock(id, stockActual, nombre) {
    document.getElementById('stock-id').value = id;
    document.getElementById('stock-cantidad').value = stockActual;
    document.getElementById('stock-nombre').textContent = nombre;
    document.getElementById('modal-stock').classList.remove('hidden');
    document.getElementById('modal-stock').classList.add('flex');
    document.getElementById('stock-cantidad').focus();
}

function cerrarModalStock() {
    document.getElementById('modal-stock').classList.add('hidden');
    document.getElementById('modal-stock').classList.remove('flex');
}

// Cerrar modal con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModalStock();
    }
});

// Cerrar modal al hacer clic fuera
document.getElementById('modal-stock').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalStock();
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
