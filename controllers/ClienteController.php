<?php
/**
 * Controlador Cliente
 * Maneja las funcionalidades del cliente (compras, carrito, historial, perfil)
 * 
 * @author Grupo 1SF131
 * @version 1.0
 */

// Cargar dependencias necesarias
if (!class_exists('Database')) {
    require_once __DIR__ . '/../config/Database.php';
}

class ClienteController {
    
    private $usuarioId;
    private $db;
    
    public function __construct() {
        // Verificar que sea cliente
        if (!hasRole(ROL_CLIENTE)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado');
            redirect('/index.php?module=auth&action=login');
        }
        
        $this->usuarioId = $_SESSION['usuario_id'];
        $this->db = Database::getInstance();
    }
    
    /**
     * Dashboard del cliente
     */
    public function dashboard() {
        try {
            // Total de compras del cliente
            $queryTotalCompras = "SELECT COUNT(*) as total FROM ventas WHERE usuario_id = :usuario_id";
            $totalCompras = $this->db->fetchOne($queryTotalCompras, [':usuario_id' => $this->usuarioId])['total'] ?? 0;
            
            // Total gastado
            $queryTotalGastado = "SELECT COALESCE(SUM(total), 0) as total FROM ventas WHERE usuario_id = :usuario_id";
            $totalGastado = $this->db->fetchOne($queryTotalGastado, [':usuario_id' => $this->usuarioId])['total'] ?? 0;
            
            // Items en el carrito
            $queryCarrito = "SELECT COALESCE(SUM(cantidad), 0) as total FROM carrito WHERE usuario_id = :usuario_id";
            $itemsCarrito = $this->db->fetchOne($queryCarrito, [':usuario_id' => $this->usuarioId])['total'] ?? 0;
            
            // Actualizar sesión
            $_SESSION['carrito_items'] = (int)$itemsCarrito;
            
            // Últimas compras
            $queryUltimasCompras = "SELECT 
                v.id, v.total, v.fecha_venta, v.estado,
                COUNT(dv.id) as total_items
                FROM ventas v
                LEFT JOIN detalle_venta dv ON v.id = dv.venta_id
                WHERE v.usuario_id = :usuario_id
                GROUP BY v.id
                ORDER BY v.fecha_venta DESC
                LIMIT 5";
            $ultimasCompras = $this->db->fetchAll($queryUltimasCompras, [':usuario_id' => $this->usuarioId]);
            
            // Autopartes más compradas
            $queryAutopartesTop = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.thumbnail,
                SUM(dv.cantidad) as total_comprado
                FROM autopartes a
                INNER JOIN detalle_venta dv ON a.id = dv.autoparte_id
                INNER JOIN ventas v ON dv.venta_id = v.id
                WHERE v.usuario_id = :usuario_id
                GROUP BY a.id
                ORDER BY total_comprado DESC
                LIMIT 5";
            $autopartesTop = $this->db->fetchAll($queryAutopartesTop, [':usuario_id' => $this->usuarioId]);
            
            // Variables para la vista
            $pageTitle = 'Mi Panel - Cliente';
            
            // Incluir la vista
            require_once VIEWS_PATH . '/cliente/dashboard.php';
            
        } catch (Exception $e) {
            error_log("Error en dashboard cliente: " . $e->getMessage());
            setFlashMessage(MSG_ERROR, 'Error al cargar el panel');
            redirect('/index.php');
        }
    }
    
    /**
     * Perfil del usuario
     */
    public function perfil() {
        try {
            // Obtener datos del usuario
            $query = "SELECT u.*, r.nombre as rol_nombre 
                     FROM usuarios u 
                     INNER JOIN roles r ON u.rol_id = r.id 
                     WHERE u.id = :id";
            $usuario = $this->db->fetchOne($query, [':id' => $this->usuarioId]);
            
            if (!$usuario) {
                setFlashMessage(MSG_ERROR, 'Usuario no encontrado');
                redirect('/index.php?module=cliente&action=dashboard');
            }
            
            // Estadísticas del usuario
            $queryStats = "SELECT 
                (SELECT COUNT(*) FROM ventas WHERE usuario_id = :id1) as total_compras,
                (SELECT COALESCE(SUM(total), 0) FROM ventas WHERE usuario_id = :id2) as total_gastado,
                (SELECT COUNT(*) FROM comentarios WHERE usuario_id = :id3) as total_comentarios";
            $stats = $this->db->fetchOne($queryStats, [
                ':id1' => $this->usuarioId,
                ':id2' => $this->usuarioId,
                ':id3' => $this->usuarioId
            ]);
            
            $pageTitle = 'Mi Perfil';
            
            require_once VIEWS_PATH . '/cliente/perfil.php';
            
        } catch (Exception $e) {
            error_log("Error en perfil: " . $e->getMessage());
            setFlashMessage(MSG_ERROR, 'Error al cargar el perfil');
            redirect('/index.php?module=cliente&action=dashboard');
        }
    }
    
