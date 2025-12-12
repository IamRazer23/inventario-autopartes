<?php
/**
 * Vista: Perfil del Administrador
 */

$pageTitle = $pageTitle ?? 'Mi Perfil';
require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= BASE_URL ?>/index.php?module=admin&action=dashboard" class="hover:text-indigo-600"><i class="fas fa-home"></i></a></li>
            <li><i class="fas fa-chevron-right text-xs text-gray-400"></i></li>
            <li class="text-indigo-600 font-medium">Mi Perfil</li>
        </ol>
    </nav>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">
            <i class="fas fa-user-shield text-indigo-600 mr-3"></i>Mi Perfil
        </h1>
        <p class="text-gray-600">Administra tu información de cuenta</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Info del usuario -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-center">
                    <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <span class="text-4xl font-bold text-indigo-600">
                            <?= strtoupper(substr($usuario['nombre'], 0, 1)) ?>
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($usuario['nombre']) ?></h2>
                    <p class="text-indigo-200"><?= htmlspecialchars($usuario['email']) ?></p>
                    <span class="inline-block mt-2 bg-white/20 text-white text-sm px-3 py-1 rounded-full">
                        <i class="fas fa-shield-alt mr-1"></i><?= htmlspecialchars($usuario['rol_nombre']) ?>
                    </span>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Miembro desde</span>
                        <span class="font-medium text-gray-800"><?= formatDate($usuario['fecha_creacion']) ?></span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Estado</span>
                        <span class="inline-flex items-center bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1"></i>Activo
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas del sistema -->
            <div class="bg-white rounded-xl shadow-md p-6 mt-6">
                <h3 class="font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>Sistema
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total usuarios</span>
                        <span class="font-bold text-indigo-600"><?= $stats['total_usuarios'] ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total autopartes</span>
                        <span class="font-bold text-green-600"><?= $stats['total_autopartes'] ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Total ventas</span>
                        <span class="font-bold text-purple-600"><?= $stats['total_ventas'] ?></span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Formulario de edición -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="bg-gray-50 px-6 py-4 border-b">
                    <h2 class="font-bold text-lg text-gray-800">
                        <i class="fas fa-edit text-indigo-600 mr-2"></i>Editar Información
                    </h2>
                </div>
                
                <form action="<?= BASE_URL ?>/index.php?module=admin&action=perfil-update" method="POST" class="p-6">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Información Personal</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo</label>
                                <input type="text" name="nombre" value="<?= htmlspecialchars($usuario['nombre']) ?>" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">
                            Cambiar Contraseña
                            <span class="text-xs font-normal text-gray-400">(opcional)</span>
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña Actual</label>
                                <input type="password" name="password_actual" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                       placeholder="••••••••">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nueva Contraseña</label>
                                <input type="password" name="password_nuevo" id="password_nuevo"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                       placeholder="••••••••" minlength="6">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar</label>
                                <input type="password" name="password_confirm" id="password_confirm"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                                       placeholder="••••••••">
                            </div>
                        </div>
                        <p id="password-match" class="mt-2 text-sm hidden"></p>
                    </div>
                    
                    <div class="flex justify-end gap-4 pt-4 border-t">
                        <a href="<?= BASE_URL ?>/index.php?module=admin&action=dashboard" 
                           class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition">
                            Cancelar
                        </a>
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition">
                            <i class="fas fa-save mr-2"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordNuevo = document.getElementById('password_nuevo');
    const passwordConfirm = document.getElementById('password_confirm');
    const passwordMatch = document.getElementById('password-match');
    
    function checkPasswords() {
        if (passwordNuevo.value || passwordConfirm.value) {
            passwordMatch.classList.remove('hidden');
            if (passwordNuevo.value === passwordConfirm.value) {
                passwordMatch.textContent = '✓ Las contraseñas coinciden';
                passwordMatch.className = 'mt-2 text-sm text-green-600';
            } else {
                passwordMatch.textContent = '✗ Las contraseñas no coinciden';
                passwordMatch.className = 'mt-2 text-sm text-red-600';
            }
        } else {
            passwordMatch.classList.add('hidden');
        }
    }
    
    passwordNuevo.addEventListener('input', checkPasswords);
    passwordConfirm.addEventListener('input', checkPasswords);
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>