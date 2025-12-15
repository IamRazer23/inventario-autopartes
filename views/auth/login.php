<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Autopartes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="w-full max-w-md">
        <!-- Logo y título -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-600 rounded-full mb-4">
                <i class="fas fa-car-side text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Sistema de Autopartes</h1>
            <p class="text-gray-600 mt-2">Ingresa tus credenciales para continuar</p>
        </div>

        <!-- Formulario de Login -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            
            <?php
            // Mostrar mensaje flash
            $flash = getFlashMessage();
            if ($flash):
                $alertColors = [
                    'success' => 'bg-green-100 border-green-400 text-green-700',
                    'error' => 'bg-red-100 border-red-400 text-red-700',
                    'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
                    'info' => 'bg-blue-100 border-blue-400 text-blue-700'
                ];
                $colorClass = $alertColors[$flash['type']] ?? $alertColors['info'];
            ?>
                <div class="<?= $colorClass ?> border px-4 py-3 rounded-lg mb-6" role="alert">
                    <span class="block sm:inline"><?= e($flash['message']) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/index.php?module=auth&action=do_login" method="POST" class="space-y-6">
                
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-envelope text-gray-400 mr-2"></i>
                        Correo Electrónico
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                        placeholder="tu@email.com"
                        value="<?= isset($_SESSION['old']['email']) ? e($_SESSION['old']['email']) : '' ?>"
                    >
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock text-gray-400 mr-2"></i>
                        Contraseña
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                            placeholder="••••••••"
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <!-- Recordar sesión -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                        >
                        <span class="ml-2 text-sm text-gray-600">Recordarme</span>
                    </label>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">
                        ¿Olvidaste tu contraseña?
                    </a>
                </div>

                <!-- Botón de submit -->
                <button 
                    type="submit" 
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Iniciar Sesión
                </button>
            </form>

            <!-- Divisor -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">O</span>
                </div>
            </div>

            <!-- Link a registro -->
            <div class="text-center">
                <p class="text-sm text-gray-600">
                    ¿No tienes cuenta? 
                    <a href="<?= BASE_URL ?>/index.php?module=auth&action=register" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                        Regístrate aquí
                    </a>
                </p>
            </div>

            <!-- Acceso público -->
            <div class="mt-4 text-center">
                <a href="<?= BASE_URL ?>/index.php?module=public&action=catalogo" class="text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-home mr-1"></i>
                    Ver catálogo público
                </a>
            </div>
        </div>

        <!-- Credenciales de prueba (solo en modo desarrollo) -->
       <!-- <?php if (DEV_MODE): ?>
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-xs font-semibold text-yellow-800 mb-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Credenciales de Prueba:
                </p>
                <div class="text-xs text-yellow-700 space-y-1">
                    <p><strong>Admin:</strong> botaciojuan3@gmail.com / root2514</p>
                    <p><strong>Operador:</strong> operador@sistema.com / operador123</p>
                    <p><strong>Cliente:</strong> cliente@sistema.com / cliente123</p>
                </div>
            </div>
        <?php endif; ?>
    </div> -->

    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Limpiar datos antiguos de la sesión
        <?php 
        if (isset($_SESSION['old'])) {
            unset($_SESSION['old']);
        }
        ?>
    </script>
</body>
</html>