<?php
/**
 * Vista: Catálogo Público
 * Cumple con requisito 8: Página pública con listado de autopartes
 */

$pageTitle = isset($categoria) ? $categoria['nombre'] . ' - Catálogo' : 'Catálogo - AutoPartes Pro';
require_once VIEWS_PATH . '/layouts/header.php';

// Helper para construir URLs
function buildCatalogoUrl($params = []) {
    global $filtros, $categoria, $pagina;
    
    $baseParams = [
        'module' => 'publico',
        'action' => isset($categoria) ? 'categoria' : 'catalogo'
    ];
    
    if (isset($categoria)) {
        $baseParams['id'] = $categoria['id'];
    }
    
    $currentParams = [
        'buscar' => $filtros['buscar'] ?? '',
        'marca' => $filtros['marca'] ?? '',
        'anio' => $filtros['anio'] ?? '',
        'precio_min' => $filtros['precio_min'] ?? '',
        'precio_max' => $filtros['precio_max'] ?? '',
        'orden' => $filtros['orden'] ?? 'fecha_creacion',
        'direccion' => $filtros['direccion'] ?? 'DESC',
        'pagina' => $pagina ?? 1
    ];
    
    if (!isset($categoria)) {
        $currentParams['categoria'] = $filtros['categoria_id'] ?? '';
    }
    
    $finalParams = array_merge($baseParams, $currentParams, $params);
    $finalParams = array_filter($finalParams, function($v) { return $v !== ''; });
    
    return BASE_URL . '/index.php?' . http_build_query($finalParams);
}
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <?php if (isset($categoria)): ?>
                <li><a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" class="hover:text-indigo-600">Catálogo</a></li>
                <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
                <li class="text-indigo-600 font-medium"><?= htmlspecialchars($categoria['nombre']) ?></li>
            <?php else: ?>
                <li class="text-indigo-600 font-medium">Catálogo</li>
            <?php endif; ?>
        </ol>
    </nav>

    <!-- Título -->
    <div class="mb-8">
        <?php if (isset($categoria)): ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-tag text-indigo-600 mr-2"></i><?= htmlspecialchars($categoria['nombre']) ?>
            </h1>
            <?php if (!empty($categoria['descripcion'])): ?>
                <p class="text-gray-600"><?= htmlspecialchars($categoria['descripcion']) ?></p>
            <?php endif; ?>
        <?php else: ?>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-th-large text-indigo-600 mr-2"></i>Catálogo de Autopartes
            </h1>
            <p class="text-gray-600">Encuentra la pieza que necesitas para tu vehículo</p>
        <?php endif; ?>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Sidebar Filtros -->
        <aside class="lg:w-72 flex-shrink-0">
            <div class="bg-white rounded-xl shadow-md overflow-hidden sticky top-24">
                <div class="bg-indigo-600 text-white px-6 py-4">
                    <h2 class="font-bold text-lg"><i class="fas fa-filter mr-2"></i>Filtros</h2>
                </div>
                <form method="GET" action="" class="p-6 space-y-5">
                    <input type="hidden" name="module" value="publico">
                    <input type="hidden" name="action" value="<?= isset($categoria) ? 'categoria' : 'catalogo' ?>">
                    <?php if (isset($categoria)): ?>
                        <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
                    <?php endif; ?>
                    
                    <!-- Búsqueda -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar</label>
                        <input type="text" name="buscar" value="<?= htmlspecialchars($filtros['buscar'] ?? '') ?>"
                               placeholder="Nombre, marca..." 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    </div>
                    
                    <?php if (!isset($categoria)): ?>
                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Categoría</label>
                        <select name="categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Todas</option>
                            <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($filtros['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['nombre']) ?> (<?= $cat['total_autopartes'] ?? 0 ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Marca -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Marca</label>
                        <select name="marca" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Todas</option>
                            <?php foreach ($marcas as $marca): ?>
                            <option value="<?= htmlspecialchars($marca) ?>" <?= ($filtros['marca'] ?? '') == $marca ? 'selected' : '' ?>>
                                <?= htmlspecialchars($marca) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Año -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Año</label>
                        <select name="anio" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="">Todos</option>
                            <?php foreach ($anios as $anio): ?>
                            <option value="<?= $anio ?>" <?= ($filtros['anio'] ?? '') == $anio ? 'selected' : '' ?>><?= $anio ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Precio -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Precio</label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" name="precio_min" value="<?= htmlspecialchars($filtros['precio_min'] ?? '') ?>"
                                   placeholder="Mín" min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                            <input type="number" name="precio_max" value="<?= htmlspecialchars($filtros['precio_max'] ?? '') ?>"
                                   placeholder="Máx" min="0" step="0.01"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
                        </div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="space-y-2 pt-2">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition">
                            <i class="fas fa-search mr-2"></i>Buscar
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
                           class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2.5 px-4 rounded-lg transition">
                            <i class="fas fa-times mr-2"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Listado -->
        <div class="flex-1">
            <!-- Barra superior -->
            <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-gray-600">
                    <strong class="text-gray-800"><?= number_format($totalAutopartes) ?></strong> autopartes encontradas
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Ordenar:</span>
                    <select onchange="window.location.href=this.value" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        <option value="<?= buildCatalogoUrl(['orden' => 'fecha_creacion', 'direccion' => 'DESC']) ?>" <?= ($filtros['orden'] ?? '') == 'fecha_creacion' ? 'selected' : '' ?>>Más recientes</option>
                        <option value="<?= buildCatalogoUrl(['orden' => 'precio', 'direccion' => 'ASC']) ?>" <?= ($filtros['orden'] ?? '') == 'precio' && ($filtros['direccion'] ?? '') == 'ASC' ? 'selected' : '' ?>>Precio: menor a mayor</option>
                        <option value="<?= buildCatalogoUrl(['orden' => 'precio', 'direccion' => 'DESC']) ?>" <?= ($filtros['orden'] ?? '') == 'precio' && ($filtros['direccion'] ?? '') == 'DESC' ? 'selected' : '' ?>>Precio: mayor a menor</option>
                        <option value="<?= buildCatalogoUrl(['orden' => 'nombre', 'direccion' => 'ASC']) ?>" <?= ($filtros['orden'] ?? '') == 'nombre' ? 'selected' : '' ?>>Nombre A-Z</option>
                    </select>
                </div>
            </div>

            <?php if (empty($autopartes)): ?>
                <!-- Sin resultados -->
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">No se encontraron autopartes</h3>
                    <p class="text-gray-500 mb-6">Intenta ajustar los filtros de búsqueda</p>
                    <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" class="inline-flex items-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg transition">
                        <i class="fas fa-redo mr-2"></i>Ver todo el catálogo
                    </a>
                </div>
            <?php else: ?>
                <!-- Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($autopartes as $autoparte): ?>
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all overflow-hidden group">
                        <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $autoparte['id'] ?>" class="block relative">
                            <?php if ($autoparte['imagen_thumb']): ?>
                                <img src="<?= UPLOADS_URL . '/' . htmlspecialchars($autoparte['imagen_thumb']) ?>" 
                                     alt="<?= htmlspecialchars($autoparte['nombre']) ?>" 
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-48 bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                    <i class="fas fa-car text-gray-400 text-5xl"></i>
                                </div>
                            <?php endif; ?>
                            <span class="absolute top-3 left-3 bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded">
                                <?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?>
                            </span>
                            <?php if ($autoparte['stock'] <= 3 && $autoparte['stock'] > 0): ?>
                            <span class="absolute top-3 right-3 bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded">¡Últimas <?= $autoparte['stock'] ?>!</span>
                            <?php elseif ($autoparte['stock'] <= 0): ?>
                            <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">Agotado</span>
                            <?php endif; ?>
                        </a>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-800 mb-2 line-clamp-1 group-hover:text-indigo-600 transition">
                                <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $autoparte['id'] ?>">
                                    <?= htmlspecialchars($autoparte['nombre']) ?>
                                </a>
                            </h3>
                            <p class="text-sm text-gray-500 mb-3">
                                <i class="fas fa-car mr-1"></i>
                                <?= htmlspecialchars($autoparte['marca']) ?> <?= htmlspecialchars($autoparte['modelo']) ?>
                                <span class="ml-1 bg-gray-200 text-gray-600 text-xs px-2 py-0.5 rounded"><?= $autoparte['anio'] ?></span>
                            </p>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xl font-bold text-indigo-600">$<?= number_format($autoparte['precio'], 2) ?></span>
                                <?php if ($autoparte['stock'] > 0): ?>
                                    <span class="text-xs text-green-600"><i class="fas fa-check-circle mr-1"></i>En stock</span>
                                <?php else: ?>
                                    <span class="text-xs text-red-600"><i class="fas fa-times-circle mr-1"></i>Agotado</span>
                                <?php endif; ?>
                            </div>
                            <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $autoparte['id'] ?>" 
                               class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 rounded-lg transition">
                                <i class="fas fa-eye mr-2"></i>Ver Detalle
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                <nav class="mt-8 flex justify-center">
                    <ul class="flex items-center space-x-1">
                        <li>
                            <a href="<?= buildCatalogoUrl(['pagina' => max(1, $pagina - 1)]) ?>" 
                               class="px-4 py-2 rounded-lg border <?= $pagina <= 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-indigo-50' ?> transition">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>
                        <?php for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++): ?>
                        <li>
                            <a href="<?= buildCatalogoUrl(['pagina' => $i]) ?>" 
                               class="px-4 py-2 rounded-lg border <?= $i === $pagina ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-indigo-50' ?> transition">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        <li>
                            <a href="<?= buildCatalogoUrl(['pagina' => min($totalPaginas, $pagina + 1)]) ?>" 
                               class="px-4 py-2 rounded-lg border <?= $pagina >= $totalPaginas ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-gray-700 hover:bg-indigo-50' ?> transition">
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

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>