<?php
/**
 * Clase Carrito
 * Modelo para gestión del carrito de compras
 */

require_once __DIR__ . '/../config/Database.php';

class Carrito {
    
    private $db;
    
    // Propiedades del carrito
    public $id;
    public $usuario_id;
    public $autoparte_id;
    public $cantidad;
    public $fecha_agregado;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Agrega un producto al carrito
     * Si ya existe, aumenta la cantidad
     * 
     * @param int $usuarioId
     * @param int $autoparteId
     * @param int $cantidad
     * @return bool
     */
    public function agregar($usuarioId, $autoparteId, $cantidad = 1) {
        try {
            // Verificar stock disponible
            $queryStock = "SELECT stock, nombre FROM autopartes WHERE id = :autoparte_id AND estado = 1";
            $stmtStock = $this->db->prepare($queryStock);
            $stmtStock->bindParam(':autoparte_id', $autoparteId, PDO::PARAM_INT);
            $stmtStock->execute();
            $autoparte = $stmtStock->fetch();
            
            if (!$autoparte) {
                throw new Exception("El producto no existe o no está disponible");
            }
            
            // Verificar si ya está en el carrito
            $queryExiste = "SELECT id, cantidad FROM carrito WHERE usuario_id = :usuario_id AND autoparte_id = :autoparte_id";
            $stmtExiste = $this->db->prepare($queryExiste);
            $stmtExiste->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmtExiste->bindParam(':autoparte_id', $autoparteId, PDO::PARAM_INT);
            $stmtExiste->execute();
            $itemExistente = $stmtExiste->fetch();
            
            if ($itemExistente) {
                // Actualizar cantidad
                $nuevaCantidad = $itemExistente['cantidad'] + $cantidad;
                
                // Verificar que no exceda el stock
                if ($nuevaCantidad > $autoparte['stock']) {
                    throw new Exception("No hay suficiente stock. Disponible: " . $autoparte['stock']);
                }
                
                $queryUpdate = "UPDATE carrito SET cantidad = :cantidad, fecha_agregado = CURRENT_TIMESTAMP 
                               WHERE id = :id";
                $stmtUpdate = $this->db->prepare($queryUpdate);
                $stmtUpdate->bindParam(':cantidad', $nuevaCantidad, PDO::PARAM_INT);
                $stmtUpdate->bindParam(':id', $itemExistente['id'], PDO::PARAM_INT);
                
                return $stmtUpdate->execute();
            } else {
                // Verificar stock para nuevo item
                if ($cantidad > $autoparte['stock']) {
                    throw new Exception("No hay suficiente stock. Disponible: " . $autoparte['stock']);
                }
                
                // Insertar nuevo item
                $queryInsert = "INSERT INTO carrito (usuario_id, autoparte_id, cantidad) 
                               VALUES (:usuario_id, :autoparte_id, :cantidad)";
                $stmtInsert = $this->db->prepare($queryInsert);
                $stmtInsert->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
                $stmtInsert->bindParam(':autoparte_id', $autoparteId, PDO::PARAM_INT);
                $stmtInsert->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                
                return $stmtInsert->execute();
            }
            
        } catch (PDOException $e) {
            throw new Exception("Error al agregar al carrito: " . $e->getMessage());
        }
    }
    
    /**
     * Actualiza la cantidad de un item en el carrito
     * 
     * @param int $usuarioId
     * @param int $autoparteId
     * @param int $cantidad
     * @return bool
     */
    public function actualizarCantidad($usuarioId, $autoparteId, $cantidad) {
        try {
            if ($cantidad <= 0) {
                return $this->eliminar($usuarioId, $autoparteId);
            }
            
            // Verificar stock
            $queryStock = "SELECT stock FROM autopartes WHERE id = :autoparte_id";
            $stmtStock = $this->db->prepare($queryStock);
            $stmtStock->bindParam(':autoparte_id', $autoparteId, PDO::PARAM_INT);
            $stmtStock->execute();
            $autoparte = $stmtStock->fetch();
            
            if ($cantidad > $autoparte['stock']) {
                throw new Exception("No hay suficiente stock. Disponible: " . $autoparte['stock']);
            }
            
            $query = "UPDATE carrito SET cantidad = :cantidad 
                     WHERE usuario_id = :usuario_id AND autoparte_id = :autoparte_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':autoparte_id', $autoparteId, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al actualizar cantidad: " . $e->getMessage());
        }
    }
    
