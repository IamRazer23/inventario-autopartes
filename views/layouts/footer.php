</main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-auto">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                
                <!-- Columna 1: Sobre nosotros -->
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-car-side text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold">AutoPartes Pro</h3>
                    </div>
                    <p class="text-gray-400 text-sm mb-4">
                        Tu mejor opción para encontrar autopartes de calidad. Amplio inventario y los mejores precios del mercado.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-8 h-8 bg-gray-700 hover:bg-indigo-600 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-facebook-f text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-700 hover:bg-indigo-600 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-instagram text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-700 hover:bg-indigo-600 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-twitter text-sm"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-gray-700 hover:bg-indigo-600 rounded-full flex items-center justify-center transition-colors">
                            <i class="fab fa-whatsapp text-sm"></i>
                        </a>
                    </div>
                </div>

                <!-- Columna 2: Enlaces rápidos -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Enlaces Rápidos</h4>
                    <ul class="space-y-2 text-sm">
                        <li>
                            <a href="<?= BASE_URL ?>/index.php" class="text-gray-400 hover:text-white flex items-center transition-colors">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>Inicio
                            </a>
                        </li>
                        <li>
                            <a href="<?= BASE_URL ?>/index.php?module=publico&action=catalogo" class="text-gray-400 hover:text-white flex items-center transition-colors">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>Catálogo
                            </a>
                        </li>
                        <li>
                            <a href="#" class="text-gray-400 hover:text-white flex items-center transition-colors">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>Sobre Nosotros
                            </a>
                        </li>
                        <li>
                            <a href="#contacto" class="text-gray-400 hover:text-white flex items-center transition-colors">
                                <i class="fas fa-chevron-right text-xs mr-2"></i>Contacto
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Columna 3: Categorías populares -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Categorías</h4>
                    <ul class="space-y-2 text-sm">
                        <?php 
                        try {
                            $db = Database::getInstance();
                            $cats = $db->fetchAll("SELECT id, nombre FROM categorias WHERE estado = 1 ORDER BY nombre LIMIT 5");
                            foreach ($cats as $cat): 
                        ?>
                        <li>
                            <a href="<?= BASE_URL ?>/index.php?module=publico&action=categoria&id=<?= $cat['id'] ?>" class="text-gray-400 hover:text-white flex items-center transition-colors">
                                <i class="fas fa-cog text-xs mr-2"></i><?= htmlspecialchars($cat['nombre']) ?>
                            </a>
                        </li>
                        <?php endforeach; } catch (Exception $e) {} ?>
                    </ul>
                </div>

                <!-- Columna 4: Contacto -->
                <div id="contacto">
                    <h4 class="text-lg font-semibold mb-4">Contacto</h4>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-indigo-500"></i>
                            <span>Panamá, Panamá<br>Ciudad de Panamá</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-3 text-indigo-500"></i>
                            <a href="tel:+5076008-6038" class="hover:text-white transition-colors">+507 6008-6038</a>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-indigo-500"></i>
                            <a href="mailto:info@autopartes.com" class="hover:text-white transition-colors">info@autopartes.com</a>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-clock mt-1 mr-3 text-indigo-500"></i>
                            <span>Lun-Vie: 8:00 AM - 6:00 PM<br>Sábado: 9:00 AM - 2:00 PM</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter -->
            <div class="mt-8 pt-8 border-t border-gray-700">
                <div class="max-w-xl mx-auto text-center">
                    <h4 class="text-lg font-semibold mb-2">Suscríbete a nuestro Newsletter</h4>
                    <p class="text-gray-400 text-sm mb-4">Recibe ofertas exclusivas y novedades</p>
                    <form class="flex gap-2" onsubmit="return false;">
                        <input 
                            type="email" 
                            placeholder="Tu correo electrónico" 
                            class="flex-1 px-4 py-2 rounded-lg bg-gray-700 text-white border border-gray-600 focus:outline-none focus:border-indigo-500"
                            required
                        >
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg font-semibold whitespace-nowrap transition-colors">
                            Suscribirse
                        </button>
                    </form>
                </div>
            </div>

            <!-- Métodos de pago -->
            <div class="mt-8 pt-8 border-t border-gray-700">
                <div class="text-center">
                    <p class="text-gray-400 text-sm mb-4">Métodos de pago aceptados</p>
                    <div class="flex justify-center items-center space-x-4 text-3xl text-gray-500">
                        <i class="fab fa-cc-visa hover:text-white transition-colors cursor-pointer"></i>
                        <i class="fab fa-cc-mastercard hover:text-white transition-colors cursor-pointer"></i>
                        <i class="fab fa-cc-amex hover:text-white transition-colors cursor-pointer"></i>
                        <i class="fas fa-credit-card hover:text-white transition-colors cursor-pointer"></i>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="mt-8 pt-8 border-t border-gray-700 text-center text-sm text-gray-400">
                <p>
                    &copy; <?= date('Y') ?> AutoPartes Pro. Todos los derechos reservados.
                    <?php if (defined('DEV_MODE') && DEV_MODE): ?>
                        <span class="text-yellow-500 ml-2">
                            <i class="fas fa-code"></i> MODO DESARROLLO
                        </span>
                    <?php endif; ?>
                </p>
                <p class="mt-2">
                    <a href="#" class="hover:text-white mx-2 transition-colors">Términos y Condiciones</a> | 
                    <a href="#" class="hover:text-white mx-2 transition-colors">Política de Privacidad</a> | 
                    <a href="#" class="hover:text-white mx-2 transition-colors">Política de Devoluciones</a>
                </p>
                <p class="mt-4 text-xs text-gray-500">
                    Desarrollado por Grupo 1SF131 - Universidad Tecnológica de Panamá
                    <br>Proyecto Final - Ingeniería Web
                </p>
            </div>
        </div>
    </footer>

    <!-- Botón "Volver arriba" -->
    <button 
        id="back-to-top"
        onclick="scrollToTop()"
        class="fixed bottom-8 right-8 bg-indigo-600 hover:bg-indigo-700 text-white w-12 h-12 rounded-full shadow-lg hidden items-center justify-center transition-all duration-300 z-40"
        aria-label="Volver arriba"
    >
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Scripts adicionales -->
    <?php if (isset($customScripts)): ?>
        <?= $customScripts ?>
    <?php endif; ?>

    <script>
        // Botón "Volver arriba"
        const backToTopButton = document.getElementById('back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.remove('hidden');
                backToTopButton.classList.add('flex');
            } else {
                backToTopButton.classList.add('hidden');
                backToTopButton.classList.remove('flex');
            }
        });
        
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Auto-cerrar alertas después de 5 segundos
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => {
                setTimeout(() => {
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 300);
                }, 5000);
            });
        });
    </script>
</body>
</html>