    /**
     * Actualizar perfil del usuario
     */
    public function perfilUpdate() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=cliente&action=perfil');
            }
            
            // Verificar CSRF
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verifyCSRFToken($csrfToken)) {
                throw new Exception('Token de seguridad inválido');
            }
            
            $nombre = trim($_POST['nombre'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $passwordActual = $_POST['password_actual'] ?? '';
            $passwordNuevo = $_POST['password_nuevo'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            
            // Validaciones
            if (empty($nombre)) {
                throw new Exception('El nombre es requerido');
            }
            
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email inválido');
            }
            
            // Verificar que el email no esté en uso por otro usuario
            $queryEmail = "SELECT id FROM usuarios WHERE email = :email AND id != :id";
            $existeEmail = $this->db->fetchOne($queryEmail, [':email' => $email, ':id' => $this->usuarioId]);
            if ($existeEmail) {
                throw new Exception('El email ya está en uso por otro usuario');
            }
            
            // Obtener datos actuales del usuario
            $queryUsuario = "SELECT password FROM usuarios WHERE id = :id";
            $usuarioActual = $this->db->fetchOne($queryUsuario, [':id' => $this->usuarioId]);
            
            // Si quiere cambiar la contraseña
            if (!empty($passwordNuevo)) {
                // Verificar contraseña actual
                if (empty($passwordActual) || !password_verify($passwordActual, $usuarioActual['password'])) {
                    throw new Exception('La contraseña actual es incorrecta');
                }
                
                if (strlen($passwordNuevo) < 6) {
                    throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
                }
                
                if ($passwordNuevo !== $passwordConfirm) {
                    throw new Exception('Las contraseñas no coinciden');
                }
                
                // Actualizar con nueva contraseña
                $query = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :password WHERE id = :id";
                $stmt = $this->db->getConnection()->prepare($query);
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':email' => $email,
                    ':password' => password_hash($passwordNuevo, PASSWORD_DEFAULT),
                    ':id' => $this->usuarioId
                ]);
            } else {
                // Actualizar sin cambiar contraseña
                $query = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
                $stmt = $this->db->getConnection()->prepare($query);
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':email' => $email,
                    ':id' => $this->usuarioId
                ]);
            }
            
            // Actualizar sesión
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['usuario_email'] = $email;
            
            setFlashMessage(MSG_SUCCESS, 'Perfil actualizado correctamente');
            redirect('/index.php?module=cliente&action=perfil');
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, $e->getMessage());
            redirect('/index.php?module=cliente&action=perfil');
        }
    }
    
    /**
     * Obtiene el contador del carrito (AJAX)
     */
    public function cart_count() {
        try {
            $query = "SELECT COALESCE(SUM(cantidad), 0) as total FROM carrito WHERE usuario_id = :usuario_id";
            $result = $this->db->fetchOne($query, [':usuario_id' => $this->usuarioId]);
            
            jsonResponse([
                'success' => true,
                'count' => (int)$result['total']
            ]);
            
        } catch (Exception $e) {
            jsonResponse([
                'success' => false,
                'count' => 0
            ]);
        }
    }
    
    /**
     * Ver comentarios del usuario
     */
    public function comentarios() {
        try {
            $query = "SELECT 
                c.*, 
                a.nombre as autoparte_nombre,
                a.id as autoparte_id,
                a.thumbnail
                FROM comentarios c
                INNER JOIN autopartes a ON c.autoparte_id = a.id
                WHERE c.usuario_id = :usuario_id
                ORDER BY c.fecha_creacion DESC";
            $comentarios = $this->db->fetchAll($query, [':usuario_id' => $this->usuarioId]);
            
            $pageTitle = 'Mis Comentarios';
            
            require_once VIEWS_PATH . '/cliente/comentarios.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar comentarios');
            redirect('/index.php?module=cliente&action=dashboard');
        }
    }
    
    /**
     * Vista del carrito (redirige al módulo carrito)
     */
    public function carrito() {
        redirect('/index.php?module=carrito&action=ver');
    }
}
?>