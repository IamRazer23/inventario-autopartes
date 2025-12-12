    <?php
/**
 * Funciones Helper del Sistema
 * Contiene todas las funciones auxiliares usadas en el proyecto
 */

/**
 * Verifica si el usuario está autenticado
 * 
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Alias de isAuthenticated para compatibilidad
 * 
 * @return bool
 */
function isLoggedIn() {
    return isAuthenticated();
}

/**
 * Verifica si el usuario tiene un rol específico
 * 
 * @param int|array $roles ID del rol o array de IDs
 * @return bool
 */
function hasRole($roles) {
    if (!isAuthenticated()) {
        return false;
    }
    
    $userRole = $_SESSION['rol_id'] ?? 0;
    
    if (is_array($roles)) {
        return in_array($userRole, $roles);
    }
    
    return $userRole == $roles;
}

/**
 * Verifica si el usuario tiene alguno de los roles especificados
 * 
 * @param array $roles
 * @return bool
 */
function hasAnyRole(array $roles) {
    return hasRole($roles);
}

/**
 * Verifica si el usuario es administrador
 * 
 * @return bool
 */
function isAdmin() {
    return hasRole(ROL_ADMINISTRADOR);
}

/**
 * Verifica si el usuario es operador
 * 
 * @return bool
 */
function isOperador() {
    return hasRole(ROL_OPERADOR);
}

/**
 * Verifica si el usuario es cliente
 * 
 * @return bool
 */
function isCliente() {
    return hasRole(ROL_CLIENTE);
}

/**
 * Obtiene el ID del usuario actual
 * 
 * @return int|null
 */
function getCurrentUserId() {
    return $_SESSION['usuario_id'] ?? null;
}

/**
 * Obtiene el nombre del usuario actual
 * 
 * @return string|null
 */
function getCurrentUserName() {
    return $_SESSION['usuario_nombre'] ?? null;
}

/**
 * Obtiene el rol del usuario actual
 * 
 * @return int|null
 */
function getCurrentUserRole() {
    return $_SESSION['rol_id'] ?? null;
}

/**
 * Obtiene el nombre del rol del usuario actual
 * 
 * @return string|null
 */
function getCurrentRoleName() {
    return $_SESSION['rol_nombre'] ?? null;
}

// ============================================================================
// FUNCIONES DE MENSAJES FLASH
// ============================================================================

/**
 * Establece un mensaje flash en la sesión
 * 
 * @param string $type Tipo de mensaje (success, error, warning, info)
 * @param string $message Mensaje a mostrar
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Obtiene y elimina el mensaje flash
 * 
 * @return array|null ['type' => string, 'message' => string]
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $flash = [
            'type' => $_SESSION['flash_type'] ?? 'info',
            'message' => $_SESSION['flash_message']
        ];
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return $flash;
    }
    return null;
}

/**
 * Verifica si hay un mensaje flash pendiente
 * 
 * @return bool
 */
function hasFlashMessage() {
    return isset($_SESSION['flash_message']);
}

// ============================================================================
// FUNCIONES DE REDIRECCIÓN
// ============================================================================

/**
 * Redirige a una URL
 * 
 * @param string $url URL relativa o absoluta
 * @param int $statusCode Código HTTP (default 302)
 */
function redirect($url, $statusCode = 302) {
    // Si es una URL relativa, agregar BASE_URL
    if (strpos($url, 'http') !== 0 && strpos($url, '/') === 0) {
        $url = BASE_URL . $url;
    }
    
    header("Location: " . $url, true, $statusCode);
    exit;
}

/**
 * Redirige a la página anterior
 */
function redirectBack() {
    $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
    redirect($referer);
}

/**
 * Redirige con un mensaje flash
 * 
 * @param string $url
 * @param string $type
 * @param string $message
 */
function redirectWithMessage($url, $type, $message) {
    setFlashMessage($type, $message);
    redirect($url);
}

// ============================================================================
// FUNCIONES DE RESPUESTA JSON (AJAX)
// ============================================================================

/**
 * Envía una respuesta JSON y termina la ejecución
 * 
 * @param array $data Datos a enviar
 * @param int $statusCode Código HTTP
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Envía una respuesta JSON de éxito
 * 
 * @param string $message
 * @param array $data Datos adicionales
 */
function jsonSuccess($message = 'Operación exitosa', $data = []) {
    jsonResponse(array_merge(['success' => true, 'message' => $message], $data));
}

