<?php
/**
 * Vista: Página de Contacto
 */

$pageTitle = 'Contacto - AutoPartes Pro';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h1 class="text-4xl font-bold mb-4">Contáctanos</h1>
        <p class="text-xl text-indigo-100">Estamos aquí para ayudarte</p>
    </div>
</div>

<div class="container mx-auto px-4 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Información de Contacto -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">Información de Contacto</h3>
                
                <div class="space-y-4">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-map-marker-alt text-indigo-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Dirección</h4>
                            <p class="text-gray-600">Vía España, Ciudad de Panamá</p>
                            <p class="text-gray-600">Panamá</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-phone text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Teléfono</h4>
                            <p class="text-gray-600">+507 6123-4567</p>
                            <p class="text-gray-600">+507 234-5678</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-envelope text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Email</h4>
                            <p class="text-gray-600">info@autopartes.com</p>
                            <p class="text-gray-600">ventas@autopartes.com</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-clock text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">Horario</h4>
                            <p class="text-gray-600">Lun - Vie: 8:00 AM - 6:00 PM</p>
                            <p class="text-gray-600">Sáb: 8:00 AM - 2:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Redes Sociales -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Síguenos</h3>
                <div class="flex space-x-4">
                    <a href="#" class="w-12 h-12 bg-blue-600 text-white rounded-lg flex items-center justify-center hover:bg-blue-700 transition-colors">
                        <i class="fab fa-facebook-f text-xl"></i>
                    </a>
                    <a href="#" class="w-12 h-12 bg-pink-600 text-white rounded-lg flex items-center justify-center hover:bg-pink-700 transition-colors">
                        <i class="fab fa-instagram text-xl"></i>
                    </a>
                    <a href="#" class="w-12 h-12 bg-green-500 text-white rounded-lg flex items-center justify-center hover:bg-green-600 transition-colors">
                        <i class="fab fa-whatsapp text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Formulario de Contacto -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Envíanos un Mensaje</h3>
                
                <?php if (isset($mensaje_enviado) && $mensaje_enviado): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                        <i class="fas fa-check-circle mr-2"></i>
                        ¡Mensaje enviado correctamente! Te contactaremos pronto.
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= BASE_URL ?>/index.php?module=public&action=contacto" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre Completo *
                            </label>
                            <input type="text" id="nombre" name="nombre" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors <?= isset($errores['nombre']) ? 'border-red-500' : '' ?>"
                                   value="<?= e($_POST['nombre'] ?? '') ?>"
                                   placeholder="Tu nombre">
                            <?php if (isset($errores['nombre'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errores['nombre'] ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Correo Electrónico *
                            </label>
                            <input type="email" id="email" name="email" required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors <?= isset($errores['email']) ? 'border-red-500' : '' ?>"
                                   value="<?= e($_POST['email'] ?? '') ?>"
                                   placeholder="tu@email.com">
                            <?php if (isset($errores['email'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errores['email'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono
                            </label>
                            <input type="tel" id="telefono" name="telefono"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                   value="<?= e($_POST['telefono'] ?? '') ?>"
                                   placeholder="+507 6XXX-XXXX">
                        </div>
                        
                        <div>
                            <label for="asunto" class="block text-sm font-medium text-gray-700 mb-2">
                                Asunto
                            </label>
                            <select id="asunto" name="asunto"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                <option value="">Selecciona un asunto</option>
                                <option value="consulta" <?= ($_POST['asunto'] ?? '') == 'consulta' ? 'selected' : '' ?>>Consulta General</option>
                                <option value="cotizacion" <?= ($_POST['asunto'] ?? '') == 'cotizacion' ? 'selected' : '' ?>>Solicitar Cotización</option>
                                <option value="disponibilidad" <?= ($_POST['asunto'] ?? '') == 'disponibilidad' ? 'selected' : '' ?>>Consultar Disponibilidad</option>
                                <option value="reclamo" <?= ($_POST['asunto'] ?? '') == 'reclamo' ? 'selected' : '' ?>>Reclamo</option>
                                <option value="otro" <?= ($_POST['asunto'] ?? '') == 'otro' ? 'selected' : '' ?>>Otro</option>
                            </select>
                        </div>
                    </div>
                    
                    <div>
                        <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-2">
                            Mensaje *
                        </label>
                        <textarea id="mensaje" name="mensaje" rows="5" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none <?= isset($errores['mensaje']) ? 'border-red-500' : '' ?>"
                                  placeholder="Escribe tu mensaje aquí..."><?= e($_POST['mensaje'] ?? '') ?></textarea>
                        <?php if (isset($errores['mensaje'])): ?>
                            <p class="text-red-500 text-sm mt-1"><?= $errores['mensaje'] ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <button type="submit"
                                class="w-full md:w-auto px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar Mensaje
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>