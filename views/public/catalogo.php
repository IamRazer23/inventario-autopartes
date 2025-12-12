<?php
/**
 * Vista: Catálogo Público
 * MODIFICADO: Las imágenes se cargan directamente desde URLs externas
 */

$pageTitle = isset($categoria) ? $categoria['nombre'] . ' - Catálogo' : 'Catálogo - AutoPartes Pro';
require_once VIEWS_PATH . '/layouts/header.php';

// Imagen por defecto cuando no hay URL
$defaultImage = 'https://via.placeholder.com/300x300?text=Sin+Imagen';

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
                    
                    <!-- Categorías -->
                    <?php if (!isset($categoria) && !empty($categorias)): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Categoría</label>
                        <select name="categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($filtros['categoria_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?> (<?= $cat['total_autopartes'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Marca -->
                    <?php if (!empty($marcas)): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Marca</label>
                        <select name="marca" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todas las marcas</option>
                            <?php foreach ($marcas as $m): ?>
                                <option value="<?= htmlspecialchars($m) ?>" <?= ($filtros['marca'] ?? '') == $m ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($m) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Año -->
                    <?php if (!empty($anios)): ?>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Año</label>
                        <select name="anio" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Todos los años</option>
                            <?php foreach ($anios as $a): ?>
                                <option value="<?= $a ?>" <?= ($filtros['anio'] ?? '') == $a ? 'selected' : '' ?>>
                                    <?= $a ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Rango de Precio -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Rango de Precio</label>
                        <div class="flex gap-2">
                            <input type="number" name="precio_min" placeholder="Min" min="0" step="0.01"
                                   value="<?= htmlspecialchars($filtros['precio_min'] ?? '') ?>"
                                   class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                            <input type="number" name="precio_max" placeholder="Max" min="0" step="0.01"
                                   value="<?= htmlspecialchars($filtros['precio_max'] ?? '') ?>"
                                   class="w-1/2 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>
                    
                    <!-- Ordenar -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Ordenar por</label>
                        <select name="orden" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="fecha_creacion" <?= ($filtros['orden'] ?? '') == 'fecha_creacion' ? 'selected' : '' ?>>Más recientes</option>
                            <option value="precio" <?= ($filtros['orden'] ?? '') == 'precio' ? 'selected' : '' ?>>Precio</option>
                            <option value="nombre" <?= ($filtros['orden'] ?? '') == 'nombre' ? 'selected' : '' ?>>Nombre</option>
                        </select>
                    </div>
                    
                    <div class="flex gap-2 pt-2">
                        <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded-lg font-semibold transition">
                            <i class="fas fa-search mr-2"></i>Filtrar
                        </button>
                        <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg transition">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- Contenido Principal -->
        <main class="flex-1">
            
            <!-- Barra de resultados -->
            <div class="flex items-center justify-between mb-6 bg-white rounded-lg shadow-sm p-4">
                <p class="text-gray-600">
                    <span class="font-semibold text-gray-800"><?= $totalAutopartes ?></span> productos encontrados
                </p>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Dirección:</span>
                    <a href="<?= buildCatalogoUrl(['direccion' => 'ASC']) ?>" 
                       class="p-2 rounded <?= ($filtros['direccion'] ?? 'DESC') == 'ASC' ? 'bg-indigo-100 text-indigo-600' : 'text-gray-400 hover:text-gray-600' ?>">
                        <i class="fas fa-sort-amount-up"></i>
                    </a>
                    <a href="<?= buildCatalogoUrl(['direccion' => 'DESC']) ?>" 
                       class="p-2 rounded <?= ($filtros['direccion'] ?? 'DESC') == 'DESC' ? 'bg-indigo-100 text-indigo-600' : 'text-gray-400 hover:text-gray-600' ?>">
                        <i class="fas fa-sort-amount-down"></i>
                    </a>
                </div>
            </div>

            <!-- Grid de Productos -->
            <?php if (!empty($autopartes)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($autopartes as $producto): ?>
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all group">
                            <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $producto['id'] ?>" class="block">
                                <div class="relative overflow-hidden h-48">
                                    <!-- IMAGEN DESDE URL -->
                                    <?php if (!empty($producto['imagen_thumb'])): ?>
                                        <img src="<?= htmlspecialchars($producto['imagen_thumb']) ?>" 
                                             alt="<?= htmlspecialchars($producto['nombre']) ?>"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                             onerror="this.onerror=null; this.src='<?= $defaultImage ?>';">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 flex items-center justify-center">
                                            <i class="fas fa-car text-gray-400 text-4xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Badge Categoría -->
                                    <span class="absolute top-3 left-3 bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                                        <?= htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría') ?>
                                    </span>
                                    
                                    <!-- Badge Stock -->
                                    <?php if ($producto['stock'] <= 0): ?>
                                        <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                            Agotado
                                        </span>
                                    <?php elseif ($producto['stock'] <= 5): ?>
                                        <span class="absolute top-3 right-3 bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                                            ¡Últimas unidades!
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition line-clamp-2 mb-2">
                                        <?= htmlspecialchars($producto['nombre']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-2">
                                        <?= htmlspecialchars($producto['marca']) ?> <?= htmlspecialchars($producto['modelo']) ?> • <?= $producto['anio'] ?>
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xl font-bold text-indigo-600">
                                            $<?= number_format($producto['precio'], 2) ?>
                                        </span>
                                        <?php if ($producto['stock'] > 0): ?>
                                            <span class="text-xs text-green-600 font-medium">
                                                <i class="fas fa-check-circle mr-1"></i><?= $producto['stock'] ?> disponibles
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Paginación -->
                <?php if ($totalPaginas > 1): ?>
                    <div class="flex justify-center mt-8">
                        <nav class="flex items-center space-x-2">
                            <?php if ($pagina > 1): ?>
                                <a href="<?= buildCatalogoUrl(['pagina' => $pagina - 1]) ?>" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php 
                            $inicio = max(1, $pagina - 2);
                            $fin = min($totalPaginas, $pagina + 2);
                            ?>
                            
                            <?php if ($inicio > 1): ?>
                                <a href="<?= buildCatalogoUrl(['pagina' => 1]) ?>" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">1</a>
                                <?php if ($inicio > 2): ?>
                                    <span class="px-2 text-gray-400">...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $inicio; $i <= $fin; $i++): ?>
                                <a href="<?= buildCatalogoUrl(['pagina' => $i]) ?>" 
                                   class="px-4 py-2 border rounded-lg transition <?= $i == $pagina ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white border-gray-300 hover:bg-gray-50' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($fin < $totalPaginas): ?>
                                <?php if ($fin < $totalPaginas - 1): ?>
                                    <span class="px-2 text-gray-400">...</span>
                                <?php endif; ?>
                                <a href="<?= buildCatalogoUrl(['pagina' => $totalPaginas]) ?>" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"><?= $totalPaginas ?></a>
                            <?php endif; ?>
                            
                            <?php if ($pagina < $totalPaginas): ?>
                                <a href="<?= buildCatalogoUrl(['pagina' => $pagina + 1]) ?>" 
                                   class="px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- Sin resultados -->
                <div class="bg-white rounded-xl shadow-md p-12 text-center">
                    <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No se encontraron productos</h3>
                    <p class="text-gray-600 mb-6">Intenta ajustar los filtros de búsqueda</p>
                    <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
                       class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-6 py-3 rounded-lg transition">
                        <i class="fas fa-redo mr-2"></i>Ver todos los productos
                    </a>
                </div>
            <?php endif; ?>
            
        </main>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>