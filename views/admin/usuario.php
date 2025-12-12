<?php
/**
 * Clase Usuario
 * Modelo para gestión de usuarios del sistema
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../core/Validator.php';

class Usuario {
    
    private $db;
    private $validator;
    
    // Propiedades del usuario
    public $id;
    public $nombre;
    public $email;
    public $password;
    public $rol_id;
    public $estado;
    public $fecha_creacion;
    public $ultima_sesion;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->validator = new Validator();
    }
    
    /**
     * Crea un nuevo usuario
     * 
     * @return int|false ID del usuario creado o false
     */
    public function crear() {
        // Validaciones
        if (!$this->validarDatos()) {
            return false;
        }
        
        try {
            $query = "INSERT INTO usuarios (nombre, email, password, rol_id, estado) 
                     VALUES (:nombre, :email, :password, :rol_id, :estado)";
            
            $stmt = $this->db->prepare($query);
            
            // Hash de la contraseña
            $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
            
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':rol_id', $this->rol_id);
            $stmt->bindParam(':estado', $this->estado);
            
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            throw new Exception("Error al crear usuario: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene un usuario por ID
     * 
     * @param int $id
     * @return array|false
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT u.*, r.nombre as rol_nombre 
                     FROM usuarios u 
                     INNER JOIN roles r ON u.rol_id = r.id 
                     WHERE u.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene un usuario por email
     * 
     * @param string $email
     * @return array|false
     */
    public function obtenerPorEmail($email) {
        try {
            $query = "SELECT u.*, r.nombre as rol_nombre 
                     FROM usuarios u 
                     INNER JOIN roles r ON u.rol_id = r.id 
                     WHERE u.email = :email";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usuario: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene todos los usuarios
     * 
     * @param array $filtros Filtros opcionales
     * @return array
     */
    public function obtenerTodos($filtros = []) {
        try {
            $query = "SELECT u.*, r.nombre as rol_nombre 
                     FROM usuarios u 
                     INNER JOIN roles r ON u.rol_id = r.id 
                     WHERE 1=1";
            
            $params = [];
            
            // Aplicar filtros - CORREGIDO: verificar que no sea vacío
            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $query .= " AND u.estado = :estado";
                $params[':estado'] = (int)$filtros['estado'];
            }
            
            if (!empty($filtros['rol_id'])) {
                $query .= " AND u.rol_id = :rol_id";
                $params[':rol_id'] = (int)$filtros['rol_id'];
            }
            
            if (!empty($filtros['buscar'])) {
                $query .= " AND (u.nombre LIKE :buscar OR u.email LIKE :buscar2)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar2'] = '%' . $filtros['buscar'] . '%';
            }
            
            $query .= " ORDER BY u.fecha_creacion DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener usuarios: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza un usuario
     * 
     * @return bool
     */
    public function actualizar() {
        if (!$this->validarDatos(false)) {
            return false;
        }
        
        try {
            $query = "UPDATE usuarios 
                     SET nombre = :nombre, 
                         email = :email, 
                         rol_id = :rol_id, 
                         estado = :estado";
            
            // Si se proporciona nueva contraseña, actualizarla
            if (!empty($this->password)) {
                $query .= ", password = :password";
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':rol_id', $this->rol_id);
            $stmt->bindParam(':estado', $this->estado);
            $stmt->bindParam(':id', $this->id);
            
            if (!empty($this->password)) {
                $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);
                $stmt->bindParam(':password', $hashedPassword);
            }
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar usuario: " . $e->getMessage());
        }
    }
    
    /**
     * Desactiva un usuario (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function desactivar($id) {
        try {
            $query = "UPDATE usuarios SET estado = 0 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar usuario: " . $e->getMessage());
        }
    }
    
    /**
     * Activa un usuario
     * 
     * @param int $id
     * @return bool
     */
    public function activar($id) {
        try {
            $query = "UPDATE usuarios SET estado = 1 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al activar usuario: " . $e->getMessage());
        }
    }
    
    /**
     * Autentica un usuario
     * 
     * @param string $email
     * @param string $password
     * @return array|false Datos del usuario o false
     */
    public function autenticar($email, $password) {
        // Sanitizar datos
        $email = Validator::sanitizeEmail($email);
        
        // Obtener usuario
        $usuario = $this->obtenerPorEmail($email);
        
        if (!$usuario) {
            return false;
        }
        
        // Verificar estado
        if ($usuario['estado'] != 1) {
            return false;
        }
        
        // Verificar contraseña
        if (!password_verify($password, $usuario['password'])) {
            return false;
        }
        
        // Actualizar última sesión
        $this->actualizarUltimaSesion($usuario['id']);
        
        // No retornar la contraseña
        unset($usuario['password']);
        
        return $usuario;
    }
    
    /**
     * Actualiza la última sesión del usuario
     * 
     * @param int $id
     * @return bool
     */
    private function actualizarUltimaSesion($id) {
        try {
            $query = "UPDATE usuarios SET ultima_sesion = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Verifica si un email ya existe
     * 
     * @param string $email
     * @param int|null $excludeId ID a excluir (para actualización)
     * @return bool
     */
    public function emailExiste($email, $excludeId = null) {
        try {
            $query = "SELECT COUNT(*) FROM usuarios WHERE email = :email";
            $params = [':email' => $email];
            
            if ($excludeId) {
                $query .= " AND id != :id";
                $params[':id'] = $excludeId;
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtiene permisos del usuario
     * 
     * @param int $rolId
     * @return array
     */
    public function obtenerPermisos($rolId) {
        try {
            $query = "SELECT modulo, puede_crear, puede_leer, puede_actualizar, puede_eliminar 
                     FROM permisos_rol 
                     WHERE rol_id = :rol_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':rol_id', $rolId, PDO::PARAM_INT);
            $stmt->execute();
            
            $permisos = [];
            while ($row = $stmt->fetch()) {
                $permisos[$row['modulo']] = [
                    'crear' => (bool)$row['puede_crear'],
                    'leer' => (bool)$row['puede_leer'],
                    'actualizar' => (bool)$row['puede_actualizar'],
                    'eliminar' => (bool)$row['puede_eliminar']
                ];
            }
            
            return $permisos;
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener permisos: " . $e->getMessage());
        }
    }
    
    /**
     * Valida los datos del usuario
     * 
     * @param bool $esNuevo
     * @return bool
     */
    private function validarDatos($esNuevo = true) {
        $this->validator->clearErrors();
        
        // Validar nombre
        $this->validator->required($this->nombre, 'nombre');
        $this->validator->minLength($this->nombre, 3, 'nombre');
        $this->validator->maxLength($this->nombre, 100, 'nombre');
        
        // Validar email
        $this->validator->required($this->email, 'email');
        $this->validator->email($this->email, 'email');
        
        // Verificar si el email ya existe
        if ($this->emailExiste($this->email, $this->id)) {
            $this->validator->getErrors()['email'] = 'El email ya está registrado';
        }
        
        // Validar contraseña (solo para usuarios nuevos o si se proporciona)
        if ($esNuevo || !empty($this->password)) {
            $this->validator->required($this->password, 'password');
            $this->validator->minLength($this->password, 6, 'password');
        }
        
        // Validar rol
        $this->validator->required($this->rol_id, 'rol');
        
        return !$this->validator->hasErrors();
    }
    
    /**
     * Obtiene los errores de validación
     * 
     * @return array
     */
    public function getErrors() {
        return $this->validator->getErrors();
    }
    
    /**
     * Cuenta total de usuarios
     * 
     * @param array $filtros
     * @return int
     */
    public function contarTodos($filtros = []) {
        try {
            $query = "SELECT COUNT(*) FROM usuarios WHERE 1=1";
            $params = [];
            
            // CORREGIDO: verificar que no sea vacío
            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $query .= " AND estado = :estado";
                $params[':estado'] = (int)$filtros['estado'];
            }
            
            if (!empty($filtros['rol_id'])) {
                $query .= " AND rol_id = :rol_id";
                $params[':rol_id'] = (int)$filtros['rol_id'];
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return (int)$stmt->fetchColumn();
            
        } catch (PDOException $e) {
            throw new Exception("Error al contar usuarios: " . $e->getMessage());
        }
    }
}
?>