<?php
/**
 * Controlador Administrador
 * Maneja las funcionalidades del panel administrativo
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Usuario.php';

class AdminController {
    
    private $usuarioModel;
    private $db;
    
    public function __construct() {
        // Verificar que el usuario sea administrador
        if (!hasRole(ROL_ADMINISTRADOR)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado');
            redirect('/index.php?module=auth&action=login');
        }
        
        $this->usuarioModel = new Usuario();
        $this->db = Database::getInstance();
    }
    
    /**
     * Dashboard principal del administrador
     */
    public function dashboard() {
        try {
            // ===== ESTADÍSTICAS GENERALES =====
            
            // Total de usuarios activos
            $totalUsuarios = $this->usuarioModel->contarTodos(['estado' => 1]);
            
            // Total de autopartes en inventario
            $queryAutopartes = "SELECT COUNT(*) as total FROM autopartes WHERE estado = 1";
            $totalAutopartes = $this->db->fetchOne($queryAutopartes)['total'];
            
            // Total de categorías
            $queryCategorias = "SELECT COUNT(*) as total FROM categorias WHERE estado = 1";
            $totalCategorias = $this->db->fetchOne($queryCategorias)['total'];
            
            // Ventas del día
            $queryVentasHoy = "SELECT 
                COUNT(*) as total_ventas,
                COALESCE(SUM(total), 0) as total_ingresos
                FROM ventas 
                WHERE DATE(fecha_venta) = CURDATE()";
            $ventasHoy = $this->db->fetchOne($queryVentasHoy);
            
            // Ventas del mes actual
            $queryVentasMes = "SELECT 
                COUNT(*) as total_ventas,
                COALESCE(SUM(total), 0) as total_ingresos
                FROM ventas 
                WHERE YEAR(fecha_venta) = YEAR(CURDATE())
                AND MONTH(fecha_venta) = MONTH(CURDATE())";
            $ventasMes = $this->db->fetchOne($queryVentasMes);
            
            // Total de ventas acumuladas
            $queryVentasTotal = "SELECT 
                COUNT(*) as total_ventas,
                COALESCE(SUM(total), 0) as total_ingresos
                FROM ventas";
            $ventasTotal = $this->db->fetchOne($queryVentasTotal);
            
            // ===== AUTOPARTES CON STOCK BAJO =====
            $queryStockBajo = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.stock, 
                a.thumbnail, c.nombre as categoria
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.stock <= 5 AND a.estado = 1
                ORDER BY a.stock ASC
                LIMIT 10";
            $stockBajo = $this->db->fetchAll($queryStockBajo);
            
            // ===== ÚLTIMAS VENTAS =====
            $queryUltimasVentas = "SELECT 
                v.id, v.total, v.fecha_venta,
                u.nombre as cliente,
                COUNT(dv.id) as total_items
                FROM ventas v
                INNER JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN detalle_venta dv ON v.id = dv.venta_id
                GROUP BY v.id
                ORDER BY v.fecha_venta DESC
                LIMIT 10";
            $ultimasVentas = $this->db->fetchAll($queryUltimasVentas);
            
            // ===== USUARIOS RECIENTES =====
            $queryUsuariosRecientes = "SELECT 
                u.id, u.nombre, u.email, u.fecha_creacion,
                r.nombre as rol
                FROM usuarios u
                INNER JOIN roles r ON u.rol_id = r.id
                WHERE u.estado = 1
                ORDER BY u.fecha_creacion DESC
                LIMIT 5";
            $usuariosRecientes = $this->db->fetchAll($queryUsuariosRecientes);
            
            // ===== CATEGORÍAS MÁS VENDIDAS =====
            $queryCategoriasTop = "SELECT 
                c.id, c.nombre,
                COUNT(dv.id) as total_ventas,
                SUM(dv.cantidad) as total_piezas
                FROM categorias c
                INNER JOIN autopartes a ON c.id = a.categoria_id
                INNER JOIN detalle_venta dv ON a.id = dv.autoparte_id
                GROUP BY c.id
                ORDER BY total_piezas DESC
                LIMIT 5";
            $categoriasTop = $this->db->fetchAll($queryCategoriasTop);
            
            // ===== AUTOPARTES MÁS VENDIDAS =====
            $queryAutopartesTop = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.thumbnail,
                SUM(dv.cantidad) as total_vendido
                FROM autopartes a
                INNER JOIN detalle_venta dv ON a.id = dv.autoparte_id
                GROUP BY a.id
                ORDER BY total_vendido DESC
                LIMIT 5";
            $autopartesTop = $this->db->fetchAll($queryAutopartesTop);
            
            // ===== COMPARACIÓN MES ACTUAL VS ANTERIOR =====
            $queryComparacionMes = "SELECT 
                (SELECT COALESCE(SUM(total), 0) 
                 FROM ventas 
                 WHERE YEAR(fecha_venta) = YEAR(CURDATE())
                 AND MONTH(fecha_venta) = MONTH(CURDATE())) as mes_actual,
                (SELECT COALESCE(SUM(total), 0) 
                 FROM ventas 
                 WHERE YEAR(fecha_venta) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))
                 AND MONTH(fecha_venta) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))) as mes_anterior";
            $comparacionMes = $this->db->fetchOne($queryComparacionMes);
            
            // Calcular porcentaje de cambio
            $porcentajeCambio = 0;
            if ($comparacionMes['mes_anterior'] > 0) {
                $porcentajeCambio = (($comparacionMes['mes_actual'] - $comparacionMes['mes_anterior']) 
                                    / $comparacionMes['mes_anterior']) * 100;
            }

            // ===== VENTAS ÚLTIMOS 7 DÍAS (para gráfico) =====
            $queryVentasSemana = "SELECT DATE(fecha_venta) AS fecha, COALESCE(SUM(total),0) AS ingresos 
                FROM ventas 
                WHERE fecha_venta >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP BY DATE(fecha_venta)
                ORDER BY DATE(fecha_venta) ASC";
            $ventasSemana = $this->db->fetchAll($queryVentasSemana);
            
            $pageTitle = 'Dashboard Administrativo - Sistema AutoPartes';
            
            require_once VIEWS_PATH . '/admin/dashboard.php';
            
        } catch (Exception $e) {
            error_log("Error en dashboard admin: " . $e->getMessage());
            setFlashMessage(MSG_ERROR, 'Error al cargar el dashboard');
            redirect('/index.php?module=auth&action=login');
        }
    }
    
    /**
     * Perfil del administrador
     */
    public function perfil() {
        try {
            $usuarioId = $_SESSION['usuario_id'];
            
            // Obtener datos del usuario
            $query = "SELECT u.*, r.nombre as rol_nombre 
                     FROM usuarios u 
                     INNER JOIN roles r ON u.rol_id = r.id 
                     WHERE u.id = :id";
            $usuario = $this->db->fetchOne($query, [':id' => $usuarioId]);
            
            if (!$usuario) {
                setFlashMessage(MSG_ERROR, 'Usuario no encontrado');
                redirect('/index.php?module=admin&action=dashboard');
            }
            
            // Estadísticas
            $stats = [
                'total_usuarios' => $this->usuarioModel->contarTodos([]),
                'total_autopartes' => $this->db->fetchOne("SELECT COUNT(*) as total FROM autopartes WHERE estado = 1")['total'],
                'total_ventas' => $this->db->fetchOne("SELECT COUNT(*) as total FROM ventas")['total']
            ];
            
            $pageTitle = 'Mi Perfil - Administrador';
            
            require_once VIEWS_PATH . '/admin/perfil.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar el perfil');
            redirect('/index.php?module=admin&action=dashboard');
        }
    }
    
    /**
     * Actualizar perfil del administrador
     */
    public function perfilUpdate() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=admin&action=perfil');
            }
            
            $usuarioId = $_SESSION['usuario_id'];
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
            
            // Verificar email único
            $queryEmail = "SELECT id FROM usuarios WHERE email = :email AND id != :id";
            $existeEmail = $this->db->fetchOne($queryEmail, [':email' => $email, ':id' => $usuarioId]);
            if ($existeEmail) {
                throw new Exception('El email ya está en uso');
            }
            
            // Obtener datos actuales
            $usuarioActual = $this->db->fetchOne("SELECT password FROM usuarios WHERE id = :id", [':id' => $usuarioId]);
            
            // Si quiere cambiar contraseña
            if (!empty($passwordNuevo)) {
                if (empty($passwordActual) || !password_verify($passwordActual, $usuarioActual['password'])) {
                    throw new Exception('La contraseña actual es incorrecta');
                }
                
                if (strlen($passwordNuevo) < 6) {
                    throw new Exception('La nueva contraseña debe tener al menos 6 caracteres');
                }
                
                if ($passwordNuevo !== $passwordConfirm) {
                    throw new Exception('Las contraseñas no coinciden');
                }
                
                $query = "UPDATE usuarios SET nombre = :nombre, email = :email, password = :password WHERE id = :id";
                $stmt = $this->db->getConnection()->prepare($query);
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':email' => $email,
                    ':password' => password_hash($passwordNuevo, PASSWORD_DEFAULT),
                    ':id' => $usuarioId
                ]);
            } else {
                $query = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
                $stmt = $this->db->getConnection()->prepare($query);
                $stmt->execute([
                    ':nombre' => $nombre,
                    ':email' => $email,
                    ':id' => $usuarioId
                ]);
            }
            
            // Actualizar sesión
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['usuario_email'] = $email;
            
            setFlashMessage(MSG_SUCCESS, 'Perfil actualizado correctamente');
            redirect('/index.php?module=admin&action=perfil');
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, $e->getMessage());
            redirect('/index.php?module=admin&action=perfil');
        }
    }
    
    /**
     * Ver todas las ventas
     */
    public function ventas() {
        try {
            // Filtros
            $filtros = [
                'fecha_desde' => $_GET['fecha_desde'] ?? '',
                'fecha_hasta' => $_GET['fecha_hasta'] ?? '',
                'cliente' => $_GET['cliente'] ?? '',
            ];
            
            // Paginación
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $porPagina = 20;
            $offset = ($pagina - 1) * $porPagina;
            
            $query = "SELECT 
                v.id, v.total, v.subtotal, v.itbms, v.fecha_venta, v.estado,
                u.nombre as cliente, u.email as cliente_email,
                COUNT(dv.id) as total_items
                FROM ventas v
                INNER JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN detalle_venta dv ON v.id = dv.venta_id
                WHERE 1=1";
            
            $params = [];
            
            if (!empty($filtros['fecha_desde'])) {
                $query .= " AND DATE(v.fecha_venta) >= :fecha_desde";
                $params[':fecha_desde'] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $query .= " AND DATE(v.fecha_venta) <= :fecha_hasta";
                $params[':fecha_hasta'] = $filtros['fecha_hasta'];
            }
            
            if (!empty($filtros['cliente'])) {
                $query .= " AND (u.nombre LIKE :cliente OR u.email LIKE :cliente)";
                $params[':cliente'] = '%' . $filtros['cliente'] . '%';
            }
            
            $query .= " GROUP BY v.id ORDER BY v.fecha_venta DESC";
            
            // Contar total de ventas (subconsulta segura que respeta filtros)
            $queryCount = "SELECT COUNT(*) as total FROM (
                SELECT v.id
                FROM ventas v
                INNER JOIN usuarios u ON v.usuario_id = u.id
                LEFT JOIN detalle_venta dv ON v.id = dv.venta_id
                WHERE 1=1";

            // Reaplicar filtros en subconsulta
            if (!empty($filtros['fecha_desde'])) {
                $queryCount .= " AND DATE(v.fecha_venta) >= :fecha_desde";
            }
            if (!empty($filtros['fecha_hasta'])) {
                $queryCount .= " AND DATE(v.fecha_venta) <= :fecha_hasta";
            }
            if (!empty($filtros['cliente'])) {
                $queryCount .= " AND (u.nombre LIKE :cliente OR u.email LIKE :cliente)";
            }

            $queryCount .= " GROUP BY v.id) as t";

            $totalVentas = $this->db->fetchOne($queryCount, $params)['total'] ?? 0;
            $totalPaginas = ceil($totalVentas / $porPagina);

            // Aplicar paginación
            $query .= " LIMIT $porPagina OFFSET $offset";

            $ventas = $this->db->fetchAll($query, $params);
            
            // Estadísticas rápidas
            $statsVentas = $this->db->fetchOne("SELECT 
                COUNT(*) as total,
                COALESCE(SUM(total), 0) as ingresos,
                COALESCE(AVG(total), 0) as promedio
                FROM ventas");
            
            $pageTitle = 'Gestión de Ventas';
            
            require_once VIEWS_PATH . '/admin/ventas.php';
            
        } catch (Exception $e) {
            error_log('Error en AdminController::ventas - ' . $e->getMessage());
            setFlashMessage(MSG_ERROR, 'Error al cargar ventas: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=dashboard');
        }
    }
    
    /**
     * Detalle de una venta específica
     */
    public function ventaDetalle() {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Venta no especificada');
                redirect('/index.php?module=admin&action=ventas');
            }
            
            // Obtener venta
            $query = "SELECT 
                v.*,
                u.nombre as cliente, u.email as cliente_email
                FROM ventas v
                INNER JOIN usuarios u ON v.usuario_id = u.id
                WHERE v.id = :id";
            $venta = $this->db->fetchOne($query, [':id' => $id]);
            
            if (!$venta) {
                setFlashMessage(MSG_ERROR, 'Venta no encontrada');
                redirect('/index.php?module=admin&action=ventas');
            }
            
            // Obtener detalles
            $queryDetalles = "SELECT 
                dv.*,
                a.nombre as autoparte_nombre, a.marca, a.modelo, a.thumbnail
                FROM detalle_venta dv
                INNER JOIN autopartes a ON dv.autoparte_id = a.id
                WHERE dv.venta_id = :venta_id";
            $detalles = $this->db->fetchAll($queryDetalles, [':venta_id' => $id]);
            
            $pageTitle = 'Detalle de Venta #' . str_pad($id, 6, '0', STR_PAD_LEFT);
            
            require_once VIEWS_PATH . '/admin/venta_detalle.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar detalle');
            redirect('/index.php?module=admin&action=ventas');
        }
    }
    
    /**
     * Lista de autopartes con stock bajo
     */
    public function stockBajo() {
        try {
            $limite = $_GET['limite'] ?? 5;
            
            $query = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.stock, a.precio,
                a.thumbnail, c.nombre as categoria
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.stock <= :limite AND a.estado = 1
                ORDER BY a.stock ASC";
            
            $autopartes = $this->db->fetchAll($query, [':limite' => $limite]);
            
            $pageTitle = 'Alertas de Stock Bajo';
            
            require_once VIEWS_PATH . '/admin/stock_bajo.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar inventario');
            redirect('/index.php?module=admin&action=dashboard');
        }
    }
    
    /**
     * Estadísticas y reportes
     */
    public function estadisticas() {
        try {
            // Ventas por mes (últimos 12 meses)
            $queryVentasMes = "SELECT 
                DATE_FORMAT(fecha_venta, '%Y-%m') as mes,
                DATE_FORMAT(fecha_venta, '%b %Y') as mes_label,
                COUNT(*) as total_ventas,
                SUM(total) as ingresos
                FROM ventas
                WHERE fecha_venta >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY mes
                ORDER BY mes ASC";
            $ventasPorMes = $this->db->fetchAll($queryVentasMes);
            
            // Productos más vendidos
            $queryTopProductos = "SELECT 
                a.id, a.nombre, a.marca, a.thumbnail,
                SUM(dv.cantidad) as total_vendido,
                SUM(dv.subtotal) as ingresos
                FROM autopartes a
                INNER JOIN detalle_venta dv ON a.id = dv.autoparte_id
                GROUP BY a.id
                ORDER BY total_vendido DESC
                LIMIT 10";
            $topProductos = $this->db->fetchAll($queryTopProductos);
            
            // Categorías más vendidas
            $queryCategorias = "SELECT 
                c.nombre,
                SUM(dv.cantidad) as total_vendido,
                SUM(dv.subtotal) as ingresos
                FROM categorias c
                INNER JOIN autopartes a ON c.id = a.categoria_id
                INNER JOIN detalle_venta dv ON a.id = dv.autoparte_id
                GROUP BY c.id
                ORDER BY total_vendido DESC";
            $categorias = $this->db->fetchAll($queryCategorias);
            
            // Clientes top
            $queryTopClientes = "SELECT 
                u.nombre, u.email,
                COUNT(v.id) as total_compras,
                SUM(v.total) as total_gastado
                FROM usuarios u
                INNER JOIN ventas v ON u.id = v.usuario_id
                GROUP BY u.id
                ORDER BY total_gastado DESC
                LIMIT 10";
            $topClientes = $this->db->fetchAll($queryTopClientes);
            
            // Estadísticas generales
            $stats = [
                'total_ventas' => $this->db->fetchOne("SELECT COUNT(*) as total FROM ventas")['total'],
                'ingresos_totales' => $this->db->fetchOne("SELECT COALESCE(SUM(total), 0) as total FROM ventas")['total'],
                'ticket_promedio' => $this->db->fetchOne("SELECT COALESCE(AVG(total), 0) as total FROM ventas")['total'],
                'total_clientes' => $this->db->fetchOne("SELECT COUNT(DISTINCT usuario_id) as total FROM ventas")['total']
            ];
            
            $pageTitle = 'Estadísticas y Reportes';
            
            require_once VIEWS_PATH . '/admin/estadisticas.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar estadísticas');
            redirect('/index.php?module=admin&action=dashboard');
        }
    }
    
    /**
     * Exportar ventas a Excel/CSV
     */
    public function exportarVentas() {
        try {
            $formato = $_GET['formato'] ?? 'csv';
            $desde = $_GET['desde'] ?? date('Y-m-01');
            $hasta = $_GET['hasta'] ?? date('Y-m-d');
            
            $query = "SELECT 
                v.id as 'ID Venta',
                u.nombre as 'Cliente',
                u.email as 'Email',
                v.subtotal as 'Subtotal',
                v.itbms as 'ITBMS',
                v.total as 'Total',
                v.fecha_venta as 'Fecha',
                v.estado as 'Estado'
                FROM ventas v
                INNER JOIN usuarios u ON v.usuario_id = u.id
                WHERE DATE(v.fecha_venta) BETWEEN :desde AND :hasta
                ORDER BY v.fecha_venta DESC";
            
            $ventas = $this->db->fetchAll($query, [':desde' => $desde, ':hasta' => $hasta]);
            
            if ($formato === 'csv') {
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=ventas_' . date('Y-m-d') . '.csv');
                
                $output = fopen('php://output', 'w');
                
                // BOM para Excel
                fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Encabezados
                if (!empty($ventas)) {
                    fputcsv($output, array_keys($ventas[0]));
                }
                
                // Datos
                foreach ($ventas as $venta) {
                    fputcsv($output, $venta);
                }
                
                fclose($output);
                exit;
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al exportar');
            redirect('/index.php?module=admin&action=ventas');
        }
    }
    
    /**
     * Obtiene estadísticas en tiempo real (AJAX)
     */
    public function getEstadisticas() {
        try {
            $tipo = $_GET['tipo'] ?? 'general';
            
            switch ($tipo) {
                case 'ventas_hoy':
                    $query = "SELECT 
                        COUNT(*) as total,
                        COALESCE(SUM(total), 0) as ingresos
                        FROM ventas 
                        WHERE DATE(fecha_venta) = CURDATE()";
                    $data = $this->db->fetchOne($query);
                    break;
                    
                case 'stock_bajo':
                    $query = "SELECT COUNT(*) as total 
                             FROM autopartes 
                             WHERE stock <= 5 AND estado = 1";
                    $data = $this->db->fetchOne($query);
                    break;
                    
                case 'usuarios_activos':
                    $data = ['total' => $this->usuarioModel->contarTodos(['estado' => 1])];
                    break;
                    
                default:
                    $data = ['error' => 'Tipo de estadística no válido'];
            }
            
            jsonResponse(['success' => true, 'data' => $data]);
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => 'Error al obtener estadísticas']);
        }
    }
    
    /**
     * Obtiene datos para gráficos (AJAX)
     */
    public function getGraficoVentas() {
        try {
            $periodo = $_GET['periodo'] ?? '7dias';
            
            switch ($periodo) {
                case '7dias':
                    $query = "SELECT 
                        DATE_FORMAT(fecha_venta, '%d/%m') as label,
                        COUNT(*) as ventas,
                        SUM(total) as ingresos
                        FROM ventas
                        WHERE fecha_venta >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                        GROUP BY DATE(fecha_venta)
                        ORDER BY fecha_venta ASC";
                    break;
                    
                case '30dias':
                    $query = "SELECT 
                        DATE_FORMAT(fecha_venta, '%d/%m') as label,
                        COUNT(*) as ventas,
                        SUM(total) as ingresos
                        FROM ventas
                        WHERE fecha_venta >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                        GROUP BY DATE(fecha_venta)
                        ORDER BY fecha_venta ASC";
                    break;
                    
                default:
                    jsonResponse(['success' => false, 'message' => 'Período no válido']);
                    return;
            }
            
            $data = $this->db->fetchAll($query);
            
            $labels = [];
            $ventas = [];
            $ingresos = [];
            
            foreach ($data as $row) {
                $labels[] = $row['label'];
                $ventas[] = (int)$row['ventas'];
                $ingresos[] = (float)$row['ingresos'];
            }
            
            jsonResponse([
                'success' => true,
                'labels' => $labels,
                'ventas' => $ventas,
                'ingresos' => $ingresos
            ]);
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => 'Error al obtener datos del gráfico']);
        }
    }
}
?>