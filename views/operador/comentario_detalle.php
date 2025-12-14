<?php
/**
 * Vista: Detalle de Comentario - Operador
 * Muestra el detalle de un comentario con opciones de actualización
 */

$pageTitle = $pageTitle ?? 'Detalle de Comentario';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-comment text-blue-600 mr-2"></i>
                Detalle del Comentario #<?= $comentario['id'] ?>
            </h1>
            <p class="text-gray-600">
                Publicado el <?= formatDateTime($comentario['fecha_creacion'] ?? '') ?>
            </p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?module=operador&action=comentarios" 
           class="mt-4 md:mt-0 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Volver
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Información del Comentario -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                    Información del Comentario
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Contenido</label>
                        <div class="bg-gray-50 p-4 rounded-lg text-gray-800">
                            <?= nl2br(e($comentario['contenido'] ?? 'Sin contenido')) ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Calificación</label>
                            <div class="flex items-center">
                                <?php 
                                $rating = $comentario['calificacion'] ?? 0;
                                for ($i = 1; $i <= 5; $i++): 
                                ?>
                                    <i class="fas fa-star <?= $i <= $rating ? 'text-yellow-400' : 'text-gray-300' ?>"></i>
                                <?php endfor; ?>
                                <span class="ml-2 text-gray-600">(<?= $rating ?>/5)</span>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Estado Actual</label>
                            <?php if ($comentario['estado'] == 1): ?>
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i> Activo
                                </span>
                            <?php else: ?>
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i> Inactivo
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!empty($comentario['respuesta_admin'])): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-500 mb-1">Respuesta del Administrador</label>
                            <div class="bg-blue-50 p-4 rounded-lg text-blue-800 border-l-4 border-blue-500">
                                <?= nl2br(e($comentario['respuesta_admin'])) ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Formulario de Actualización -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-edit text-green-600 mr-2"></i>
                    Actualizar Comentario
                </h3>
                
                <form action="<?= BASE_URL ?>/index.php?module=operador&action=actualizar-comentario" method="POST">
                    <input type="hidden" name="id" value="<?= $comentario['id'] ?>">
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Respuesta / Nota del Operador
                            </label>
                            <textarea name="respuesta" rows="4" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                      placeholder="Escribe una respuesta o nota sobre este comentario..."><?= e($comentario['respuesta_admin'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="estado" id="estado" value="1" 
                                   <?= ($comentario['estado'] ?? 0) == 1 ? 'checked' : '' ?>
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="estado" class="ml-2 text-sm text-gray-700">
                                Comentario activo (visible públicamente)
                            </label>
                        </div>
                        
                        <div class="flex gap-4 pt-4">
                            <button type="submit" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                                <i class="fas fa-save mr-2"></i> Guardar Cambios
                            </button>
                            <a href="<?= BASE_URL ?>/index.php?module=operador&action=eliminar-comentario&id=<?= $comentario['id'] ?>" 
                               class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition"
                               onclick="return confirm('¿Estás seguro de eliminar este comentario?');">
                                <i class="fas fa-trash mr-2"></i> Eliminar
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Panel Lateral -->
        <div class="lg:col-span-1">
            <!-- Información del Usuario -->
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-user text-purple-600 mr-2"></i>
                    Usuario
                </h3>
                
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-800">
                            <?= e($comentario['usuario_nombre'] ?? 'Anónimo') ?>
                        </p>
                        <p class="text-sm text-gray-500">
                            <?= e($comentario['usuario_email'] ?? 'Sin email') ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Información de la Autoparte -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-car text-orange-600 mr-2"></i>
                    Autoparte Relacionada
                </h3>
                
                <?php if (!empty($comentario['autoparte_nombre'])): ?>
                    <div class="flex items-start">
                        <?php if (!empty($comentario['autoparte_imagen'])): ?>
                            <img src="<?= e($comentario['autoparte_imagen']) ?>" 
                                 alt="<?= e($comentario['autoparte_nombre']) ?>"
                                 class="w-16 h-16 object-cover rounded-lg">
                        <?php else: ?>
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        <?php endif; ?>
                        <div class="ml-4">
                            <p class="font-semibold text-gray-800">
                                <?= e($comentario['autoparte_nombre']) ?>
                            </p>
                            <a href="<?= BASE_URL ?>/index.php?module=operador&action=ver-autoparte&id=<?= $comentario['autoparte_id'] ?>" 
                               class="text-sm text-blue-600 hover:text-blue-800">
                                Ver autoparte →
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500">Autoparte no disponible</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
