<?php
/**
 * Vista: Alertas de Stock Bajo - Administrador
 */

$pageTitle = $pageTitle ?? 'Alertas de Stock Bajo';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>/index.php?module=admin&action=dashboard" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium">Stock Bajo</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>Alertas de Stock Bajo
            </h1>
            <p class="text-gray-600">Productos que necesitan reabastecimiento</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-3">
            <select id="filtro-limite" onchange="filtrarLimite()" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <option value="5" <?= ($_GET['limite'] ?? 5) == 5 ? 'selected' : '' ?>>Stock ≤ 5</option>
                <option value="10" <?= ($_GET['limite'] ?? 5) == 10 ? 'selected' : '' ?>>Stock ≤ 10</option>
                <option value="20" <?= ($_GET['limite'] ?? 5) == 20 ? 'selected' : '' ?>>Stock ≤ 20</option>
            </select>
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-crear" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold transition inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Nueva Autoparte
            </a>
        </div>
    </div>

    <!-- Alerta -->
    <?php if (count($autopartes) > 0): ?>
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-yellow-400 text-xl mr-3"></i>
                <p class="text-yellow-700">
                    <strong><?= count($autopartes) ?> productos</strong> tienen stock bajo y necesitan reabastecimiento.
                </p>
            </div>
        </div>
    <?php endif; ?>

    <!-- Grid de productos -->
    <?php if (!empty($autopartes)): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($autopartes as $autoparte): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="flex">
                        <!-- Imagen -->
                        <div class="w-32 h-32 flex-shrink-0">
                            <?php if ($autoparte['thumbnail']): ?>
                                <img src="<?= UPLOADS_URL ?>/<?= htmlspecialchars($autoparte['thumbnail']) ?>" 
                                     alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-car text-gray-400 text-3xl"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Info -->
                        <div class="flex-1 p-4">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="font-semibold text-gray-800 line-clamp-1"><?= htmlspecialchars($autoparte['nombre']) ?></h3>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($autoparte['marca']) ?> <?= htmlspecialchars($autoparte['modelo']) ?></p>
                                </div>
                                <span class="<?= $autoparte['stock'] <= 2 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?> px-2 py-1 rounded-full text-xs font-bold">
                                    <?= $autoparte['stock'] ?> uds
                                </span>
                            </div>
                            
                            <p class="text-xs text-gray-400 mb-2"><?= htmlspecialchars($autoparte['categoria']) ?></p>
                            
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-indigo-600"><?= formatCurrency($autoparte['precio']) ?></span>
                                <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-editar&id=<?= $autoparte['id'] ?>" 
                                   class="text-sm text-indigo-600 hover:text-indigo-800">
                                    <i class="fas fa-edit mr-1"></i>Editar
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Barra de stock -->
                    <div class="px-4 pb-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <?php 
                            $porcentaje = min(100, ($autoparte['stock'] / 20) * 100);
                            $colorBarra = $autoparte['stock'] <= 2 ? 'bg-red-500' : ($autoparte['stock'] <= 5 ? 'bg-yellow-500' : 'bg-green-500');
                            ?>
                            <div class="<?= $colorBarra ?> h-2 rounded-full" style="width: <?= $porcentaje ?>%"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-xl shadow-md p-12 text-center">
            <i class="fas fa-check-circle text-green-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">¡Todo en orden!</h3>
            <p class="text-gray-500">No hay productos con stock bajo en este momento.</p>
        </div>
    <?php endif; ?>
</div>

<script>
function filtrarLimite() {
    const limite = document.getElementById('filtro-limite').value;
    window.location.href = '<?= BASE_URL ?>/index.php?module=admin&action=stock-bajo&limite=' + limite;
}
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>