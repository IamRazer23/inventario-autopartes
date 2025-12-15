<?php
/**
 * Vista: Crear Usuario
 * Ubicación: views/admin/usuarios/crear.php
 * 
 * @author Grupo 1SF131
 */

// Determinar que es creación
$esEdicion = false;
$titulo = 'Crear Usuario';
$urlAction = BASE_URL . '/index.php?module=admin&action=usuario-store';

// Obtener errores y valores antiguos
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['errors'], $_SESSION['old']);

require_once VIEWS_PATH . '/layouts/header.php';
?>

<div class="container mx-auto px-4 py-8">
    
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            <i class="fas fa-user-plus text-indigo-600 mr-2"></i>
            <?= $titulo ?>
        </h1>
        <p class="text-gray-600 mt-1">
            Completa el formulario para crear un nuevo usuario
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Formulario Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                
                <form action="<?= $urlAction ?>" method="POST" id="formUsuario">
                    
                    <!-- Nombre -->
                    <div class="mb-6">
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-1"></i>
                            Nombre Completo <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            value="<?= e($old['nombre'] ?? '') ?>"
                            required
                            maxlength="100"
                            class="w-full px-4 py-3 border <?= isset($errors['nombre']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="Juan Pérez"
                        >
                        <?php if (isset($errors['nombre'])): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <?= e($errors['nombre']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope text-gray-400 mr-1"></i>
                            Correo Electrónico <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="<?= e($old['email'] ?? '') ?>"
                            required
                            maxlength="100"
                            class="w-full px-4 py-3 border <?= isset($errors['email']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="usuario@ejemplo.com"
                        >
                        <?php if (isset($errors['email'])): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <?= e($errors['email']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Contraseña -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-400 mr-1"></i>
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                minlength="6"
                                class="w-full px-4 py-3 border <?= isset($errors['password']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Mínimo 6 caracteres"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <?= e($errors['password']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-6">
                        <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock text-gray-400 mr-1"></i>
                            Confirmar Contraseña <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password_confirm" 
                                name="password_confirm" 
                                required
                                minlength="6"
                                class="w-full px-4 py-3 border <?= isset($errors['password_confirm']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Confirma la contraseña"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password_confirm')"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <i class="fas fa-eye" id="password_confirm-icon"></i>
                            </button>
                        </div>
                        <?php if (isset($errors['password_confirm'])): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <?= e($errors['password_confirm']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Rol -->
                    <div class="mb-6">
                        <label for="rol_id" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-shield-alt text-gray-400 mr-1"></i>
                            Rol <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="rol_id" 
                            name="rol_id" 
                            required
                            class="w-full px-4 py-3 border <?= isset($errors['rol_id']) ? 'border-red-500' : 'border-gray-300' ?> rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="">Seleccionar rol...</option>
                            <?php if (isset($roles) && is_array($roles)): ?>
                                <?php foreach ($roles as $rol): ?>
                                    <option value="<?= $rol['id'] ?>" <?= ($old['rol_id'] ?? '') == $rol['id'] ? 'selected' : '' ?>>
                                        <?= e($rol['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="1">Administrador</option>
                                <option value="2">Operador</option>
                                <option value="3">Cliente</option>
                            <?php endif; ?>
                        </select>
                        <?php if (isset($errors['rol_id'])): ?>
                            <p class="mt-1 text-sm text-red-600">
                                <i class="fas fa-exclamation-circle mr-1"></i>
                                <?= e($errors['rol_id']) ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Estado -->
                    <div class="mb-6">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                name="estado" 
                                value="1"
                                checked
                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                            >
                            <span class="text-sm font-medium text-gray-700">
                                <i class="fas fa-toggle-on text-green-500 mr-1"></i>
                                Usuario activo
                            </span>
                        </label>
                        <p class="ml-8 mt-1 text-xs text-gray-500">
                            Los usuarios inactivos no pueden iniciar sesión
                        </p>
                    </div>

                    <!-- Botones -->
                    <div class="flex flex-wrap gap-3 pt-6 border-t border-gray-200">
                        <button 
                            type="submit"
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold transition"
                        >
                            <i class="fas fa-save mr-2"></i>
                            Crear Usuario
                        </button>
                        <a 
                            href="<?= BASE_URL ?>/index.php?module=admin&action=usuarios"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold transition text-center"
                        >
                            <i class="fas fa-times mr-2"></i>
                            Cancelar
                        </a>
                        <button 
                            type="reset"
                            class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-6 py-3 rounded-lg font-semibold transition"
                        >
                            <i class="fas fa-redo mr-2"></i>
                            Limpiar
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Panel Lateral: Ayuda -->
        <div class="lg:col-span-1">
            
            <!-- Ayuda -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-blue-900 mb-4">
                    <i class="fas fa-question-circle mr-2"></i>
                    Ayuda
                </h3>
                <ul class="space-y-3 text-sm text-blue-800">
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>El nombre debe tener mínimo 3 caracteres</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>El email debe ser único en el sistema</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>La contraseña debe tener mínimo 6 caracteres</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-blue-600 mr-2 mt-1"></i>
                        <span>Los usuarios inactivos no pueden iniciar sesión</span>
                    </li>
                </ul>
            </div>

            <!-- Roles -->
            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-purple-900 mb-4">
                    <i class="fas fa-shield-alt mr-2"></i>
                    Roles del Sistema
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="bg-white rounded-lg p-3">
                        <p class="font-semibold text-purple-900">Administrador</p>
                        <p class="text-purple-700">Control total del sistema</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="font-semibold text-blue-900">Operador</p>
                        <p class="text-blue-700">Gestión de inventario</p>
                    </div>
                    <div class="bg-white rounded-lg p-3">
                        <p class="font-semibold text-green-900">Cliente</p>
                        <p class="text-green-700">Realiza compras</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
// Toggle visibilidad de contraseña
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validación del formulario
document.getElementById('formUsuario').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        alert('Las contraseñas no coinciden');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('La contraseña debe tener al menos 6 caracteres');
        return false;
    }
});
</script>

<?php require_once VIEWS_PATH . '/layouts/footer.php'; ?>