/**
 * Envía una respuesta JSON de error
 * 
 * @param string $message
 * @param int $statusCode
 */
function jsonError($message = 'Error en la operación', $statusCode = 400) {
    jsonResponse(['success' => false, 'message' => $message], $statusCode);
}

// ============================================================================
// FUNCIONES DE SANITIZACIÓN Y ESCAPE
// ============================================================================

/**
 * Escapa HTML para prevenir XSS
 * 
 * @param string|null $string
 * @return string
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Alias de htmlspecialchars para compatibilidad
 * 
 * @param string|null $string
 * @return string
 */
function h($string) {
    return e($string);
}

/**
 * Sanitiza una cadena de texto
 * 
 * @param string $string
 * @return string
 */
function sanitizeString($string) {
    return trim(strip_tags($string));
}

/**
 * Sanitiza un entero
 * 
 * @param mixed $value
 * @return int
 */
function sanitizeInt($value) {
    return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Sanitiza un flotante
 * 
 * @param mixed $value
 * @return float
 */
function sanitizeFloat($value) {
    return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

/**
 * Sanitiza un email
 * 
 * @param string $email
 * @return string
 */
function sanitizeEmail($email) {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

// ============================================================================
// FUNCIONES DE FORMATO
// ============================================================================

/**
 * Formatea un precio como moneda
 * 
 * @param float $amount
 * @param string $symbol
 * @return string
 */
function formatCurrency($amount, $symbol = '$') {
    return $symbol . number_format((float)$amount, 2, '.', ',');
}

/**
 * Formatea un número con separadores de miles
 * 
 * @param float $number
 * @param int $decimals
 * @return string
 */
function formatNumber($number, $decimals = 0) {
    return number_format((float)$number, $decimals, '.', ',');
}

/**
 * Formatea una fecha
 * 
 * @param string $date
 * @param string $format
 * @return string
 */
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : '';
}

/**
 * Formatea una fecha con hora
 * 
 * @param string $datetime
 * @param string $format
 * @return string
 */
function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    return formatDate($datetime, $format);
}

/**
 * Formatea una fecha de manera relativa (hace X tiempo)
 * 
 * @param string $datetime
 * @return string
 */
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return 'hace un momento';
    } elseif ($diff < 3600) {
        $mins = round($diff / 60);
        return "hace {$mins} " . ($mins == 1 ? 'minuto' : 'minutos');
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        return "hace {$hours} " . ($hours == 1 ? 'hora' : 'horas');
    } elseif ($diff < 604800) {
        $days = round($diff / 86400);
        return "hace {$days} " . ($days == 1 ? 'día' : 'días');
    } elseif ($diff < 2592000) {
        $weeks = round($diff / 604800);
        return "hace {$weeks} " . ($weeks == 1 ? 'semana' : 'semanas');
    } else {
        return formatDate($datetime);
    }
}

/**
 * Trunca un texto a una longitud específica
 * 
 * @param string $text
 * @param int $length
 * @param string $suffix
 * @return string
 */
function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length - strlen($suffix)) . $suffix;
}

/**
 * Genera un slug a partir de un texto
 * 
 * @param string $text
 * @return string
 */
function slugify($text) {
    // Reemplazar caracteres especiales
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterar
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remover caracteres no deseados
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remover duplicados
    $text = preg_replace('~-+~', '-', $text);
    // Minúsculas
    return strtolower($text);
}

// ============================================================================
// FUNCIONES DE VALIDACIÓN
// ============================================================================

/**
 * Verifica si es una petición AJAX
 * 
 * @return bool
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Verifica si es una petición POST
 * 
 * @return bool
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Verifica si es una petición GET
 * 
 * @return bool
 */
