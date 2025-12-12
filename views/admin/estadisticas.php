<?php
/**
 * Vista: Estadísticas y Reportes - Administrador
 */

$pageTitle = $pageTitle ?? 'Estadísticas y Reportes';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>/index.php?module=admin&action=dashboard" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium">Estadísticas</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-chart-bar text-indigo-600 mr-3"></i>Estadísticas y Reportes
        </h1>
        <p class="text-gray-600">Análisis detallado del rendimiento del sistema</p>
    </div>

    <!-- Cards de estadísticas principales -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-indigo-100 text-sm">Total Ventas</p>
                    <h3 class="text-3xl font-bold"><?= number_format($stats['total_ventas']) ?></h3>
                </div>
                <i class="fas fa-receipt text-3xl opacity-50"></i>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Ingresos Totales</p>
                    <h3 class="text-3xl font-bold"><?= formatCurrency($stats['ingresos_totales']) ?></h3>
                </div>
                <i class="fas fa-dollar-sign text-3xl opacity-50"></i>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Ticket Promedio</p>
                    <h3 class="text-3xl font-bold"><?= formatCurrency($stats['ticket_promedio']) ?></h3>
                </div>
                <i class="fas fa-tags text-3xl opacity-50"></i>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Clientes Únicos</p>
                    <h3 class="text-3xl font-bold"><?= number_format($stats['total_clientes']) ?></h3>
                </div>
                <i class="fas fa-users text-3xl opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Productos -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-trophy mr-2"></i>Top 10 Productos Más Vendidos
                </h3>
            </div>
            <div class="p-6">
                <?php if (!empty($topProductos)): ?>
                    <div class="space-y-4">
                        <?php foreach ($topProductos as $index => $producto): ?>
                            <div class="flex items-center gap-4">
                                <span class="w-8 h-8 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center font-bold text-sm">
                                    <?= $index + 1 ?>
                                </span>
                                <?php if ($producto['thumbnail']): ?>
                                    <img src="<?= UPLOADS_URL ?>/<?= htmlspecialchars($producto['thumbnail']) ?>" 
                                         class="w-12 h-12 object-cover rounded-lg">
                                <?php else: ?>
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-car text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 truncate"><?= htmlspecialchars($producto['nombre']) ?></p>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($producto['marca']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-gray-800"><?= $producto['total_vendido'] ?> uds</p>
                                    <p class="text-sm text-green-600"><?= formatCurrency($producto['ingresos']) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-500 py-8">No hay datos disponibles</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Clientes -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-teal-500 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">
                    <i class="fas fa-users mr-2"></i>Top 10 Mejores Clientes
                </h3>
            </div>
            <div class="p-6">
                <?php if (!empty($topClientes)): ?>
                    <div class="space-y-4">
                        <?php foreach ($topClientes as $index => $cliente): ?>
                            <div class="flex items-center gap-4">
                                <span class="w-8 h-8 bg-green-100 text-green-600 rounded-full flex items-center justify-center font-bold text-sm">
                                    <?= $index + 1 ?>
                                </span>
                                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold"><?= strtoupper(substr($cliente['nombre'], 0, 1)) ?></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 truncate"><?= htmlspecialchars($cliente['nombre']) ?></p>
                                    <p class="text-sm text-gray-500"><?= htmlspecialchars($cliente['email']) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600"><?= formatCurrency($cliente['total_gastado']) ?></p>
                                    <p class="text-sm text-gray-500"><?= $cliente['total_compras'] ?> compras</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-center text-gray-500 py-8">No hay datos disponibles</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Ventas por categoría -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-orange-500 to-red-500 px-6 py-4">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-tags mr-2"></i>Ventas por Categoría
            </h3>
        </div>
        <div class="p-6">
            <?php if (!empty($categorias)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php 
                    $totalGeneral = array_sum(array_column($categorias, 'total_vendido'));
                    foreach ($categorias as $categoria): 
                        $porcentaje = $totalGeneral > 0 ? ($categoria['total_vendido'] / $totalGeneral) * 100 : 0;
                    ?>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-2">
                                <h4 class="font-semibold text-gray-800"><?= htmlspecialchars($categoria['nombre']) ?></h4>
                                <span class="text-sm text-gray-500"><?= number_format($porcentaje, 1) ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                                <div class="bg-orange-500 h-2 rounded-full" style="width: <?= $porcentaje ?>%"></div>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600"><?= $categoria['total_vendido'] ?> unidades</span>
                                <span class="text-green-600 font-medium"><?= formatCurrency($categoria['ingresos']) ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 py-8">No hay datos disponibles</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Ventas por mes -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
            <h3 class="text-lg font-semibold text-white">
                <i class="fas fa-chart-line mr-2"></i>Ventas Últimos 12 Meses
            </h3>
        </div>
        <div class="p-6">
            <?php if (!empty($ventasPorMes)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Mes</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Ventas</th>
                                <th class="text-right py-3 px-4 font-semibold text-gray-700">Ingresos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ventasPorMes as $mes): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-3 px-4"><?= htmlspecialchars($mes['mes_label']) ?></td>
                                    <td class="py-3 px-4 text-center">
                                        <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full text-sm">
                                            <?= $mes['total_ventas'] ?>
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-semibold text-green-600">
                                        <?= formatCurrency($mes['ingresos']) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-center text-gray-500 py-8">No hay datos disponibles</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>