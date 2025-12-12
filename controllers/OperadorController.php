<?php
/**
 * Controlador Operador
 * Maneja las acciones del operador en el sistema de inventario de autopartes
 */

if (!class_exists('Database')) {
    require_once __DIR__ . '/../config/Database.php';
}
if (!class_exists('Autoparte')) {
    require_once __DIR__ . '/../models/Autoparte.php';
}
if (!class_exists('Validator')) {
    require_once __DIR__ . '/../core/Validator.php';
}

class OperadorController {
    
    public function __construct() {
        // Verificar que sea operador o administrador
        if (!hasRole(ROL_OPERADOR) && !hasRole(ROL_ADMINISTRADOR)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado');
            redirect('/index.php?module=auth&action=login');
        }
    }
    
    /**
     * Dashboard del operador
     */
    public function dashboard() {
        try {
            $db = Database::getInstance();
            
            // Estadísticas básicas del operador
            
            // Total de autopartes
            $queryAutopartes = "SELECT COUNT(*) as total FROM autopartes WHERE estado = 1";
            $totalAutopartes = $db->fetchOne($queryAutopartes)['total'];
            
            // Stock bajo
            $queryStockBajo = "SELECT COUNT(*) as total FROM autopartes WHERE stock <= 5 AND estado = 1";
            $alertasStock = $db->fetchOne($queryStockBajo)['total'];
            
            // Ventas del día
            $queryVentasHoy = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha_venta) = CURDATE()";
            $ventasHoy = $db->fetchOne($queryVentasHoy)['total'];
            
            // Autopartes agregadas recientemente por este operador (simulado)
            $queryRecientes = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.stock, a.thumbnail,
                c.nombre as categoria, a.fecha_creacion
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.estado = 1
                ORDER BY a.fecha_creacion DESC
                LIMIT 10";
            $autopartesRecientes = $db->fetchAll($queryRecientes);
            
            // Stock bajo detallado
            $queryStockBajoDetalle = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.stock, a.thumbnail,
                c.nombre as categoria
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.stock <= 5 AND a.estado = 1
                ORDER BY a.stock ASC
                LIMIT 15";
            $stockBajo = $db->fetchAll($queryStockBajoDetalle);
            
            // Variables para la vista
            $pageTitle = 'Panel de Operador - Sistema AutoPartes';
            
            // Incluir la vista
            require_once VIEWS_PATH . '/operador/dashboard.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar el panel');
            redirect('/index.php?module=auth&action=login');
        }
    }
}
?>