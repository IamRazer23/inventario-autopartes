<?php
/**
 * Vista: Página Principal Pública
 * Página de inicio para usuarios no autenticados
 */

$pageTitle = 'Inicio - AutoPartes Pro';
$hideSearch = true;

require_once VIEWS_PATH . '/layouts/header.php';
?>

<!-- Hero Section -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="container mx-auto px-4 py-20">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-5xl font-bold mb-6">
                Encuentra las Autopartes que Necesitas
            </h1>
            <p class="text-xl mb-8 text-indigo-100">
                El mejor inventario de autopartes usadas con calidad garantizada
            </p>
            
            <!-- Buscador Principal -->
            <div class="max-w-2xl mx-auto">
                <form action="<?= BASE_URL ?>/index.php" method="GET" class="flex gap-2">
                    <input type="hidden" name="module" value="publico">
                    <input type="hidden" name="action" value="catalogo">
                    <input 
                        type="text" 
                        name="buscar" 
                        placeholder="Busca por marca, modelo o tipo de pieza..."
                        class="flex-1 px-6 py-4 rounded-lg text-gray-900 text-lg focus:outline-none focus:ring-4 focus:ring-white/50"
                    >
                    <button 
                        type="submit"
                        class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-semibold hover:bg-indigo-50 transition"
                    >
                        <i class="fas fa-search mr-2"></i>
                        Buscar
                    </button>
                </form>
            </div>
            
            <!-- Quick Links -->
            <div class="mt-8 flex justify-center gap-4 flex-wrap">
                <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
                   class="bg-white/20 hover:bg-white/30 px-6 py-2 rounded-full text-sm font-medium transition">
                    Ver Catálogo Completo
                </a>
                <?php if (!isAuthenticated()): ?>
                <a href="<?= BASE_URL ?>/index.php?module=auth&action=registro" 
                   class="bg-white/20 hover:bg-white/30 px-6 py-2 rounded-full text-sm font-medium transition">
                    Crear Cuenta
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Categorías Destacadas -->
<div class="container mx-auto px-4 py-16">
    <h2 class="text-3xl font-bold text-gray-800 mb-2 text-center">
        Explora por Categoría
    </h2>
    <p class="text-gray-600 text-center mb-12">
        Encuentra exactamente lo que buscas navegando por categorías
    </p>
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <?php if (!empty($categorias)): ?>
            <?php 
            $iconos = [
                'Motor' => 'fa-cog',
                'Carrocería' => 'fa-car-side',
                'Vidrios' => 'fa-border-all',
                'Eléctrico' => 'fa-bolt',
                'Interior' => 'fa-couch',
                'Suspensión' => 'fa-compress-arrows-alt',
                'Frenos' => 'fa-circle-notch',
                'Transmisión' => 'fa-cogs',
                'default' => 'fa-wrench'
            ];
            foreach ($categorias as $categoria): 
                $icono = $iconos[$categoria['nombre']] ?? $iconos['default'];
            ?>
                <a href="<?= BASE_URL ?>/index.php?module=publico&action=categoria&id=<?= $categoria['id'] ?>" 
                   class="group bg-white rounded-xl shadow-md hover:shadow-xl transition-all p-6 text-center transform hover:-translate-y-1">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas <?= $icono ?> text-white text-3xl"></i>
                    </div>
                    
                    <h3 class="font-bold text-gray-800 group-hover:text-indigo-600 transition">
                        <?= htmlspecialchars($categoria['nombre']) ?>
                    </h3>
                    
                    <p class="text-sm text-gray-500 mt-2">
                        <?= $categoria['total_autopartes'] ?? 0 ?> productos
                    </p>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-8 text-gray-500">
                <i class="fas fa-tags text-5xl mb-4 opacity-50"></i>
                <p>No hay categorías disponibles en este momento</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="text-center mt-8">
        <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold transition">
            Ver Todas las Categorías
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</div>

