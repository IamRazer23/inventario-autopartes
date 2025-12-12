<?php
/**
 * Vista: Página de Inicio (Home)
 * MODIFICADO: Las imágenes se cargan desde URLs externas
 */

$pageTitle = 'Inicio - AutoPartes Pro';
$defaultImage = 'https://via.placeholder.com/300x300?text=Sin+Imagen';

require_once VIEWS_PATH . '/layouts/header.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-to-r from-indigo-600 to-purple-700 text-white py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">
                Encuentra las Autopartes que Necesitas
            </h1>
            <p class="text-xl text-indigo-100 mb-8">
                El mejor inventario de repuestos para tu vehículo. Calidad garantizada y envíos a todo Panamá.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
                   class="bg-white text-indigo-600 hover:bg-gray-100 font-bold py-3 px-8 rounded-lg transition">
                    <i class="fas fa-search mr-2"></i>Ver Catálogo
                </a>
                <?php if (!isAuthenticated()): ?>
                <a href="<?= BASE_URL ?>/index.php?module=auth&action=register" 
                   class="bg-transparent border-2 border-white hover:bg-white hover:text-indigo-600 font-bold py-3 px-8 rounded-lg transition">
                    <i class="fas fa-user-plus mr-2"></i>Registrarse
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Categorías -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-8">
            <i class="fas fa-th-large text-indigo-600 mr-2"></i>
            Categorías
        </h2>
        
        <?php if (!empty($categorias)): ?>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <?php foreach ($categorias as $cat): ?>
            <a href="<?= BASE_URL ?>/index.php?module=publico&action=categoria&id=<?= $cat['id'] ?>" 
               class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all p-6 text-center group">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-indigo-600 transition">
                    <i class="fas fa-cog text-2xl text-indigo-600 group-hover:text-white transition"></i>
                </div>
                <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition">
                    <?= htmlspecialchars($cat['nombre']) ?>
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    <?= $cat['total_autopartes'] ?> productos
                </p>
            </a>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center text-gray-500">No hay categorías disponibles</p>
        <?php endif; ?>
    </div>
</section>

<!-- Productos Destacados -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-star text-yellow-500 mr-2"></i>
                Productos Destacados
            </h2>
            <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
               class="text-indigo-600 hover:text-indigo-800 font-semibold">
                Ver todos <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (!empty($destacadas)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($destacadas as $producto): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-all group">
                <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $producto['id'] ?>">
                    <div class="relative overflow-hidden h-48">
                        <!-- IMAGEN DESDE URL EXTERNA -->
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
                        
                        <span class="absolute top-3 left-3 bg-indigo-600 text-white text-xs font-bold px-2 py-1 rounded-full">
                            <?= htmlspecialchars($producto['categoria_nombre'] ?? 'Sin categoría') ?>
                        </span>
                        
                        <?php if ($producto['stock'] <= 0): ?>
                        <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">
                            Agotado
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600 transition line-clamp-2 mb-2">
                            <?= htmlspecialchars($producto['nombre']) ?>
                        </h3>
                        <p class="text-sm text-gray-500 mb-2">
                            <?= htmlspecialchars($producto['marca']) ?> <?= htmlspecialchars($producto['modelo']) ?>
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-xl font-bold text-indigo-600">
                                $<?= number_format($producto['precio'], 2) ?>
                            </span>
                            <?php if ($producto['stock'] > 0): ?>
                            <span class="text-xs text-green-600">
                                <i class="fas fa-check-circle"></i> Disponible
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-12 bg-gray-50 rounded-xl">
            <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500">No hay productos destacados disponibles</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Beneficios -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shipping-fast text-2xl text-indigo-600"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Envíos a Todo Panamá</h3>
                <p class="text-gray-600 text-sm">Entregamos en todo el territorio nacional</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-2xl text-green-600"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Garantía de Calidad</h3>
                <p class="text-gray-600 text-sm">Todos nuestros productos tienen garantía</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-2xl text-yellow-600"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Soporte 24/7</h3>
                <p class="text-gray-600 text-sm">Atención al cliente siempre disponible</p>
            </div>
        </div>
    </div>
</section>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>