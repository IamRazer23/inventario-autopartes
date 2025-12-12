<?php
/**
 * Clase Categoria
 * Modelo para gestión de categorías de autopartes
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../core/Validator.php';

class Categoria {
    
    private $db;
    private $validator;
    
    // Propiedades de la categoría
    public $id;
    public $nombre;
    public $descripcion;
    public $imagen;
    public $estado;
    public $fecha_creacion;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->validator = new Validator();
    }
    
    /**
     * Crea una nueva categoría
     * 
     * @return int|false ID de la categoría creada o false
     */
    public function crear() {
        // Validaciones
        if (!$this->validarDatos()) {
            return false;
        }
        
        try {
            $query = "INSERT INTO categorias (nombre, descripcion, imagen, estado) 
                     VALUES (:nombre, :descripcion, :imagen, :estado)";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':imagen', $this->imagen);
            $stmt->bindParam(':estado', $this->estado);
            
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            throw new Exception("Error al crear categoría: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene una categoría por ID
     * 
     * @param int $id
     * @return array|false
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT * FROM categorias WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener categoría: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene todas las categorías
     * 
     * @param array $filtros Filtros opcionales
     * @return array
     */
    public function obtenerTodas($filtros = []) {
        try {
            $query = "SELECT 
                c.*,
                COUNT(a.id) as total_autopartes
                FROM categorias c
                LEFT JOIN autopartes a ON c.id = a.categoria_id AND a.estado = 1
                WHERE 1=1";
            
            $params = [];
            
            // Aplicar filtros
            if (isset($filtros['estado'])) {
                $query .= " AND c.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (isset($filtros['buscar']) && !empty($filtros['buscar'])) {
                $query .= " AND (c.nombre LIKE :buscar OR c.descripcion LIKE :buscar)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
            }
            
            $query .= " GROUP BY c.id ORDER BY c.nombre ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener categorías: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene categorías activas (para selects)
     * 
     * @return array
     */
    public function obtenerActivas() {
        try {
            $query = "SELECT id, nombre FROM categorias WHERE estado = 1 ORDER BY nombre ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener categorías activas: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza una categoría
     * 
     * @return bool
     */
    public function actualizar() {
        if (!$this->validarDatos(false)) {
            return false;
        }
        
        try {
            $query = "UPDATE categorias 
                     SET nombre = :nombre, 
                         descripcion = :descripcion, 
                         imagen = :imagen,
                         estado = :estado
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':imagen', $this->imagen);
            $stmt->bindParam(':estado', $this->estado);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar categoría: " . $e->getMessage());
        }
    }
    
    /**
     * Elimina una categoría (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function eliminar($id) {
        try {
            // Verificar si tiene autopartes asociadas
            $queryCheck = "SELECT COUNT(*) as total FROM autopartes WHERE categoria_id = :id";
            $stmtCheck = $this->db->prepare($queryCheck);
            $stmtCheck->bindParam(':id', $id, PDO::PARAM_INT);
            $stmtCheck->execute();
            
            $result = $stmtCheck->fetch();
            
            if ($result['total'] > 0) {
                $this->validator->getErrors()['categoria'] = 
                    'No se puede eliminar la categoría porque tiene ' . $result['total'] . ' autopartes asociadas';
                return false;
            }
            
            // Eliminar categoría (soft delete)
            $query = "UPDATE categorias SET estado = 0 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar categoría: " . $e->getMessage());
        }
    }
    
    /**
     * Activa una categoría
     * 
     * @param int $id
     * @return bool
     */
    public function activar($id) {
        try {
            $query = "UPDATE categorias SET estado = 1 WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al activar categoría: " . $e->getMessage());
        }
    }
    
    /**
     * Verifica si un nombre de categoría ya existe
     * 
     * @param string $nombre
     * @param int|null $excludeId ID a excluir (para actualización)
     * @return bool
     */
    public function nombreExiste($nombre, $excludeId = null) {
        try {
            $query = "SELECT COUNT(*) as total FROM categorias WHERE nombre = :nombre";
            
            if ($excludeId) {
                $query .= " AND id != :id";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            
            if ($excludeId) {
                $stmt->bindParam(':id', $excludeId, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['total'] > 0;
            
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Cuenta total de categorías
     * 
     * @param array $filtros
     * @return int
     */
    public function contarTodas($filtros = []) {
        try {
            $query = "SELECT COUNT(*) as total FROM categorias WHERE 1=1";
            $params = [];
            
            if (isset($filtros['estado'])) {
                $query .= " AND estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (isset($filtros['buscar']) && !empty($filtros['buscar'])) {
                $query .= " AND (nombre LIKE :buscar OR descripcion LIKE :buscar)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            $result = $stmt->fetch();
            return $result['total'];
            
        } catch (PDOException $e) {
            throw new Exception("Error al contar categorías: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene estadísticas de una categoría
     * 
     * @param int $id
     * @return array
     */
    public function obtenerEstadisticas($id) {
        try {
            $query = "SELECT 
                COUNT(a.id) as total_autopartes,
                SUM(CASE WHEN a.estado = 1 THEN 1 ELSE 0 END) as autopartes_activas,
                SUM(CASE WHEN a.stock > 0 THEN 1 ELSE 0 END) as con_stock,
                SUM(CASE WHEN a.stock = 0 THEN 1 ELSE 0 END) as sin_stock,
                COALESCE(SUM(a.stock), 0) as stock_total
                FROM autopartes a
                WHERE a.categoria_id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene las categorías más populares (con más autopartes)
     * 
     * @param int $limit
     * @return array
     */
    public function obtenerMasPopulares($limit = 5) {
        try {
            $query = "SELECT 
                c.id, c.nombre, c.descripcion, c.imagen,
                COUNT(a.id) as total_autopartes
                FROM categorias c
                LEFT JOIN autopartes a ON c.id = a.categoria_id AND a.estado = 1
                WHERE c.estado = 1
                GROUP BY c.id
                HAVING total_autopartes > 0
                ORDER BY total_autopartes DESC
                LIMIT :limit";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener categorías populares: " . $e->getMessage());
        }
    }
    
    /**
     * Valida los datos de la categoría
     * 
     * @param bool $esNueva
     * @return bool
     */
    private function validarDatos($esNueva = true) {
        $this->validator->clearErrors();
        
        // Validar nombre
        $this->validator->required($this->nombre, 'nombre');
        $this->validator->minLength($this->nombre, 3, 'nombre');
        $this->validator->maxLength($this->nombre, 100, 'nombre');
        
        // Verificar si el nombre ya existe
        if ($this->nombreExiste($this->nombre, $this->id)) {
            $this->validator->getErrors()['nombre'] = 'El nombre de la categoría ya existe';
        }
        
        // Validar descripción (opcional pero con límite)
        if (!empty($this->descripcion)) {
            $this->validator->maxLength($this->descripcion, 500, 'descripcion');
        }
        
        // Validar estado
        if ($this->estado === null || $this->estado === '') {
            $this->estado = 1; // Por defecto activo
        }
        
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
     * Guarda una imagen de categoría
     * 
     * @param array $file Archivo $_FILES
     * @return string|false Nombre del archivo guardado o false
     */
    public function guardarImagen($file) {
        try {
            // Validar imagen
            if (!$this->validator->validateImage($file, 2097152)) { // 2MB max
                return false;
            }
            
            // Generar nombre único
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $nombreArchivo = 'cat_' . uniqid() . '_' . time() . '.' . $extension;
            
            // Ruta de destino
            $rutaDestino = UPLOADS_PATH . '/categories/' . $nombreArchivo;
            
            // Crear directorio si no existe
            $dirCategories = UPLOADS_PATH . '/categories';
            if (!file_exists($dirCategories)) {
                mkdir($dirCategories, 0755, true);
            }
            
            // Mover archivo
            if (move_uploaded_file($file['tmp_name'], $rutaDestino)) {
                return $nombreArchivo;
            }
            
            return false;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Elimina una imagen de categoría
     * 
     * @param string $nombreArchivo
     * @return bool
     */
    public function eliminarImagen($nombreArchivo) {
        try {
            if (empty($nombreArchivo)) {
                return true;
            }
            
            $rutaArchivo = UPLOADS_PATH . '/categories/' . $nombreArchivo;
            
            if (file_exists($rutaArchivo)) {
                return unlink($rutaArchivo);
            }
            
            return true;
            
        } catch (Exception $e) {
            return false;
        }
    }
}
?>