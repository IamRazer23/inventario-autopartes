<?php
/**
 * Archivo de Configuración Principal
 */

define('DEV_MODE', true);

// Zona horaria
date_default_timezone_set('America/Panama');

// Configuración de errores según el modo
if (DEV_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
}

// =====================================================
// RUTAS DEL SISTEMA
// =====================================================

// Ruta raíz del proyecto
define('ROOT_PATH', dirname(__DIR__));

// Rutas de directorios
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH', ROOT_PATH . '/core');
define('MODELS_PATH', ROOT_PATH . '/models');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('UPLOADS_PATH', ROOT_PATH . '/public/uploads');
define('LOGS_PATH', ROOT_PATH . '/logs');

// URLs base
define('BASE_URL', 'http://localhost/inventario-autopartes');
define('ASSETS_URL', BASE_URL . '/public');
define('UPLOADS_URL', BASE_URL . '/public/uploads');

// =====================================================
// CONFIGURACIÓN DE BASE DE DATOS
// =====================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'inventario_autopartes');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =====================================================
// CONFIGURACIÓN DE SESIONES
// =====================================================

// Nombre de la sesión
define('SESSION_NAME', 'AUTOPARTES_SESSION');

// Tiempo de vida de la sesión (en segundos) - 2 horas
define('SESSION_LIFETIME', 7200);

// Configurar sesión segura
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS

// =====================================================
// CONFIGURACIÓN DE SUBIDA DE ARCHIVOS
// =====================================================

// Tamaño máximo de archivo (5MB en bytes)
define('MAX_FILE_SIZE', 5242880);

// Tipos MIME permitidos para imágenes
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']);

// Extensiones permitidas
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'webp']);

// Directorios de subida
define('UPLOAD_THUMBS_DIR', UPLOADS_PATH . '/thumbs');
define('UPLOAD_IMAGES_DIR', UPLOADS_PATH . '/images');

// =====================================================
// CONFIGURACIÓN DE NEGOCIO
// =====================================================

// ITBMS (Impuesto de Transferencia de Bienes Muebles y Servicios) - Panamá
define('ITBMS_RATE', 0.07); // 7%

// Moneda
define('CURRENCY', 'USD');
define('CURRENCY_SYMBOL', '$');

// Paginación
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// =====================================================
// ROLES DEL SISTEMA
// =====================================================

define('ROL_ADMINISTRADOR', 1);
define('ROL_OPERADOR', 2);
define('ROL_CLIENTE', 3);

// =====================================================
// ESTADOS
// =====================================================

define('ESTADO_ACTIVO', 1);
define('ESTADO_INACTIVO', 0);

// =====================================================
// MENSAJES DEL SISTEMA
// =====================================================

define('MSG_SUCCESS', 'success');
define('MSG_ERROR', 'error');
define('MSG_WARNING', 'warning');
define('MSG_INFO', 'info');

// =====================================================
// CONFIGURACIÓN DE EMAIL (Opcional para futuro)
// =====================================================

define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'tu-email@gmail.com');
define('SMTP_PASS', 'tu-contraseña');
define('SMTP_FROM', 'noreply@autopartes.com');
define('SMTP_FROM_NAME', 'Sistema Autopartes');

// =====================================================
// INICIALIZACIÓN DEL SISTEMA
// =====================================================

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
    
    // Regenerar ID de sesión periódicamente para seguridad
    if (!isset($_SESSION['last_regeneration'])) {
        $_SESSION['last_regeneration'] = time();
    } elseif (time() - $_SESSION['last_regeneration'] > 300) {
        session_regenerate_id(true);
        $_SESSION['last_regeneration'] = time();
    }
}

// Crear directorios necesarios si no existen
$directories = [
    UPLOADS_PATH,
    UPLOAD_THUMBS_DIR,
    UPLOAD_IMAGES_DIR,
    LOGS_PATH
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

// =====================================================
// AUTOLOADER SIMPLE
// =====================================================

spl_autoload_register(function ($class) {
    $paths = [
        CORE_PATH . '/' . $class . '.php',
        MODELS_PATH . '/' . $class . '.php',
        CONTROLLERS_PATH . '/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// =====================================================
// FUNCIONES DE UTILIDAD
// =====================================================

/**
 * Redirecciona a una URL
 * 
 * @param string $url
 * @return void
 */
function redirect($url) {
    header("Location: " . BASE_URL . $url);
    exit;
}

/**
 * Verifica si el usuario está autenticado
 * 
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Verifica si el usuario tiene un rol específico
 * 
 * @param int $rol
 * @return bool
 */
function hasRole($rol) {
    return isAuthenticated() && $_SESSION['rol_id'] == $rol;
}

/**
 * Verifica si el usuario tiene permiso para un módulo
 * 
 * @param string $modulo
 * @param string $accion (crear, leer, actualizar, eliminar)
 * @return bool
 */
function hasPermission($modulo, $accion) {
    if (!isAuthenticated()) {
        return false;
    }
    
    if (!isset($_SESSION['permisos'][$modulo])) {
        return false;
    }
    
    return $_SESSION['permisos'][$modulo][$accion] ?? false;
}

/**
 * Obtiene el usuario actual
 * 
 * @return array|null
 */
function currentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'id' => $_SESSION['usuario_id'],
        'nombre' => $_SESSION['usuario_nombre'],
        'email' => $_SESSION['usuario_email'],
        'rol_id' => $_SESSION['rol_id'],
        'rol_nombre' => $_SESSION['rol_nombre']
    ];
}

/**
 * Formatea un número como moneda
 * 
 * @param float $amount
 * @return string
 */
function formatCurrency($amount) {
    return CURRENCY_SYMBOL . number_format($amount, 2);
}

/**
 * Formatea una fecha
 * 
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'd/m/Y') {
    return date($format, strtotime($date));
}

/**
 * Formatea fecha y hora
 * 
 * @param string $datetime
 * @return string
 */
function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Genera un token CSRF
 * 
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica un token CSRF
 * 
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Establece un mensaje flash
 * 
 * @param string $type
 * @param string $message
 * @return void
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Obtiene y elimina el mensaje flash
 * 
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Limpia la sesión (logout)
 * 
 * @return void
 */
function clearSession() {
    $_SESSION = [];
    
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    session_destroy();
}

/**
 * Genera una URL amigable (slug)
 * 
 * @param string $text
 * @return string
 */
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9-]/', '-', $text);
    $text = preg_replace('/-+/', '-', $text);
    return trim($text, '-');
}

/**
 * Escapa datos para HTML
 * 
 * @param string $data
 * @return string
 */
function e($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Muestra JSON y termina
 * 
 * @param mixed $data
 * @return void
 */
function jsonResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// =====================================================
// INICIALIZAR MANEJADOR DE ERRORES
// =====================================================

// Cargar la interfaz primero
require_once CORE_PATH . '/interfaces/IErrorHandler.php';
require_once CORE_PATH . '/ErrorHandler.php';
$errorHandler = ErrorHandler::getInstance();

?>