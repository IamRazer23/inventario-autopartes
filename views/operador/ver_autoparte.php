<?php
/**
 * Vista: Ver Detalle de Autoparte - Operador (Tailwind)
 */

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($autoparte['nombre']) ?></h1>
            <p class="text-sm text-gray-500">ID #<?= $autoparte['id'] ?> · <?= htmlspecialchars($autoparte['marca'] ?? '') ?> <?= htmlspecialchars($autoparte['modelo'] ?? '') ?> · <?= $autoparte['anio'] ?></p>
        </div>
        <div class="flex items-center gap-3">
            <?php if (hasPermission('inventario', 'actualizar')): ?>
            <a href="<?= BASE_URL ?>/index.php?module=operador&action=editar-autoparte&id=<?= $autoparte['id'] ?>" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Editar</a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/index.php?module=operador&action=inventario" class="px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">Volver</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow p-4">
                <?php
                // Construir URL de imagen: si la ruta es absoluta usarla, si no, prepend UPLOADS_URL
                $imgPathLarge = $autoparte['imagen_grande'] ?? '';
                $imgPathThumb = $autoparte['thumbnail'] ?? '';

                function buildImageUrl($path) {
                    if (empty($path)) return '';
                    if (preg_match('#^https?://#i', $path)) return $path;
                    return UPLOADS_URL . '/' . ltrim($path, '/');
                }

                $srcLarge = buildImageUrl($imgPathLarge);
                $srcThumb = buildImageUrl($imgPathThumb);

                if (!empty($srcLarge)): ?>
                    <img src="<?= htmlspecialchars($srcLarge) ?>" alt="<?= htmlspecialchars($autoparte['nombre']) ?>" class="w-full h-64 object-contain rounded">
                <?php elseif (!empty($srcThumb)): ?>
                    <img src="<?= htmlspecialchars($srcThumb) ?>" alt="<?= htmlspecialchars($autoparte['nombre']) ?>" class="w-full h-64 object-contain rounded">
                <?php else: ?>
                    <div class="w-full h-64 bg-gray-100 rounded flex items-center justify-center text-gray-400">
                        <i class="fas fa-image fa-3x"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl shadow p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= $autoparte['estado'] == 1 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' ?>"><?= $autoparte['estado'] == 1 ? 'Activo' : 'Inactivo' ?></span>
                        <h2 class="text-xl font-semibold text-gray-800 mt-2">$<?= number_format($autoparte['precio'], 2) ?></h2>
                        <p class="text-sm text-gray-500">Precio unitario</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-bold <?= $autoparte['stock'] <= 0 ? 'text-red-600' : ($autoparte['stock'] <=5 ? 'text-yellow-600' : 'text-green-600') ?>"><?= $autoparte['stock'] ?> <?= $autoparte['stock'] == 1 ? 'unidad' : 'unidades' ?></p>
                        <p class="text-sm text-gray-500"><?= $autoparte['stock'] <= 0 ? 'Agotado' : ($autoparte['stock'] <=5 ? 'Stock bajo' : 'En stock') ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-gray-700 whitespace-pre-line"><?= nl2br(htmlspecialchars($autoparte['descripcion'] ?? '')) ?></p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-4">
                <h3 class="text-sm text-gray-500 mb-3">Información</h3>
                <div class="grid grid-cols-2 gap-4 text-sm text-gray-700">
                    <div>
                        <div class="text-gray-500">Categoría</div>
                        <div class="font-medium"><?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Registrado</div>
                        <div class="font-medium"><?= date('d/m/Y H:i', strtotime($autoparte['fecha_creacion'])) ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Última actualización</div>
                        <div class="font-medium"><?= !empty($autoparte['fecha_actualizacion']) ? date('d/m/Y H:i', strtotime($autoparte['fecha_actualizacion'])) : '-' ?></div>
                    </div>
                    <div>
                        <div class="text-gray-500">Valor en inventario</div>
                        <div class="font-medium">$<?= number_format($autoparte['precio'] * $autoparte['stock'], 2) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
