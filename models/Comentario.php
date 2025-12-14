<?php
/**
 * Modelo Comentario
 * Gestión de comentarios de autopartes
 */

require_once __DIR__ . '/../config/Database.php';

class Comentario {
    
    private $db;
    
    // Propiedades
    public $id;
    public $autoparte_id;
    public $usuario_id;
    public $contenido;
    public $calificacion;
    public $respuesta_admin;
    public $estado;
    public $fecha_creacion;
    public $fecha_actualizacion;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Obtiene un comentario por ID
     * 
     * @param int $id
     * @return array|false
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT c.*, 
                        a.nombre as autoparte_nombre,
                        a.thumbnail as autoparte_imagen,
                        u.nombre as usuario_nombre,
                        u.email as usuario_email
                     FROM comentarios c
                     LEFT JOIN autopartes a ON c.autoparte_id = a.id
                     LEFT JOIN usuarios u ON c.usuario_id = u.id
                     WHERE c.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener comentario: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene todos los comentarios con filtros
     * 
     * @param array $filtros
     * @return array
     */
    public function obtenerTodos($filtros = []) {
        try {
            $query = "SELECT c.*, 
                        a.nombre as autoparte_nombre,
                        u.nombre as usuario_nombre,
                        u.email as usuario_email
                     FROM comentarios c
                     LEFT JOIN autopartes a ON c.autoparte_id = a.id
                     LEFT JOIN usuarios u ON c.usuario_id = u.id
                     WHERE 1=1";
            
            $params = [];
            
            if (isset($filtros['autoparte_id']) && $filtros['autoparte_id'] !== '') {
                $query .= " AND c.autoparte_id = :autoparte_id";
                $params[':autoparte_id'] = $filtros['autoparte_id'];
            }
            
            if (isset($filtros['usuario_id']) && $filtros['usuario_id'] !== '') {
                $query .= " AND c.usuario_id = :usuario_id";
                $params[':usuario_id'] = $filtros['usuario_id'];
            }
            
            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $query .= " AND c.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (isset($filtros['buscar']) && !empty($filtros['buscar'])) {
                $query .= " AND (c.contenido LIKE :buscar OR u.nombre LIKE :buscar2)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar2'] = '%' . $filtros['buscar'] . '%';
            }
            
            $query .= " ORDER BY c.fecha_creacion DESC";
            
            if (isset($filtros['limite'])) {
                $query .= " LIMIT :limite";
                if (isset($filtros['offset'])) {
                    $query .= " OFFSET :offset";
                }
            }
            
            $stmt = $this->db->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            if (isset($filtros['limite'])) {
                $stmt->bindValue(':limite', (int)$filtros['limite'], PDO::PARAM_INT);
                if (isset($filtros['offset'])) {
                    $stmt->bindValue(':offset', (int)$filtros['offset'], PDO::PARAM_INT);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener comentarios: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene comentarios de una autoparte específica
     * 
     * @param int $autoparteId
     * @param bool $soloActivos
     * @return array
     */
    public function obtenerPorAutoparte($autoparteId, $soloActivos = true) {
        try {
            $query = "SELECT c.*, u.nombre as usuario_nombre
                     FROM comentarios c
                     LEFT JOIN usuarios u ON c.usuario_id = u.id
                     WHERE c.autoparte_id = :autoparte_id";
            
            if ($soloActivos) {
                $query .= " AND c.estado = 1";
            }
            
            $query .= " ORDER BY c.fecha_creacion DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':autoparte_id', $autoparteId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener comentarios: " . $e->getMessage());
        }
    }
    
    /**
     * Cuenta el total de comentarios
     * 
     * @param array $filtros
     * @return int
     */
    public function contarTodos($filtros = []) {
        try {
            $query = "SELECT COUNT(*) as total FROM comentarios c WHERE 1=1";
            
            $params = [];
            
            if (isset($filtros['autoparte_id']) && $filtros['autoparte_id'] !== '') {
                $query .= " AND c.autoparte_id = :autoparte_id";
                $params[':autoparte_id'] = $filtros['autoparte_id'];
            }
            
            if (isset($filtros['estado']) && $filtros['estado'] !== '') {
                $query .= " AND c.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            $stmt = $this->db->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $resultado = $stmt->fetch();
            
            return $resultado['total'] ?? 0;
            
        } catch (PDOException $e) {
            throw new Exception("Error al contar comentarios: " . $e->getMessage());
        }
    }
    
    /**
     * Crea un nuevo comentario
     * 
     * @return int|false
     */
    public function crear() {
        try {
            $query = "INSERT INTO comentarios (
                        autoparte_id, usuario_id, contenido, calificacion, estado
                    ) VALUES (
                        :autoparte_id, :usuario_id, :contenido, :calificacion, :estado
                    )";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':autoparte_id', $this->autoparte_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $this->usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':contenido', $this->contenido);
            $stmt->bindParam(':calificacion', $this->calificacion, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            throw new Exception("Error al crear comentario: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza un comentario
     * 
     * @return bool
     */
    public function actualizar() {
        try {
            $query = "UPDATE comentarios SET 
                        contenido = :contenido,
                        calificacion = :calificacion,
                        respuesta_admin = :respuesta_admin,
                        estado = :estado,
                        fecha_actualizacion = NOW()
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':contenido', $this->contenido);
            $stmt->bindParam(':calificacion', $this->calificacion, PDO::PARAM_INT);
            $stmt->bindParam(':respuesta_admin', $this->respuesta_admin);
            $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar comentario: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza el estado de un comentario
     * 
     * @param int $id
     * @param int $estado
     * @return bool
     */
    public function actualizarEstado($id, $estado) {
        try {
            $query = "UPDATE comentarios SET 
                        estado = :estado,
                        fecha_actualizacion = NOW()
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar estado: " . $e->getMessage());
        }
    }
    
    /**
     * Agrega respuesta del administrador
     * 
     * @param int $id
     * @param string $respuesta
     * @return bool
     */
    public function agregarRespuesta($id, $respuesta) {
        try {
            $query = "UPDATE comentarios SET 
                        respuesta_admin = :respuesta,
                        fecha_actualizacion = NOW()
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':respuesta', $respuesta);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al agregar respuesta: " . $e->getMessage());
        }
    }
    
    /**
     * Elimina un comentario
     * 
     * @param int $id
     * @return bool
     */
    public function eliminar($id) {
        try {
            $query = "DELETE FROM comentarios WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar comentario: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene el promedio de calificación de una autoparte
     * 
     * @param int $autoparteId
     * @return float
     */
    public function obtenerPromedioCalificacion($autoparteId) {
        try {
            $query = "SELECT AVG(calificacion) as promedio 
                     FROM comentarios 
                     WHERE autoparte_id = :autoparte_id AND estado = 1 AND calificacion > 0";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':autoparte_id', $autoparteId, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch();
            
            return round($resultado['promedio'] ?? 0, 1);
            
        } catch (PDOException $e) {
            return 0;
        }
    }
}
