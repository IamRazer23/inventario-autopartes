<?php
/**
 * Clase Validator
 * Sanitiza y valida datos de entrada
 */

class Validator {
    
    private $errors = [];
    
    /**
     * Sanitiza una cadena de texto
     * 
     * @param string $data
     * @return string
     */
    public static function sanitizeString($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        return $data;
    }
    
    /**
     * Sanitiza un email
     * 
     * @param string $email
     * @return string
     */
    public static function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Sanitiza un número entero
     * 
     * @param mixed $number
     * @return int
     */
    public static function sanitizeInt($number) {
        return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Sanitiza un número decimal
     * 
     * @param mixed $number
     * @return float
     */
    public static function sanitizeFloat($number) {
        return filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
    
    /**
     * Sanitiza una URL
     * 
     * @param string $url
     * @return string
     */
    public static function sanitizeUrl($url) {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }
    
    /**
     * Valida si un campo está vacío
     * 
     * @param mixed $value
     * @param string $fieldName
     * @return bool
     */
    public function required($value, $fieldName = 'Campo') {
        if (empty($value) && $value !== '0') {
            $this->errors[$fieldName] = "$fieldName es requerido";
            return false;
        }
        return true;
    }
    
    /**
     * Valida un email
     * 
     * @param string $email
     * @param string $fieldName
     * @return bool
     */
    public function email($email, $fieldName = 'Email') {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$fieldName] = "$fieldName no es válido";
            return false;
        }
        return true;
    }
    
    /**
     * Valida longitud mínima
     * 
     * @param string $value
     * @param int $min
     * @param string $fieldName
     * @return bool
     */
    public function minLength($value, $min, $fieldName = 'Campo') {
        if (strlen($value) < $min) {
            $this->errors[$fieldName] = "$fieldName debe tener al menos $min caracteres";
            return false;
        }
        return true;
    }
    
    /**
     * Valida longitud máxima
     * 
     * @param string $value
     * @param int $max
     * @param string $fieldName
     * @return bool
     */
    public function maxLength($value, $max, $fieldName = 'Campo') {
        if (strlen($value) > $max) {
            $this->errors[$fieldName] = "$fieldName no debe exceder $max caracteres";
            return false;
        }
        return true;
    }
    
    /**
     * Valida que sea un número
     * 
     * @param mixed $value
     * @param string $fieldName
     * @return bool
     */
    public function numeric($value, $fieldName = 'Campo') {
        if (!is_numeric($value)) {
            $this->errors[$fieldName] = "$fieldName debe ser numérico";
            return false;
        }
        return true;
    }
    
    /**
     * Valida rango numérico
     * 
     * @param float $value
     * @param float $min
     * @param float $max
     * @param string $fieldName
     * @return bool
     */
    public function range($value, $min, $max, $fieldName = 'Campo') {
        if ($value < $min || $value > $max) {
            $this->errors[$fieldName] = "$fieldName debe estar entre $min y $max";
            return false;
        }
        return true;
    }
    
    /**
     * Valida formato de año
     * 
     * @param int $year
     * @param string $fieldName
     * @return bool
     */
    public function validYear($year, $fieldName = 'Año') {
        $currentYear = date('Y');
        if ($year < 1900 || $year > $currentYear + 1) {
            $this->errors[$fieldName] = "$fieldName debe estar entre 1900 y " . ($currentYear + 1);
            return false;
        }
        return true;
    }
    
    /**
     * Valida una fecha
     * 
     * @param string $date
     * @param string $format
     * @param string $fieldName
     * @return bool
     */
    public function date($date, $format = 'Y-m-d', $fieldName = 'Fecha') {
        $d = DateTime::createFromFormat($format, $date);
        if (!$d || $d->format($format) !== $date) {
            $this->errors[$fieldName] = "$fieldName no tiene un formato válido";
            return false;
        }
        return true;
    }
    
    /**
     * Valida archivo de imagen
     * 
     * @param array $file $_FILES['name']
     * @param int $maxSize Tamaño máximo en bytes
     * @param string $fieldName
     * @return bool
     */
    public function validateImage($file, $maxSize = 5242880, $fieldName = 'Imagen') {
        // Verificar si hay error en la carga
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[$fieldName] = "Error al subir $fieldName";
            return false;
        }
        
        // Verificar tamaño
        if ($file['size'] > $maxSize) {
            $sizeMB = $maxSize / 1048576;
            $this->errors[$fieldName] = "$fieldName no debe exceder {$sizeMB}MB";
            return false;
        }
        
        // Verificar tipo MIME
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $this->errors[$fieldName] = "$fieldName debe ser JPG, PNG o WEBP";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida contraseña segura
     * 
     * @param string $password
     * @param string $fieldName
     * @return bool
     */
    public function securePassword($password, $fieldName = 'Contraseña') {
        if (strlen($password) < 8) {
            $this->errors[$fieldName] = "$fieldName debe tener al menos 8 caracteres";
            return false;
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $this->errors[$fieldName] = "$fieldName debe contener al menos una mayúscula";
            return false;
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $this->errors[$fieldName] = "$fieldName debe contener al menos una minúscula";
            return false;
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $this->errors[$fieldName] = "$fieldName debe contener al menos un número";
            return false;
        }
        
        return true;
    }
    
    /**
     * Valida confirmación de contraseña
     * 
     * @param string $password
     * @param string $confirm
     * @return bool
     */
    public function passwordMatch($password, $confirm) {
        if ($password !== $confirm) {
            $this->errors['password_confirm'] = "Las contraseñas no coinciden";
            return false;
        }
        return true;
    }
    
    /**
     * Protege contra inyección SQL (usando preparadas, esto es adicional)
     * 
     * @param string $data
     * @return string
     */
    public static function antiSqlInjection($data) {
        $data = trim($data);
        $data = stripslashes($data);
        // Remover caracteres peligrosos
        $data = preg_replace('/[^a-zA-Z0-9\s@._-]/', '', $data);
        return $data;
    }
    
    /**
     * Protege contra XSS
     * 
     * @param string $data
     * @return string
     */
    public static function antiXSS($data) {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    /**
     * Obtiene todos los errores
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Verifica si hay errores
     * 
     * @return bool
     */
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    /**
     * Limpia los errores
     */
    public function clearErrors() {
        $this->errors = [];
    }
    
    /**
     * Obtiene el primer error
     * 
     * @return string|null
     */
    public function getFirstError() {
        return !empty($this->errors) ? reset($this->errors) : null;
    }
}
?>