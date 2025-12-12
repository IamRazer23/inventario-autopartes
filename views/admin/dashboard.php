<?php
/**
 * Vista: Dashboard del Administrador
 * Panel principal con estadísticas, gráficos y accesos rápidos
 */

$pageTitle = $pageTitle ?? 'Dashboard Administrativo';

// Imagen por defecto para productos sin imagen
$defaultImage = 'https://via.placeholder.com/100x100?text=Sin+Img';

// Función helper para nombres de días en español
function getNombreDia($fecha) {
    $dias = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    return $dias[date('w', strtotime($fecha))];
}

// Inicializar variables con valores por defecto si no existen
$totalUsuarios = $totalUsuarios ?? 0;
$totalAutopartes = $totalAutopartes ?? 0;
$totalCategorias = $totalCategorias ?? 0;
$ventasHoy = $ventasHoy ?? ['total_ventas' => 0, 'total_ingresos' => 0];
$ventasMes = $ventasMes ?? ['total_ventas' => 0, 'total_ingresos' => 0];
$ventasTotal = $ventasTotal ?? ['total_ventas' => 0, 'total_ingresos' => 0];
$stockBajo = $stockBajo ?? [];
$ultimasVentas = $ultimasVentas ?? [];
$usuariosRecientes = $usuariosRecientes ?? [];
$autopartesTop = $autopartesTop ?? [];
$categoriasTop = $categoriasTop ?? [];
$ventasSemana = $ventasSemana ?? [];
$comparacionMes = $comparacionMes ?? ['mes_actual' => 0, 'mes_anterior' => 0];
$porcentajeCambio = $porcentajeCambio ?? 0;

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header del Dashboard -->
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-tachometer-alt text-indigo-600 mr-3"></i>
                Dashboard Administrativo
            </h1>
            <p class="text-gray-600">
                Bienvenido, <strong><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Admin') ?></strong> • 
                <?= date('l, d \d\e F \d\e Y') ?>
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=exportar-inventario" 
               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center">
                <i class="fas fa-file-excel mr-2"></i>Exportar CSV
            </a>
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=autoparte-crear" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition flex items-center">
                <i class="fas fa-plus mr-2"></i>Nueva Autoparte
            </a>
        </div>
    </div>

    <!-- ===== TARJETAS DE ESTADÍSTICAS PRINCIPALES ===== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        
        <!-- Total Usuarios -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">Total Usuarios</p>
                    <h3 class="text-4xl font-bold"><?= number_format($totalUsuarios) ?></h3>
                    <p class="text-blue-200 text-xs mt-2">
                        <i class="fas fa-user-check mr-1"></i>Usuarios activos
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-users text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Autopartes (Inventario) -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Inventario</p>
                    <h3 class="text-4xl font-bold"><?= number_format($totalAutopartes) ?></h3>
                    <p class="text-green-200 text-xs mt-2">
                        <i class="fas fa-box mr-1"></i>Autopartes activas
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-cogs text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Ventas Hoy -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">Ventas Hoy</p>
                    <h3 class="text-4xl font-bold"><?= $ventasHoy['total_ventas'] ?? 0 ?></h3>
                    <p class="text-purple-200 text-xs mt-2">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        <?= formatCurrency($ventasHoy['total_ingresos'] ?? 0) ?>
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-shopping-bag text-3xl"></i>
                </div>
            </div>
        </div>

        <!-- Ventas del Mes -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">Ventas del Mes</p>
                    <h3 class="text-4xl font-bold"><?= $ventasMes['total_ventas'] ?? 0 ?></h3>
                    <p class="text-orange-200 text-xs mt-2">
                        <i class="fas fa-dollar-sign mr-1"></i>
                        <?= formatCurrency($ventasMes['total_ingresos'] ?? 0) ?>
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-4">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== GRÁFICOS: Comparación Mensual + Ventas 7 Días ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Comparación Mensual -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-balance-scale text-indigo-500 mr-2"></i>
                Comparación Mensual
            </h3>
            
            <?php 
            $mesActual = $comparacionMes['mes_actual'] ?? 0;
            $mesAnterior = $comparacionMes['mes_anterior'] ?? 0;
            $maxValor = max($mesActual, $mesAnterior, 1);
            $porcentajeActual = ($mesActual / $maxValor) * 100;
            $porcentajeAnterior = ($mesAnterior / $maxValor) * 100;
            ?>
            
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Mes Actual</span>
                        <span class="text-sm font-bold text-indigo-600"><?= formatCurrency($mesActual) ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-indigo-600 h-4 rounded-full transition-all duration-500" style="width: <?= $porcentajeActual ?>%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Mes Anterior</span>
                        <span class="text-sm font-bold text-gray-500"><?= formatCurrency($mesAnterior) ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-gray-400 h-4 rounded-full transition-all duration-500" style="width: <?= $porcentajeAnterior ?>%"></div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <?php if ($porcentajeCambio > 0): ?>
                    <span class="text-2xl font-bold text-green-600">
                        <i class="fas fa-arrow-up mr-1"></i>+<?= number_format($porcentajeCambio, 1) ?>%
                    </span>
                <?php elseif ($porcentajeCambio < 0): ?>
                    <span class="text-2xl font-bold text-red-600">
                        <i class="fas fa-arrow-down mr-1"></i><?= number_format($porcentajeCambio, 1) ?>%
                    </span>
                <?php else: ?>
                    <span class="text-2xl font-bold text-gray-500">
                        <i class="fas fa-minus mr-1"></i>0.0%
                    </span>
                <?php endif; ?>
                <p class="text-sm text-gray-500 mt-1">vs mes anterior</p>
            </div>
        </div>

        <!-- Gráfico de Ventas Últimos 7 Días -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-chart-bar text-green-500 mr-2"></i>
                Ventas de los Últimos 7 Días
            </h3>
            
            <?php
            // Preparar datos para los últimos 7 días
            $dias = [];
            for ($i = 6; $i >= 0; $i--) {
                $fecha = date('Y-m-d', strtotime("-$i days"));
                $dias[$fecha] = [
                    'nombre' => getNombreDia($fecha),
                    'fecha' => date('d/m', strtotime($fecha)),
                    'ingresos' => 0
                ];
            }
            
            // Rellenar con datos reales
            if (!empty($ventasSemana)) {
                foreach ($ventasSemana as $venta) {
                    if (isset($dias[$venta['fecha']])) {
                        $dias[$venta['fecha']]['ingresos'] = floatval($venta['ingresos']);
                    }
                }
            }
            
            $maxIngreso = max(array_column($dias, 'ingresos'));
            if ($maxIngreso == 0) $maxIngreso = 100;
            ?>
            
            <div class="flex items-end justify-between h-48 gap-2">
                <?php foreach ($dias as $fecha => $info): 
                    $altura = ($info['ingresos'] / $maxIngreso) * 100;
                    if ($info['ingresos'] > 0 && $altura < 5) $altura = 5;
                ?>
                <div class="flex-1 flex flex-col items-center">
                    <div class="w-full bg-gray-100 rounded-t-lg relative" style="height: 140px;">
                        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-green-500 to-green-400 rounded-t-lg transition-all duration-500 hover:from-green-600 hover:to-green-500 cursor-pointer"
                             style="height: <?= $altura ?>%;"
                             title="<?= formatCurrency($info['ingresos']) ?>">
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <p class="text-xs font-semibold text-gray-700"><?= $info['nombre'] ?></p>
                        <p class="text-xs text-gray-500"><?= $info['fecha'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (empty($ventasSemana) || array_sum(array_column($dias, 'ingresos')) == 0): ?>
            <p class="text-center text-gray-500 text-sm mt-4">
                <i class="fas fa-info-circle mr-1"></i>
                No hay ventas registradas en los últimos 7 días
            </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ===== SECCIÓN MEDIA: Top Vendidas + Categorías + Stock ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Top 5 Más Vendidas -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-yellow-400 to-yellow-500 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-trophy mr-2"></i>
                    Top 5 Más Vendidas
                </h3>
            </div>
            <div class="p-6">
                <?php if (!empty($autopartesTop)): ?>
                    <div class="space-y-4">
                        <?php foreach ($autopartesTop as $index => $autoparte): ?>
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center font-bold text-sm flex-shrink-0">
                                    <?= $index + 1 ?>
                                </span>
                                
                                <?php if (!empty($autoparte['thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars($autoparte['thumbnail']) ?>" 
                                         alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                                         class="w-12 h-12 object-cover rounded-lg flex-shrink-0"
                                         onerror="this.src='<?= $defaultImage ?>'">
                                <?php else: ?>
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-car text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 truncate text-sm"><?= htmlspecialchars($autoparte['nombre']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($autoparte['marca'] ?? '') ?> <?= htmlspecialchars($autoparte['modelo'] ?? '') ?></p>
                                </div>
                                <span class="text-sm font-bold text-green-600 flex-shrink-0"><?= $autoparte['total_vendido'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chart-line text-gray-300 text-4xl mb-3"></i>
                        <p class="font-semibold">Sin datos aún</p>
                        <p class="text-sm">Las autopartes más vendidas aparecerán aquí</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Categorías Populares -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-tags mr-2"></i>
                    Categorías Populares
                </h3>
            </div>
            <div class="p-6">
                <?php if (!empty($categoriasTop)): ?>
                    <div class="space-y-4">
                        <?php 
                        $maxPiezas = max(array_column($categoriasTop, 'total_piezas')) ?: 1;
                        foreach ($categoriasTop as $categoria): 
                            $porcentaje = ($categoria['total_piezas'] / $maxPiezas) * 100;
                        ?>
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-sm font-medium text-gray-700"><?= htmlspecialchars($categoria['nombre']) ?></span>
                                    <span class="text-sm text-indigo-600 font-bold"><?= number_format($categoria['total_piezas']) ?> uds</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-300" style="width: <?= $porcentaje ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-folder-open text-gray-300 text-4xl mb-3"></i>
                        <p class="font-semibold">Sin datos aún</p>
                        <p class="text-sm">Las categorías más vendidas aparecerán aquí</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alertas de Stock Bajo -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-red-500 to-orange-500 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Stock Bajo
                </h3>
                <?php if (!empty($stockBajo)): ?>
                    <span class="bg-white text-red-600 px-3 py-1 rounded-full text-sm font-bold">
                        <?= count($stockBajo) ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="p-6">
                <?php if (!empty($stockBajo)): ?>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <?php foreach (array_slice($stockBajo, 0, 5) as $item): ?>
                            <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg hover:bg-red-50 transition">
                                <?php if (!empty($item['thumbnail'])): ?>
                                    <img src="<?= htmlspecialchars($item['thumbnail']) ?>" 
                                         class="w-10 h-10 object-cover rounded-lg"
                                         onerror="this.src='<?= $defaultImage ?>'">
                                <?php else: ?>
                                    <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-car text-gray-400 text-sm"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-800 truncate text-sm"><?= htmlspecialchars($item['nombre']) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($item['marca'] ?? '') ?></p>
                                </div>
                                <span class="<?= $item['stock'] <= 2 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' ?> px-2 py-1 rounded-full text-xs font-bold flex-shrink-0">
                                    <?= $item['stock'] ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4 pt-4 border-t">
                        <a href="<?= BASE_URL ?>/index.php?module=admin&action=stock-bajo" 
                           class="text-orange-600 hover:text-orange-800 font-semibold text-sm flex items-center justify-center">
                            Ver todo el inventario bajo
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-check-circle text-green-400 text-4xl mb-3"></i>
                        <p class="font-semibold text-green-600">¡Todo en orden!</p>
                        <p class="text-sm">No hay productos con stock bajo</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== ÚLTIMAS VENTAS + USUARIOS RECIENTES ===== -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- Últimas Ventas -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Últimas Ventas
                </h3>
                <a href="<?= BASE_URL ?>/index.php?module=admin&action=ventas" 
                   class="text-white text-sm hover:underline">
                    Ver todas <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                <?php if (!empty($ultimasVentas)): ?>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        <?php foreach (array_slice($ultimasVentas, 0, 6) as $venta): ?>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-purple-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-purple-600 font-bold text-sm">
                                            <?= strtoupper(substr($venta['cliente'] ?? 'C', 0, 1)) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($venta['cliente'] ?? 'Cliente') ?></p>
                                        <p class="text-xs text-gray-500">
                                            <i class="fas fa-box mr-1"></i><?= $venta['total_items'] ?? 0 ?> items •
                                            <i class="fas fa-clock ml-1 mr-1"></i><?= date('d/m H:i', strtotime($venta['fecha_venta'])) ?>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-green-600"><?= formatCurrency($venta['total']) ?></p>
                                    <a href="<?= BASE_URL ?>/index.php?module=admin&action=venta-detalle&id=<?= $venta['id'] ?>" 
                                       class="text-xs text-purple-600 hover:text-purple-800">Ver →</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-receipt text-gray-300 text-4xl mb-3"></i>
                        <p class="font-semibold">No hay ventas aún</p>
                        <p class="text-sm">Las ventas aparecerán aquí</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Usuarios Recientes -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-user-plus mr-2"></i>
                    Usuarios Recientes
                </h3>
                <a href="<?= BASE_URL ?>/index.php?module=admin&action=usuarios" 
                   class="text-white text-sm hover:underline">
                    Ver todos <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="p-6">
                <?php if (!empty($usuariosRecientes)): ?>
                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        <?php foreach ($usuariosRecientes as $usuario): ?>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                    <span class="text-blue-600 font-bold">
                                        <?= strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1)) ?>
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate"><?= htmlspecialchars($usuario['nombre']) ?></p>
                                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($usuario['email']) ?></p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <?php 
                                    $rolClass = 'bg-green-100 text-green-700';
                                    if ($usuario['rol'] == 'Administrador') $rolClass = 'bg-purple-100 text-purple-700';
                                    elseif ($usuario['rol'] == 'Operador') $rolClass = 'bg-blue-100 text-blue-700';
                                    ?>
                                    <span class="<?= $rolClass ?> text-xs px-2 py-1 rounded-full">
                                        <?= htmlspecialchars($usuario['rol']) ?>
                                    </span>
                                    <p class="text-xs text-gray-400 mt-1"><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-users text-gray-300 text-4xl mb-3"></i>
                        <p class="font-semibold">No hay usuarios registrados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ===== ACCESOS RÁPIDOS ===== -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl shadow-lg p-6">
        <h3 class="text-xl font-semibold text-white mb-6 flex items-center">
            <i class="fas fa-bolt mr-3"></i>
            Accesos Rápidos
        </h3>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=usuarios" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-users text-3xl mb-2"></i>
                <p class="font-semibold text-sm">Usuarios</p>
            </a>
            
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=autopartes" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-cogs text-3xl mb-2"></i>
                <p class="font-semibold text-sm">Autopartes</p>
            </a>
            
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=categorias" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-tags text-3xl mb-2"></i>
                <p class="font-semibold text-sm">Categorías</p>
            </a>
            
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=ventas" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                <p class="font-semibold text-sm">Ventas</p>
            </a>
            
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=estadisticas" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-chart-bar text-3xl mb-2"></i>
                <p class="font-semibold text-sm">Estadísticas</p>
            </a>
            
            <a href="<?= BASE_URL ?>/index.php?module=admin&action=exportar-inventario" 
               class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white rounded-lg p-4 text-center transition-all transform hover:scale-105">
                <i class="fas fa-file-excel text-3xl mb-2"></i>
                <p class="font-semibold text-sm">Exportar</p>
            </a>
        </div>
    </div>

    <!-- ===== RESUMEN RÁPIDO (Footer Stats) ===== -->
    <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-gray-500 text-xs uppercase tracking-wider">Total Ventas</p>
            <p class="text-2xl font-bold text-gray-800"><?= formatCurrency($ventasTotal['total_ingresos'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-gray-500 text-xs uppercase tracking-wider">Órdenes Totales</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($ventasTotal['total_ventas'] ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-gray-500 text-xs uppercase tracking-wider">Categorías</p>
            <p class="text-2xl font-bold text-gray-800"><?= number_format($totalCategorias ?? 0) ?></p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-gray-500 text-xs uppercase tracking-wider">Ticket Promedio</p>
            <p class="text-2xl font-bold text-gray-800">
                <?php 
                $totalVentas = $ventasTotal['total_ventas'] ?? 0;
                $totalIngresos = $ventasTotal['total_ingresos'] ?? 0;
                $ticketPromedio = $totalVentas > 0 ? ($totalIngresos / $totalVentas) : 0;
                echo formatCurrency($ticketPromedio);
                ?>
            </p>
        </div>
    </div>

</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>