    /**
     * Elimina un item del carrito
     * 
     * @param int $usuarioId
     * @param int $autoparteId
     * @return bool
     */
    public function eliminar($usuarioId, $autoparteId) {
        try {
            $query = "DELETE FROM carrito WHERE usuario_id = :usuario_id AND autoparte_id = :autoparte_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':autoparte_id', $autoparteId, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al eliminar del carrito: " . $e->getMessage());
        }
    }
    
    /**
     * Vacía el carrito completo de un usuario
     * 
     * @param int $usuarioId
     * @return bool
     */
    public function vaciar($usuarioId) {
        try {
            $query = "DELETE FROM carrito WHERE usuario_id = :usuario_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            throw new Exception("Error al vaciar el carrito: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene todos los items del carrito con información de autopartes
     * 
     * @param int $usuarioId
     * @return array
     */
    public function obtenerCarrito($usuarioId) {
        try {
            $query = "SELECT 
                        c.id,
                        c.autoparte_id,
                        c.cantidad,
                        c.fecha_agregado,
                        a.nombre,
                        a.marca,
                        a.modelo,
                        a.anio,
                        a.precio,
                        a.stock,
                        a.thumbnail as imagen_thumb,
                        a.imagen_grande,
                        a.estado,
                        cat.nombre as categoria_nombre,
                        (c.cantidad * a.precio) as subtotal
                     FROM carrito c
                     INNER JOIN autopartes a ON c.autoparte_id = a.id
                     LEFT JOIN categorias cat ON a.categoria_id = cat.id
                     WHERE c.usuario_id = :usuario_id
                     ORDER BY c.fecha_agregado DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener carrito: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene el total de items en el carrito
     * 
     * @param int $usuarioId
     * @return int
     */
    public function contarItems($usuarioId) {
        try {
            $query = "SELECT COALESCE(SUM(cantidad), 0) as total FROM carrito WHERE usuario_id = :usuario_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return (int)$result['total'];
            
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    /**
     * Calcula los totales del carrito
     * 
     * @param int $usuarioId
     * @return array
     */
    public function calcularTotales($usuarioId) {
        try {
            $query = "SELECT 
                        COALESCE(SUM(c.cantidad * a.precio), 0) as subtotal,
                        COUNT(c.id) as total_items,
                        COALESCE(SUM(c.cantidad), 0) as total_productos
                     FROM carrito c
                     INNER JOIN autopartes a ON c.autoparte_id = a.id
                     WHERE c.usuario_id = :usuario_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            $subtotal = (float)$result['subtotal'];
            $itbms = $subtotal * ITBMS_RATE; // 7% ITBMS
            $total = $subtotal + $itbms;
            
            return [
                'subtotal' => $subtotal,
                'itbms' => $itbms,
                'total' => $total,
                'total_items' => (int)$result['total_items'],
                'total_productos' => (int)$result['total_productos']
            ];
            
        } catch (PDOException $e) {
            return [
                'subtotal' => 0,
                'itbms' => 0,
                'total' => 0,
                'total_items' => 0,
                'total_productos' => 0
            ];
        }
    }
    
    /**
     * Verifica disponibilidad de stock para todos los items del carrito
     * 
     * @param int $usuarioId
     * @return array Items sin stock suficiente
     */
    public function verificarStock($usuarioId) {
        try {
            $query = "SELECT 
                        c.autoparte_id,
                        c.cantidad as cantidad_carrito,
                        a.nombre,
                        a.stock as stock_disponible
                     FROM carrito c
                     INNER JOIN autopartes a ON c.autoparte_id = a.id
                     WHERE c.usuario_id = :usuario_id AND c.cantidad > a.stock";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al verificar stock: " . $e->getMessage());
        }
    }
    
    /**
     * Procesa la compra del carrito
     * Crea la venta, el detalle y actualiza el stock
     * 
     * @param int $usuarioId
     * @return int|false ID de la venta o false
     */
    public function procesarCompra($usuarioId) {
        try {
            $this->db->beginTransaction();
            
            // Obtener items del carrito
            $items = $this->obtenerCarrito($usuarioId);
            
            if (empty($items)) {
                throw new Exception("El carrito está vacío");
            }
            
            // Verificar stock
            $sinStock = $this->verificarStock($usuarioId);
            if (!empty($sinStock)) {
                $nombres = array_column($sinStock, 'nombre');
                throw new Exception("Stock insuficiente para: " . implode(', ', $nombres));
            }
            
            // Calcular totales
            $totales = $this->calcularTotales($usuarioId);
            
            // Crear la venta
            $queryVenta = "INSERT INTO ventas (usuario_id, subtotal, itbms, total, estado) 
                          VALUES (:usuario_id, :subtotal, :itbms, :total, 'completada')";
            $stmtVenta = $this->db->prepare($queryVenta);
            $stmtVenta->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmtVenta->bindParam(':subtotal', $totales['subtotal']);
            $stmtVenta->bindParam(':itbms', $totales['itbms']);
            $stmtVenta->bindParam(':total', $totales['total']);
            $stmtVenta->execute();
            
            $ventaId = $this->db->lastInsertId();
            
            // Crear detalle de venta (el trigger actualiza el stock y vendido_parte)
            $queryDetalle = "INSERT INTO detalle_venta (venta_id, autoparte_id, cantidad, precio_unitario, subtotal) 
                            VALUES (:venta_id, :autoparte_id, :cantidad, :precio, :subtotal)";
            $stmtDetalle = $this->db->prepare($queryDetalle);
            
            foreach ($items as $item) {
                $subtotalItem = $item['cantidad'] * $item['precio'];
                $stmtDetalle->bindParam(':venta_id', $ventaId, PDO::PARAM_INT);
                $stmtDetalle->bindParam(':autoparte_id', $item['autoparte_id'], PDO::PARAM_INT);
                $stmtDetalle->bindParam(':cantidad', $item['cantidad'], PDO::PARAM_INT);
                $stmtDetalle->bindParam(':precio', $item['precio']);
                $stmtDetalle->bindParam(':subtotal', $subtotalItem);
                $stmtDetalle->execute();
            }
            
            // Vaciar el carrito
            $this->vaciar($usuarioId);
            
            $this->db->commit();
            
            return $ventaId;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Error al procesar la compra: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene el historial de compras del usuario
     * 
     * @param int $usuarioId
     * @param int $limite
     * @return array
     */
    public function obtenerHistorialCompras($usuarioId, $limite = 10) {
        try {
            $query = "SELECT 
                        v.id,
                        v.subtotal,
                        v.itbms,
                        v.total,
                        v.fecha_venta,
                        v.estado,
                        COUNT(dv.id) as total_items
                     FROM ventas v
                     LEFT JOIN detalle_venta dv ON v.id = dv.venta_id
                     WHERE v.usuario_id = :usuario_id
                     GROUP BY v.id
                     ORDER BY v.fecha_venta DESC
                     LIMIT :limite";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener historial: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene el detalle de una compra específica
     * 
     * @param int $ventaId
     * @param int $usuarioId
     * @return array
     */
    public function obtenerDetalleCompra($ventaId, $usuarioId) {
        try {
            // Verificar que la venta pertenece al usuario
            $queryVenta = "SELECT * FROM ventas WHERE id = :venta_id AND usuario_id = :usuario_id";
            $stmtVenta = $this->db->prepare($queryVenta);
            $stmtVenta->bindParam(':venta_id', $ventaId, PDO::PARAM_INT);
            $stmtVenta->bindParam(':usuario_id', $usuarioId, PDO::PARAM_INT);
            $stmtVenta->execute();
            $venta = $stmtVenta->fetch();
            
            if (!$venta) {
                return null;
            }
            
            // Obtener detalle
            $queryDetalle = "SELECT 
                                dv.*,
                                a.nombre,
                                a.marca,
                                a.modelo,
                                a.anio,
                                a.thumbnail as imagen_thumb
                            FROM detalle_venta dv
                            INNER JOIN autopartes a ON dv.autoparte_id = a.id
                            WHERE dv.venta_id = :venta_id";
            $stmtDetalle = $this->db->prepare($queryDetalle);
            $stmtDetalle->bindParam(':venta_id', $ventaId, PDO::PARAM_INT);
            $stmtDetalle->execute();
            
            return [
                'venta' => $venta,
                'detalle' => $stmtDetalle->fetchAll()
            ];
            
        } catch (PDOException $e) {
            throw new Exception("Error al obtener detalle: " . $e->getMessage());
        }
    }
}
?>