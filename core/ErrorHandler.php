<?php
/**
 * Clase ErrorHandler
 * Implementa IErrorHandler para manejo centralizado de errores
 * Cumple con requisito 12: Implementar control de Errores
 */

// La interfaz debe estar cargada ANTES de este archivo
// Se carga en config.php

class ErrorHandler implements IErrorHandler {
    
    private static $instance = null;
    private $errors = [];
    private $logFile;
    
    /**
     * Constructor privado para Singleton
     */
    private function __construct() {
        // Definir ruta del archivo de log
        $this->logFile = __DIR__ . '/../logs/errors.log';
        
        // Crear directorio de logs si no existe
        $logDir = dirname($this->logFile);
        if (!file_exists($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Configurar manejadores de errores
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleFatalError']);
    }
    
    /**
     * Obtiene la instancia única
     * 
     * @return ErrorHandler
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Maneja errores de PHP
     * 
     * @param string $message
     * @param int $code
     * @param string $file
     * @param int $line
     * @return bool
     */
    public function handleError($message, $code = 0, $file = '', $line = 0) {
        // No procesar errores suprimidos con @
        if (error_reporting() === 0) {
            return false;
        }
        
        $error = [
            'type' => 'Error',
            'message' => $message,
            'code' => $code,
            'file' => $file,
            'line' => $line,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $this->errors[] = $error;
        $this->logError($this->formatError($error), 'error');
        
        // En producción, mostrar mensaje genérico
        if (!$this->isDevMode()) {
            $this->showUserFriendlyError();
            exit;
        }
        
        return true;
    }
    
    /**
     * Maneja excepciones no capturadas
     * 
     * @param Exception $exception
     * @return void
     */
    public function handleException($exception) {
        $error = [
            'type' => 'Exception',
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $this->errors[] = $error;
        $this->logError($this->formatError($error), 'critical');
        
        if (!$this->isDevMode()) {
            $this->showUserFriendlyError();
        } else {
            $this->showDetailedError($error);
        }
        
        exit;
    }
    
    /**
     * Maneja errores fatales
     * 
     * @return void
     */
    public function handleFatalError() {
        $error = error_get_last();
        
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $errorData = [
                'type' => 'Fatal Error',
                'message' => $error['message'],
                'code' => $error['type'],
                'file' => $error['file'],
                'line' => $error['line'],
                'timestamp' => date('Y-m-d H:i:s')
            ];
            
            $this->errors[] = $errorData;
            $this->logError($this->formatError($errorData), 'critical');
            
            if (!$this->isDevMode()) {
                $this->showUserFriendlyError();
            }
        }
    }
    
    /**
     * Registra error en archivo de log
     * 
     * @param string $message
     * @param string $level
     * @return void
     */
    public function logError($message, $level = 'error') {
        $logMessage = sprintf(
            "[%s] [%s] %s" . PHP_EOL,
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message
        );
        
        file_put_contents($this->logFile, $logMessage, FILE_APPEND);
    }
    
    /**
     * Formatea un error para el log
     * 
     * @param array $error
     * @return string
     */
    private function formatError($error) {
        return sprintf(
            "%s: %s en %s línea %d",
            $error['type'],
            $error['message'],
            $error['file'],
            $error['line']
        );
    }
    
    /**
     * Muestra error amigable al usuario
     * 
     * @return void
     */
    private function showUserFriendlyError() {
        http_response_code(500);
        
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Ha ocurrido un error. Por favor, intente nuevamente.'
            ]);
        } else {
            include __DIR__ . '/../views/errors/500.php';
        }
    }
    
    /**
     * Muestra error detallado (solo en desarrollo)
     * 
     * @param array $error
     * @return void
     */
    private function showDetailedError($error) {
        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $error
            ]);
        } else {
            echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; margin: 20px; border-radius: 5px;'>";
            echo "<h2 style='color: #721c24;'>{$error['type']}</h2>";
            echo "<p><strong>Mensaje:</strong> {$error['message']}</p>";
            echo "<p><strong>Archivo:</strong> {$error['file']}</p>";
            echo "<p><strong>Línea:</strong> {$error['line']}</p>";
            if (isset($error['trace'])) {
                echo "<details><summary><strong>Stack Trace</strong></summary>";
                echo "<pre>" . htmlspecialchars($error['trace']) . "</pre>";
                echo "</details>";
            }
            echo "</div>";
        }
    }
    
    /**
     * Verifica si es una petición AJAX
     * 
     * @return bool
     */
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Verifica si está en modo desarrollo
     * 
     * @return bool
     */
    private function isDevMode() {
        // Puede leer de un archivo de configuración
        return defined('DEV_MODE') && DEV_MODE === true;
    }
    
    /**
     * Obtiene el último error
     * 
     * @return array|null
     */
    public function getLastError() {
        return !empty($this->errors) ? end($this->errors) : null;
    }
    
    /**
     * Obtiene todos los errores
     * 
     * @return array
     */
    public function getAllErrors() {
        return $this->errors;
    }
    
    /**
     * Limpia los errores almacenados
     * 
     * @return void
     */
    public function clearErrors() {
        $this->errors = [];
    }
    
    /**
     * Registra un mensaje informativo
     * 
     * @param string $message
     * @return void
     */
    public function info($message) {
        $this->logError($message, 'info');
    }
    
    /**
     * Registra una advertencia
     * 
     * @param string $message
     * @return void
     */
    public function warning($message) {
        $this->logError($message, 'warning');
    }
    
    /**
     * Previene clonación
     */
    private function __clone() {}
    
    /**
     * Previene deserialización
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton");
    }
}
?>