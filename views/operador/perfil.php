<?php
/**
 * Vista: Perfil del Operador
 * Permite al operador ver y actualizar su información personal
 */

$pageTitle = $pageTitle ?? 'Mi Perfil';
require_once VIEWS_PATH . '/layouts/header.php';

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">
                <i class="fas fa-user-circle text-blue-600 mr-2"></i>
                Mi Perfil
            </h1>
            <p class="text-gray-600">Gestiona tu información personal y credenciales de acceso</p>
        </div>
        <a href="<?= BASE_URL ?>/index.php?module=operador&action=dashboard" 
           class="mt-4 md:mt-0 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
            <i class="fas fa-arrow-left mr-2"></i> Volver al Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Info del Perfil -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mx-auto flex items-center justify-center mb-4">
                        <i class="fas fa-user text-white text-4xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800">
                        <?= e($usuario['nombre'] ?? '') ?>
                    </h3>
                    <p class="text-gray-500">
                        <?= e($usuario['email'] ?? '') ?>
                    </p>
                    <span class="inline-flex items-center px-3 py-1 mt-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <i class="fas fa-user-cog mr-1"></i>
                        <?= e($usuario['rol_nombre'] ?? 'Operador') ?>
                    </span>
                </div>
                
                <div class="border-t pt-4 space-y-3">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-calendar text-gray-400 w-5"></i>
                        <span class="ml-2 text-gray-600">
                            Miembro desde: <?= formatDate($usuario['fecha_creacion'] ?? '', 'd/m/Y') ?>
                        </span>
                    </div>
                    <?php if (!empty($usuario['telefono'])): ?>
                        <div class="flex items-center text-sm">
                            <i class="fas fa-phone text-gray-400 w-5"></i>
                            <span class="ml-2 text-gray-600">
                                <?= e($usuario['telefono']) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-circle text-green-400 w-5"></i>
                        <span class="ml-2 text-gray-600">
                            Estado: <?= ($usuario['estado'] ?? 0) == 1 ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Permisos del Rol -->
            <div class="bg-white rounded-xl shadow-md p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                    <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                    Mis Permisos
                </h3>
                
                <div class="space-y-2">
                    <div class="flex items-center text-sm">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span>Gestionar inventario (crear, editar)</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span>Moderar comentarios</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span>Ver categorías (solo lectura)</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span>Ver ventas (solo lectura)</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-400">
                        <i class="fas fa-times-circle text-red-400 mr-2"></i>
                        <span>Gestionar usuarios</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-400">
                        <i class="fas fa-times-circle text-red-400 mr-2"></i>
                        <span>Eliminar autopartes</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulario de Edición -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b pb-2">
                    <i class="fas fa-edit text-blue-600 mr-2"></i>
                    Editar Información
                </h3>
                
                <form action="<?= BASE_URL ?>/index.php?module=operador&action=actualizar-perfil" method="POST">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Nombre -->
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombre" id="nombre" required
                                   value="<?= e($old['nombre'] ?? $usuario['nombre'] ?? '') ?>"
                                   class="w-full px-4 py-2 border <?= isset($errors['nombre']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php if (isset($errors['nombre'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['nombre'] ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Correo Electrónico <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required
                                   value="<?= e($old['email'] ?? $usuario['email'] ?? '') ?>"
                                   class="w-full px-4 py-2 border <?= isset($errors['email']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php if (isset($errors['email'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['email'] ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1">
                                Teléfono
                            </label>
                            <input type="text" name="telefono" id="telefono"
                                   value="<?= e($old['telefono'] ?? $usuario['telefono'] ?? '') ?>"
                                   placeholder="Ej: +507 6123-4567"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <!-- Separador -->
                    <div class="border-t my-6"></div>
                    
                    <h4 class="text-md font-semibold text-gray-700 mb-4">
                        <i class="fas fa-lock text-yellow-600 mr-2"></i>
                        Cambiar Contraseña (opcional)
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nueva Contraseña -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                Nueva Contraseña
                            </label>
                            <input type="password" name="password" id="password"
                                   placeholder="Dejar vacío para no cambiar"
                                   class="w-full px-4 py-2 border <?= isset($errors['contraseña']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php if (isset($errors['contraseña'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['contraseña'] ?></p>
                            <?php endif; ?>
                            <p class="text-gray-500 text-xs mt-1">Mínimo 6 caracteres</p>
                        </div>
                        
                        <!-- Confirmar Contraseña -->
                        <div>
                            <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-1">
                                Confirmar Contraseña
                            </label>
                            <input type="password" name="password_confirm" id="password_confirm"
                                   placeholder="Repite la nueva contraseña"
                                   class="w-full px-4 py-2 border <?= isset($errors['password_confirm']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php if (isset($errors['password_confirm'])): ?>
                                <p class="text-red-500 text-sm mt-1"><?= $errors['password_confirm'] ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Botones -->
                    <div class="flex justify-end gap-4 mt-8 pt-6 border-t">
                        <a href="<?= BASE_URL ?>/index.php?module=operador&action=dashboard" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            <i class="fas fa-save mr-2"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>
