<?php
/**
 * Vista: Categorías - Operador (Solo Lectura)
 * Lista de categorías sin opciones de edición
 */

$pageTitle = $pageTitle ?? 'Categorías';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-tags text-blue-600 mr-2"></i>
                Categorías
            </h1>
            <p class="text-gray-600">
                <i class="fas fa-lock text-yellow-500 mr-1"></i>
                Vista de solo lectura - No puedes modificar las categorías
            </p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?module=operador&action=dashboard" 
           class="mt-4 md:mt-0 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
        </a>
    </div>

    <!-- Aviso de Solo Lectura -->
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-yellow-400 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-yellow-800">Modo de Solo Lectura</p>
                <p class="text-yellow-700 text-sm">
                    Como operador, solo puedes ver las categorías existentes. 
                    Para crear, editar o eliminar categorías, contacta a un administrador.
                </p>
            </div>
        </div>
    </div>

    <!-- Grid de Categorías -->
    <?php if (!empty($categorias)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <?php foreach ($categorias as $categoria): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Imagen de categoría -->
                    <div class="h-32 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
                        <?php if (!empty($categoria['imagen'])): ?>
                            <img src="<?= e($categoria['imagen']) ?>" 
                                 alt="<?= e($categoria['nombre']) ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-folder text-white text-4xl"></i>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 text-lg mb-2">
                            <?= e($categoria['nombre']) ?>
                        </h3>
                        
                        <?php if (!empty($categoria['descripcion'])): ?>
                            <p class="text-gray-500 text-sm mb-3 line-clamp-2">
                                <?= e(substr($categoria['descripcion'], 0, 100)) ?>
                                <?= strlen($categoria['descripcion']) > 100 ? '...' : '' ?>
                            </p>
                        <?php endif; ?>
                        
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-box mr-1"></i>
                                <?= $categoria['total_autopartes'] ?? 0 ?> autopartes
                            </span>
                            
                            <a href="<?= BASE_URL ?>/index.php?module=operador&action=inventario&categoria=<?= $categoria['id'] ?>" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Ver productos →
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">No hay categorías registradas</p>
        </div>
    <?php endif; ?>

    <!-- Resumen -->
    <div class="mt-8 bg-white rounded-xl shadow-md p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-chart-pie text-green-600 mr-2"></i>
            Resumen de Categorías
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-600 font-medium">Total de Categorías</p>
                <p class="text-2xl font-bold text-blue-800"><?= count($categorias) ?></p>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-green-600 font-medium">Total de Autopartes</p>
                <p class="text-2xl font-bold text-green-800">
                    <?= array_sum(array_column($categorias, 'total_autopartes')) ?>
                </p>
            </div>
            
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-sm text-purple-600 font-medium">Promedio por Categoría</p>
                <p class="text-2xl font-bold text-purple-800">
                    <?php
                    $total = array_sum(array_column($categorias, 'total_autopartes'));
                    $count = count($categorias);
                    echo $count > 0 ? round($total / $count, 1) : 0;
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