function isGet() {
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/**
 * Valida un email
 * 
 * @param string $email
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida una URL
 * 
 * @param string $url
 * @return bool
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

// ============================================================================
// FUNCIONES DE ARCHIVOS E IMÁGENES
// ============================================================================

/**
 * Obtiene la extensión de un archivo
 * 
 * @param string $filename
 * @return string
 */
function getFileExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

/**
 * Genera un nombre único para un archivo
 * 
 * @param string $originalName
 * @param string $prefix
 * @return string
 */
function generateUniqueFilename($originalName, $prefix = '') {
    $extension = getFileExtension($originalName);
    return $prefix . uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Verifica si un archivo es una imagen válida
 * 
 * @param string $filename
 * @return bool
 */
function isValidImage($filename) {
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    return in_array(getFileExtension($filename), $allowedExtensions);
}

/**
 * Formatea el tamaño de un archivo
 * 
 * @param int $bytes
 * @return string
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
}

// ============================================================================
// FUNCIONES DE CARRITO
// ============================================================================

/**
 * Obtiene el número de items en el carrito
 * 
 * @return int
 */
function getCartItemCount() {
    return $_SESSION['carrito_items'] ?? 0;
}

/**
 * Actualiza el contador del carrito en sesión
 * 
 * @param int $count
 */
function updateCartCount($count) {
    $_SESSION['carrito_items'] = max(0, (int)$count);
}

// ============================================================================
// FUNCIONES DE SEGURIDAD
// ============================================================================

/**
 * Genera un token CSRF
 * 
 * @return string
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
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
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Genera el campo HTML del token CSRF
 * 
 * @return string
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCsrfToken() . '">';
}

/**
 * Hashea una contraseña
 * 
 * @param string $password
 * @return string
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifica una contraseña
 * 
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// ============================================================================
// FUNCIONES DE PAGINACIÓN
// ============================================================================

/**
 * Genera información de paginación
 * 
 * @param int $totalItems
 * @param int $currentPage
 * @param int $perPage
 * @return array
 */
function paginate($totalItems, $currentPage = 1, $perPage = 10) {
    $totalPages = ceil($totalItems / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'previous_page' => max(1, $currentPage - 1),
        'next_page' => min($totalPages, $currentPage + 1)
    ];
}

/**
 * Genera un array de números de página para mostrar
 * 
 * @param int $currentPage
 * @param int $totalPages
 * @param int $range
 * @return array
 */
function getPageNumbers($currentPage, $totalPages, $range = 2) {
    $pages = [];
    
    $start = max(1, $currentPage - $range);
    $end = min($totalPages, $currentPage + $range);
    
    // Siempre incluir primera página
    if ($start > 1) {
        $pages[] = 1;
        if ($start > 2) {
            $pages[] = '...';
        }
    }
    
    // Páginas del rango
    for ($i = $start; $i <= $end; $i++) {
        $pages[] = $i;
    }
    
    // Siempre incluir última página
    if ($end < $totalPages) {
        if ($end < $totalPages - 1) {
            $pages[] = '...';
        }
        $pages[] = $totalPages;
    }
    
    return $pages;
}

// ============================================================================
// FUNCIONES DE UTILIDAD
// ============================================================================

/**
 * Obtiene un valor de un array con valor por defecto
 * 
 * @param array $array
 * @param string $key
 * @param mixed $default
 * @return mixed
 */
function array_get($array, $key, $default = null) {
    return $array[$key] ?? $default;
}

/**
 * Verifica si estamos en modo desarrollo
 * 
 * @return bool
 */
function isDevelopment() {
    return defined('DEV_MODE') && DEV_MODE === true;
}

/**
 * Debug: muestra una variable y termina la ejecución
 * 
 * @param mixed $data
 */
function dd($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit;
}

/**
 * Debug: muestra una variable sin terminar
 * 
 * @param mixed $data
 */
function dump($data) {
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}

/**
 * Obtiene la URL actual completa
 * 
 * @return string
 */
function getCurrentUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

/**
 * Obtiene el nombre del módulo actual
 * 
 * @return string
 */
function getCurrentModule() {
    return $_GET['module'] ?? 'public';
}

/**
 * Obtiene la acción actual
 * 
 * @return string
 */
function getCurrentAction() {
    return $_GET['action'] ?? 'index';
}

/**
 * Genera una URL con parámetros
 * 
 * @param string $module
 * @param string $action
 * @param array $params
 * @return string
 */
function url($module, $action = 'index', $params = []) {
    $url = BASE_URL . '/index.php?module=' . $module . '&action=' . $action;
    
    if (!empty($params)) {
        $url .= '&' . http_build_query($params);
    }
    
    return $url;
}

/**
 * Genera URL para assets
 * 
 * @param string $path
 * @return string
 */
function asset($path) {
    return ASSETS_URL . '/' . ltrim($path, '/');
}

/**
 * Genera URL para uploads
 * 
 * @param string $path
 * @return string
 */
function upload($path) {
    return UPLOADS_URL . '/' . ltrim($path, '/');
}