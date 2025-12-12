<?php
/**
 * Vista: Lista de Ventas - Administrador
 */

$pageTitle = $pageTitle ?? 'Gestión de Ventas';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>/index.php?module=admin&action=dashboard" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium">Ventas</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-cash-register text-indigo-600 mr-3"></i>Gestión de Ventas
            </h1>
            <p class="text-gray-600">Administra y revisa todas las ventas del sistema</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=exportar-ventas" 
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold transition inline-flex items-center">
                <i class="fas fa-file-excel mr-2"></i>Exportar CSV
            </a>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">Total Ventas</p>
                    <h3 class="text-3xl font-bold"><?= number_format($statsVentas['total']) ?></h3>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-receipt text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Ingresos Totales</p>
                    <h3 class="text-3xl font-bold"><?= formatCurrency($statsVentas['ingresos']) ?></h3>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-2xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Ticket Promedio</p>
                    <h3 class="text-3xl font-bold"><?= formatCurrency($statsVentas['promedio']) ?></h3>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="module" value="admin">
            <input type="hidden" name="action" value="ventas">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Desde</label>
                <input type="date" name="fecha_desde" value="<?= htmlspecialchars($filtros['fecha_desde']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Hasta</label>
                <input type="date" name="fecha_hasta" value="<?= htmlspecialchars($filtros['fecha_hasta']) ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cliente</label>
                <input type="text" name="cliente" value="<?= htmlspecialchars($filtros['cliente']) ?>" placeholder="Nombre o email..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
                <a href="<?= BASE_URL ?>/index.php?module=admin&action=ventas" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de ventas -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <?php if (!empty($ventas)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"># Orden</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($ventas as $venta): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-mono font-semibold text-indigo-600">
                                        #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <p class="font-medium text-gray-900"><?= htmlspecialchars($venta['cliente']) ?></p>
                                        <p class="text-sm text-gray-500"><?= htmlspecialchars($venta['cliente_email']) ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-sm">
                                        <?= $venta['total_items'] ?> items
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-green-600"><?= formatCurrency($venta['total']) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= formatDateTime($venta['fecha_venta']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $estadoClass = match($venta['estado'] ?? 'completada') {
                                        'completada' => 'bg-green-100 text-green-700',
                                        'pendiente' => 'bg-yellow-100 text-yellow-700',
                                        'cancelada' => 'bg-red-100 text-red-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                    ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium <?= $estadoClass ?>">
                                        <?= ucfirst($venta['estado'] ?? 'completada') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="<?= BASE_URL ?>/index.php?module=admin&action=venta-detalle&id=<?= $venta['id'] ?>" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye mr-1"></i>Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <div class="bg-gray-50 px-6 py-4 border-t flex items-center justify-between">
                    <p class="text-sm text-gray-600">
                        Mostrando <?= count($ventas) ?> de <?= $totalVentas ?> ventas
                    </p>
                    <div class="flex gap-2">
                        <?php if ($pagina > 1): ?>
                            <a href="<?= BASE_URL ?>/index.php?module=admin&action=ventas&pagina=<?= $pagina - 1 ?>" 
                               class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">Anterior</a>
                        <?php endif; ?>
                        
                        <?php if ($pagina < $totalPaginas): ?>
                            <a href="<?= BASE_URL ?>/index.php?module=admin&action=ventas&pagina=<?= $pagina + 1 ?>" 
                               class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm">Siguiente</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-receipt text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">No hay ventas</h3>
                <p class="text-gray-500">No se encontraron ventas con los filtros seleccionados</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>