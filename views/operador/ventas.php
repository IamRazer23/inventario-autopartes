<?php
/**
 * Vista: Historial de Ventas - Operador (Solo Lectura)
 * Lista de ventas sin opciones de modificación
 */

$pageTitle = $pageTitle ?? 'Historial de Ventas';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-receipt text-blue-600 mr-2"></i>
                Historial de Ventas
            </h1>
            <p class="text-gray-600">
                <i class="fas fa-lock text-yellow-500 mr-1"></i>
                Vista de solo lectura - Consulta las ventas realizadas
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
                    Como operador, solo puedes consultar el historial de ventas. 
                    No tienes permisos para crear, modificar o eliminar ventas.
                </p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="module" value="operador">
            <input type="hidden" name="action" value="ventas">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="buscar" value="<?= e($filtros['buscar'] ?? '') ?>" 
                       placeholder="ID o nombre del cliente..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="<?= e($filtros['fecha_inicio'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="<?= e($filtros['fecha_fin'] ?? '') ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
                <a href="<?= BASE_URL ?>/index.php?module=operador&action=ventas" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Ventas -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <?php if (!empty($ventas)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                # Venta
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cliente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($ventas as $venta): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">
                                        #<?= str_pad($venta['id'], 6, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= e($venta['cliente_nombre'] ?? 'N/A') ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= e($venta['cliente_email'] ?? '') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= formatDateTime($venta['fecha_venta'] ?? '') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?= $venta['total_items'] ?? 0 ?> productos
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <span class="text-sm font-semibold text-green-600">
                                        <?= formatCurrency($venta['total'] ?? 0) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php
                                    $estado = $venta['estado'] ?? 'completada';
                                    $estadoClases = [
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'procesando' => 'bg-blue-100 text-blue-800',
                                        'completada' => 'bg-green-100 text-green-800',
                                        'cancelada' => 'bg-red-100 text-red-800'
                                    ];
                                    $clase = $estadoClases[$estado] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $clase ?>">
                                        <?= ucfirst($estado) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="<?= BASE_URL ?>/index.php?module=operador&action=ver-venta&id=<?= $venta['id'] ?>" 
                                       class="text-blue-600 hover:text-blue-900" title="Ver detalle">
                                        <i class="fas fa-eye mr-1"></i> Ver
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <div class="px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <p class="text-sm text-gray-600">
                            Mostrando <?= count($ventas) ?> de <?= $totalVentas ?> ventas
                        </p>
                        <nav class="flex space-x-2">
                            <?php if ($pagina > 1): ?>
                                <a href="<?= BASE_URL ?>/index.php?module=operador&action=ventas&pagina=<?= $pagina - 1 ?>" 
                                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++): ?>
                                <a href="<?= BASE_URL ?>/index.php?module=operador&action=ventas&pagina=<?= $i ?>" 
                                   class="px-3 py-2 <?= $i === $pagina ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> rounded-lg">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($pagina < $totalPaginas): ?>
                                <a href="<?= BASE_URL ?>/index.php?module=operador&action=ventas&pagina=<?= $pagina + 1 ?>" 
                                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-receipt text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">No hay ventas para mostrar</p>
                <p class="text-gray-400 text-sm mt-2">Intenta ajustar los filtros de búsqueda</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
