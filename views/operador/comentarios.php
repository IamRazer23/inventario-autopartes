<?php
/**
 * Vista: Gestión de Comentarios - Operador
 * Lista de comentarios con opciones de actualización y eliminación
 */

$pageTitle = $pageTitle ?? 'Gestión de Comentarios';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-comments text-blue-600 mr-2"></i>
                Gestión de Comentarios
            </h1>
            <p class="text-gray-600">Revisa, actualiza y modera los comentarios del sistema</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?module=operador&action=dashboard" 
           class="mt-4 md:mt-0 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
        </a>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="hidden" name="module" value="operador">
            <input type="hidden" name="action" value="comentarios">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" name="buscar" value="<?= e($filtros['buscar'] ?? '') ?>" 
                       placeholder="Contenido o usuario..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="1" <?= ($filtros['estado'] ?? '') === '1' ? 'selected' : '' ?>>Activos</option>
                    <option value="0" <?= ($filtros['estado'] ?? '') === '0' ? 'selected' : '' ?>>Inactivos</option>
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i> Filtrar
                </button>
                <a href="<?= BASE_URL ?>/index.php?module=operador&action=comentarios" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg transition">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Comentarios -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <?php if (!empty($comentarios)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autoparte</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contenido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($comentarios as $comentario): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    #<?= $comentario['id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?= e($comentario['usuario_nombre'] ?? 'Anónimo') ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?= e($comentario['usuario_email'] ?? '') ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= e($comentario['autoparte_nombre'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        <?= e(substr($comentario['contenido'] ?? '', 0, 100)) ?>
                                        <?= strlen($comentario['contenido'] ?? '') > 100 ? '...' : '' ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= formatDateTime($comentario['fecha_creacion'] ?? '') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($comentario['estado'] == 1): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Activo
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Inactivo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center gap-2">
                                        <a href="<?= BASE_URL ?>/index.php?module=operador&action=ver-comentario&id=<?= $comentario['id'] ?>" 
                                           class="text-blue-600 hover:text-blue-900" title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/index.php?module=operador&action=eliminar-comentario&id=<?= $comentario['id'] ?>" 
                                           class="text-red-600 hover:text-red-900" title="Eliminar"
                                           onclick="return confirm('¿Estás seguro de eliminar este comentario?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($totalPaginas > 1): ?>
                <div class="px-6 py-4 border-t border-gray-200">
                    <nav class="flex justify-center">
                        <ul class="flex space-x-2">
                            <?php if ($pagina > 1): ?>
                                <li>
                                    <a href="<?= BASE_URL ?>/index.php?module=operador&action=comentarios&pagina=<?= $pagina - 1 ?>" 
                                       class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $pagina - 2); $i <= min($totalPaginas, $pagina + 2); $i++): ?>
                                <li>
                                    <a href="<?= BASE_URL ?>/index.php?module=operador&action=comentarios&pagina=<?= $i ?>" 
                                       class="px-3 py-2 <?= $i === $pagina ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?> rounded-lg">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($pagina < $totalPaginas): ?>
                                <li>
                                    <a href="<?= BASE_URL ?>/index.php?module=operador&action=comentarios&pagina=<?= $pagina + 1 ?>" 
                                       class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
            
        <?php else: ?>
            <div class="text-center py-12">
                <i class="fas fa-comments text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500 text-lg">No hay comentarios para mostrar</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