<!-- Autopartes Destacadas -->
<?php if (!empty($destacadas)): ?>
<div class="bg-gray-50 py-16">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-gray-800 mb-2 text-center">
            Últimas Autopartes Agregadas
        </h2>
        <p class="text-gray-600 text-center mb-12">
            Descubre las piezas más recientes en nuestro inventario
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($destacadas as $autoparte): ?>
                <a href="<?= BASE_URL ?>/index.php?module=publico&action=detalle&id=<?= $autoparte['id'] ?>" 
                   class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all overflow-hidden group">
                    
                    <!-- Imagen -->
                    <div class="relative h-48 bg-gray-200 overflow-hidden">
                        <?php if ($autoparte['imagen_thumb']): ?>
                            <img src="<?= UPLOADS_URL ?>/<?= htmlspecialchars($autoparte['imagen_thumb']) ?>" 
                                 alt="<?= htmlspecialchars($autoparte['nombre']) ?>"
                                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-300 to-gray-400">
                                <i class="fas fa-car text-white text-5xl opacity-50"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Badge de Categoría -->
                        <span class="absolute top-2 right-2 bg-indigo-600 text-white text-xs px-3 py-1 rounded-full">
                            <?= htmlspecialchars($autoparte['categoria_nombre'] ?? 'Sin categoría') ?>
                        </span>
                    </div>
                    
                    <!-- Información -->
                    <div class="p-4">
                        <h3 class="font-bold text-gray-800 mb-2 group-hover:text-indigo-600 transition line-clamp-1">
                            <?= htmlspecialchars($autoparte['nombre']) ?>
                        </h3>
                        
                        <p class="text-sm text-gray-600 mb-3">
                            <?= htmlspecialchars($autoparte['marca']) ?> <?= htmlspecialchars($autoparte['modelo']) ?> (<?= $autoparte['anio'] ?>)
                        </p>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-indigo-600">
                                $<?= number_format($autoparte['precio'], 2) ?>
                            </span>
                            <?php if ($autoparte['stock'] > 0): ?>
                                <span class="text-sm text-green-600">
                                    <i class="fas fa-check-circle mr-1"></i>Disponible
                                </span>
                            <?php else: ?>
                                <span class="text-sm text-red-600">
                                    <i class="fas fa-times-circle mr-1"></i>Agotado
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-8">
            <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-8 py-3 rounded-lg font-semibold transition">
                Ver Más Autopartes
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Características -->
<div class="container mx-auto px-4 py-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="text-center">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-shield-alt text-indigo-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Calidad Garantizada</h3>
            <p class="text-gray-600">
                Todas nuestras autopartes pasan por inspección de calidad antes de ser publicadas
            </p>
        </div>
        
        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-truck text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Envío Disponible</h3>
            <p class="text-gray-600">
                Contamos con servicio de envío a todo el país para tu comodidad
            </p>
        </div>
        
        <div class="text-center">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-headset text-purple-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">Soporte 24/7</h3>
            <p class="text-gray-600">
                Nuestro equipo está disponible para ayudarte en cualquier momento
            </p>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold mb-4">
            ¿Listo para Encontrar tu Autoparte?
        </h2>
        <p class="text-xl mb-8 text-indigo-100">
            Únete a miles de clientes satisfechos que ya encontraron lo que buscaban
        </p>
        <div class="flex justify-center gap-4 flex-wrap">
            <?php if (!isAuthenticated()): ?>
            <a href="<?= BASE_URL ?>/index.php?module=auth&action=registro" 
               class="bg-white text-indigo-600 px-8 py-4 rounded-lg font-bold text-lg hover:bg-indigo-50 transition inline-block">
                <i class="fas fa-user-plus mr-2"></i>
                Crear Cuenta Gratis
            </a>
            <?php endif; ?>
            <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" 
               class="bg-white/20 hover:bg-white/30 text-white px-8 py-4 rounded-lg font-bold text-lg transition inline-block border-2 border-white">
                <i class="fas fa-search mr-2"></i>
                Explorar Catálogo
            </a>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>