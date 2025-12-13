<?php
/**
 * Clase Autoparte
 * Modelo para gestión del inventario de autopartes
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../core/Validator.php';

class Autoparte {
    
    private $db;
    private $validator;
    
    // Propiedades de la autoparte
    public $id;
    public $nombre;
    public $descripcion;
    public $marca;
    public $modelo;
    public $anio;
    public $precio;
    public $stock;
    public $categoria_id;
    public $thumbnail;
    public $imagen_grande;
    public $estado;
    public $fecha_creacion;
    public $fecha_actualizacion;
    public $usuario_id;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->validator = new Validator();
    }
    
    /**
     * Crea una nueva autoparte
     * 
     * @return int|false ID de la autoparte creada o false
     */
    public function crear() {
        // Validaciones
        if (!$this->validarDatos()) {
            return false;
        }
        
        try {
            $query = "INSERT INTO autopartes (
                        nombre, descripcion, marca, modelo, anio, 
                        precio, stock, categoria_id,
                        thumbnail, imagen_grande, estado, usuario_id
                    ) VALUES (
                        :nombre, :descripcion, :marca, :modelo, :anio,
                        :precio, :stock, :categoria_id,
                        :thumbnail, :imagen_grande, :estado, :usuario_id
                    )";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':marca', $this->marca);
            $stmt->bindParam(':modelo', $this->modelo);
            $stmt->bindParam(':anio', $this->anio, PDO::PARAM_INT);
            $stmt->bindParam(':precio', $this->precio);
            $stmt->bindParam(':stock', $this->stock, PDO::PARAM_INT);
            $stmt->bindParam(':categoria_id', $this->categoria_id, PDO::PARAM_INT);
            $stmt->bindParam(':thumbnail', $this->thumbnail);
            $stmt->bindParam(':imagen_grande', $this->imagen_grande);
            $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $this->usuario_id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            
            return false;
            
        } catch (PDOException $e) {
            throw new Exception("Error al crear autoparte: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene una autoparte por ID
     * 
     * @param int $id
     * @return array|false
     */
    public function obtenerPorId($id) {
        try {
            $query = "SELECT a.*, 
                            c.nombre as categoria_nombre,
                            u.nombre as usuario_nombre
                     FROM autopartes a 
                     LEFT JOIN categorias c ON a.categoria_id = c.id 
                     LEFT JOIN usuarios u ON a.usuario_id = u.id
                     WHERE a.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener autoparte: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene todas las autopartes con filtros
     * 
     * @param array $filtros Filtros opcionales
     * @return array
     */
    public function obtenerTodos($filtros = []) {
        try {
            $query = "SELECT a.*, 
                            c.nombre as categoria_nombre
                     FROM autopartes a 
                     LEFT JOIN categorias c ON a.categoria_id = c.id 
                     WHERE 1=1";
            
            $params = [];
            
            // Aplicar filtros
            if (isset($filtros['estado'])) {
                $query .= " AND a.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (isset($filtros['categoria_id']) && $filtros['categoria_id'] !== '') {
                $query .= " AND a.categoria_id = :categoria_id";
                $params[':categoria_id'] = $filtros['categoria_id'];
            }
            
            if (isset($filtros['marca']) && $filtros['marca'] !== '') {
                $query .= " AND a.marca LIKE :marca";
                $params[':marca'] = '%' . $filtros['marca'] . '%';
            }
            
            if (isset($filtros['modelo']) && $filtros['modelo'] !== '') {
                $query .= " AND a.modelo LIKE :modelo";
                $params[':modelo'] = '%' . $filtros['modelo'] . '%';
            }
            
            if (isset($filtros['anio']) && $filtros['anio'] !== '') {
                $query .= " AND a.anio = :anio";
                $params[':anio'] = $filtros['anio'];
            }
            
            if (isset($filtros['precio_min']) && $filtros['precio_min'] !== '') {
                $query .= " AND a.precio >= :precio_min";
                $params[':precio_min'] = $filtros['precio_min'];
            }
            
            if (isset($filtros['precio_max']) && $filtros['precio_max'] !== '') {
                $query .= " AND a.precio <= :precio_max";
                $params[':precio_max'] = $filtros['precio_max'];
            }
            
            if (isset($filtros['buscar']) && $filtros['buscar'] !== '') {
                $query .= " AND (a.nombre LIKE :buscar OR a.descripcion LIKE :buscar2 OR a.marca LIKE :buscar3 OR a.modelo LIKE :buscar4)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar2'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar3'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar4'] = '%' . $filtros['buscar'] . '%';
            }
            
            if (isset($filtros['stock_bajo']) && $filtros['stock_bajo']) {
                $query .= " AND a.stock <= 5";
            }
            
            // Ordenamiento
            $ordenValidos = ['nombre', 'precio', 'stock', 'fecha_creacion', 'marca', 'anio'];
            $orden = isset($filtros['orden']) && in_array($filtros['orden'], $ordenValidos) 
                    ? $filtros['orden'] : 'fecha_creacion';
            
            $direccion = isset($filtros['direccion']) && strtoupper($filtros['direccion']) === 'ASC' 
                        ? 'ASC' : 'DESC';
            
            $query .= " ORDER BY a.$orden $direccion";
            
            // Paginación
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
            throw new Exception("Error al obtener autopartes: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene autopartes para la página pública (catálogo)
     * 
     * @param array $filtros
     * @return array
     */
    public function obtenerParaCatalogo($filtros = []) {
        // Solo mostrar autopartes activas con stock
        $filtros['estado'] = 1;
        
        if (!isset($filtros['solo_disponibles']) || $filtros['solo_disponibles']) {
            // Agregar filtro de stock > 0
        }
        
        return $this->obtenerTodos($filtros);
    }
    
    /**
     * Actualiza una autoparte
     * 
     * @return bool
     */
    public function actualizar() {
        if (!$this->validarDatos(false)) {
            return false;
        }
        
        try {
            $query = "UPDATE autopartes SET
                        nombre = :nombre,
                        descripcion = :descripcion,
                        marca = :marca,
                        modelo = :modelo,
                        anio = :anio,
                        precio = :precio,
                        stock = :stock,
                        categoria_id = :categoria_id,
                        estado = :estado,
                        fecha_actualizacion = CURRENT_TIMESTAMP";
            
            // Si hay nuevas imágenes, actualizarlas
            if (!empty($this->thumbnail)) {
                $query .= ", thumbnail = :thumbnail";
            }
            if (!empty($this->imagen_grande)) {
                $query .= ", imagen_grande = :imagen_grande";
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':marca', $this->marca);
            $stmt->bindParam(':modelo', $this->modelo);
            $stmt->bindParam(':anio', $this->anio, PDO::PARAM_INT);
            $stmt->bindParam(':precio', $this->precio);
            $stmt->bindParam(':stock', $this->stock, PDO::PARAM_INT);
            $stmt->bindParam(':categoria_id', $this->categoria_id, PDO::PARAM_INT);
            $stmt->bindParam(':estado', $this->estado, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            
            if (!empty($this->thumbnail)) {
                $stmt->bindParam(':thumbnail', $this->thumbnail);
            }
            if (!empty($this->imagen_grande)) {
                $stmt->bindParam(':imagen_grande', $this->imagen_grande);
            }
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar autoparte: " . $e->getMessage());
        }
    }
    
    /**
     * Desactiva una autoparte (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function desactivar($id) {
        try {
            $query = "UPDATE autopartes SET estado = 0, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al desactivar autoparte: " . $e->getMessage());
        }
    }
    
    /**
     * Activa una autoparte
     * 
     * @param int $id
     * @return bool
     */
    public function activar($id) {
        try {
            $query = "UPDATE autopartes SET estado = 1, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al activar autoparte: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza el stock de una autoparte
     * 
     * @param int $id
     * @param int $cantidad Cantidad a agregar (positivo) o restar (negativo)
     * @return bool
     */
    public function actualizarStock($id, $cantidad) {
        try {
            // Primero verificar stock actual
            $autoparte = $this->obtenerPorId($id);
            
            if (!$autoparte) {
                return false;
            }
            
            $nuevoStock = $autoparte['stock'] + $cantidad;
            
            // No permitir stock negativo
            if ($nuevoStock < 0) {
                return false;
            }
            
            $query = "UPDATE autopartes SET stock = :stock, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':stock', $nuevoStock, PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar stock: " . $e->getMessage());
        }
    }
    
    /**
     * Registra una venta (mueve a tabla vendido_parte)
     * Cumple con requisito 4: Disminuir inventario enviando a tabla VENDIDO_PARTE
     * 
     * @param int $autoparteId
     * @param int $cantidad
     * @param float $precioVenta
     * @param int $usuarioId
     * @return int|false ID de la venta o false
     */
    public function registrarVenta($autoparteId, $cantidad, $precioVenta, $usuarioId) {
        try {
            $this->db->beginTransaction();
            
            // Verificar stock disponible
            $autoparte = $this->obtenerPorId($autoparteId);
            
            if (!$autoparte || $autoparte['stock'] < $cantidad) {
                $this->db->rollBack();
                return false;
            }
            
            // Registrar en vendido_parte
            $query = "INSERT INTO vendido_parte (autoparte_id, cantidad, precio_unitario, precio_total, usuario_id)
                     VALUES (:autoparte_id, :cantidad, :precio_unitario, :precio_total, :usuario_id)";
            
            $stmt = $this->db->prepare($query);
            
            $precioTotal = $precioVenta * $cantidad;
            
            $stmt->bindParam(':autoparte_id', $autoparteId, PDO::PARAM_INT);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':precio_unitario', $precioVenta);
            $stmt->bindParam(':precio_total', $precioTotal);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            
            if (!$stmt->execute()) {
                $this->db->rollBack();
                return false;
            }
            
            $ventaId = $this->db->lastInsertId();
            
            // Actualizar stock
            if (!$this->actualizarStock($autoparteId, -$cantidad)) {
                $this->db->rollBack();
                return false;
            }
            
            $this->db->commit();
            return $ventaId;
            
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw new Exception("Error al registrar venta: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene marcas únicas para filtros
     * 
     * @return array
     */
    public function obtenerMarcas() {
        try {
            $query = "SELECT DISTINCT marca FROM autopartes WHERE estado = 1 AND marca != '' ORDER BY marca";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener marcas: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene modelos únicos para filtros
     * 
     * @param string $marca Filtrar por marca opcional
     * @return array
     */
    public function obtenerModelos($marca = null) {
        try {
            $query = "SELECT DISTINCT modelo FROM autopartes WHERE estado = 1 AND modelo != ''";
            $params = [];
            
            if ($marca) {
                $query .= " AND marca = :marca";
                $params[':marca'] = $marca;
            }
            
            $query .= " ORDER BY modelo";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener modelos: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene años únicos para filtros
     * 
     * @return array
     */
    public function obtenerAnios() {
        try {
            $query = "SELECT DISTINCT anio FROM autopartes WHERE estado = 1 ORDER BY anio DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener años: " . $e->getMessage());
        }
    }
    
    /**
     * Cuenta total de autopartes
     * 
     * @param array $filtros
     * @return int
     */
    public function contarTodos($filtros = []) {
        try {
            $query = "SELECT COUNT(*) FROM autopartes WHERE 1=1";
            $params = [];
            
            if (isset($filtros['estado'])) {
                $query .= " AND estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (isset($filtros['categoria_id']) && $filtros['categoria_id'] !== '') {
                $query .= " AND categoria_id = :categoria_id";
                $params[':categoria_id'] = $filtros['categoria_id'];
            }
            
            if (isset($filtros['marca']) && $filtros['marca'] !== '') {
                $query .= " AND marca LIKE :marca";
                $params[':marca'] = '%' . $filtros['marca'] . '%';
            }
            
            if (isset($filtros['modelo']) && $filtros['modelo'] !== '') {
                $query .= " AND modelo LIKE :modelo";
                $params[':modelo'] = '%' . $filtros['modelo'] . '%';
            }
            
            if (isset($filtros['anio']) && $filtros['anio'] !== '') {
                $query .= " AND anio = :anio";
                $params[':anio'] = $filtros['anio'];
            }
            
            if (isset($filtros['precio_min']) && $filtros['precio_min'] !== '') {
                $query .= " AND precio >= :precio_min";
                $params[':precio_min'] = $filtros['precio_min'];
            }
            
            if (isset($filtros['precio_max']) && $filtros['precio_max'] !== '') {
                $query .= " AND precio <= :precio_max";
                $params[':precio_max'] = $filtros['precio_max'];
            }
            
            if (isset($filtros['buscar']) && $filtros['buscar'] !== '') {
                $query .= " AND (nombre LIKE :buscar OR descripcion LIKE :buscar2 OR marca LIKE :buscar3 OR modelo LIKE :buscar4)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar2'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar3'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar4'] = '%' . $filtros['buscar'] . '%';
            }
            
            if (isset($filtros['stock_bajo']) && $filtros['stock_bajo']) {
                $query .= " AND stock <= 5";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return $stmt->fetchColumn();
            
        } catch (PDOException $e) {
            throw new Exception("Error al contar autopartes: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene valor total del inventario
     * 
     * @return float
     */
    public function obtenerValorInventario() {
        try {
            $query = "SELECT SUM(precio * stock) as valor_total FROM autopartes WHERE estado = 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result['valor_total'] ?? 0;
            
        } catch (PDOException $e) {
            throw new Exception("Error al calcular valor inventario: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene autopartes con stock bajo
     * 
     * @param int $limite Cantidad mínima para considerar stock bajo
     * @return array
     */
    public function obtenerStockBajo($limite = 5) {
        try {
            $query = "SELECT a.*, c.nombre as categoria_nombre 
                     FROM autopartes a
                     LEFT JOIN categorias c ON a.categoria_id = c.id
                     WHERE a.estado = 1 AND a.stock <= :limite
                     ORDER BY a.stock ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener stock bajo: " . $e->getMessage());
        }
    }
    
    /**
     * Valida los datos de la autoparte
     * 
     * @param bool $esNuevo
     * @return bool
     */
    private function validarDatos($esNuevo = true) {
        $this->validator->clearErrors();
        
        // Validar nombre
        $this->validator->required($this->nombre, 'nombre');
        $this->validator->minLength($this->nombre, 3, 'nombre');
        $this->validator->maxLength($this->nombre, 150, 'nombre');
        
        // Validar marca
        $this->validator->required($this->marca, 'marca');
        $this->validator->maxLength($this->marca, 50, 'marca');
        
        // Validar modelo
        $this->validator->required($this->modelo, 'modelo');
        $this->validator->maxLength($this->modelo, 50, 'modelo');
        
        // Validar año
        $this->validator->required($this->anio, 'anio');
        $this->validator->validYear($this->anio, 'anio');
        
        // Validar precio
        $this->validator->required($this->precio, 'precio');
        $this->validator->numeric($this->precio, 'precio');
        if ($this->precio < 0) {
            $this->validator->getErrors()['precio'] = 'El precio no puede ser negativo';
        }
        
        // Validar stock
        $this->validator->required($this->stock, 'stock');
        $this->validator->numeric($this->stock, 'stock');
        if ($this->stock < 0) {
            $this->validator->getErrors()['stock'] = 'El stock no puede ser negativo';
        }
        
        // Validar categoría
        $this->validator->required($this->categoria_id, 'categoria');
        
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
}
?>