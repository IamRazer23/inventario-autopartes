<?php
/**
 * Controlador Público
 * Maneja las páginas públicas (catálogo, detalles, búsqueda)
 * 
 * @author Grupo 1SF131
 * @version 1.0
 */

// Asegurarse de que Database esté disponible
require_once __DIR__ . '/../config/Database.php';

class PublicController {
    
    /**
     * Página de inicio
     */
    public function home() {
        try {
            $db = Database::getInstance();
            
            // Categorías disponibles con conteo de productos
            $queryCategorias = "SELECT 
                c.id, c.nombre, c.descripcion, c.imagen,
                COUNT(a.id) as total_autopartes
                FROM categorias c
                LEFT JOIN autopartes a ON c.id = a.categoria_id AND a.estado = 1 AND a.stock > 0
                WHERE c.estado = 1
                GROUP BY c.id
                ORDER BY c.nombre ASC";
            $categorias = $db->fetchAll($queryCategorias);
            
            // Autopartes destacadas (más recientes)
            $queryDestacadas = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.anio, a.precio, a.stock, 
                a.thumbnail as imagen_thumb,
                c.nombre as categoria_nombre
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.estado = 1 AND a.stock > 0
                ORDER BY a.fecha_creacion DESC
                LIMIT 8";
            $destacadas = $db->fetchAll($queryDestacadas);
            
            // Variables para la vista
            $pageTitle = 'Inicio - AutoPartes Pro';
            
            // Incluir la vista
            require_once VIEWS_PATH . '/public/home.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar la página');
            require_once VIEWS_PATH . '/public/home.php';
        }
    }
    
    /**
     * Catálogo de autopartes
     */
    public function catalogo() {
        try {
            $db = Database::getInstance();
            
            // Filtros de búsqueda
            $filtros = [
                'categoria_id' => $_GET['categoria'] ?? '',
                'marca' => $_GET['marca'] ?? '',
                'buscar' => $_GET['buscar'] ?? '',
                'anio' => $_GET['anio'] ?? '',
                'precio_min' => $_GET['precio_min'] ?? '',
                'precio_max' => $_GET['precio_max'] ?? '',
                'orden' => $_GET['orden'] ?? 'fecha_creacion',
                'direccion' => $_GET['direccion'] ?? 'DESC'
            ];
            
            // Paginación
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $porPagina = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 12;
            $offset = ($pagina - 1) * $porPagina;
            
            // Construir query
            $query = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.anio, a.precio, a.stock, 
                a.thumbnail as imagen_thumb,
                c.nombre as categoria_nombre, c.id as categoria_id
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.estado = 1";
            
            $params = [];
            
            // Aplicar filtros
            if (!empty($filtros['categoria_id'])) {
                $query .= " AND c.id = :categoria_id";
                $params[':categoria_id'] = $filtros['categoria_id'];
            }
            
            if (!empty($filtros['marca'])) {
                $query .= " AND a.marca = :marca";
                $params[':marca'] = $filtros['marca'];
            }
            
            if (!empty($filtros['buscar'])) {
                $query .= " AND (a.nombre LIKE :buscar OR a.marca LIKE :buscar OR a.modelo LIKE :buscar)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
            }
            
            if (!empty($filtros['anio'])) {
                $query .= " AND a.anio = :anio";
                $params[':anio'] = $filtros['anio'];
            }
            
            if (!empty($filtros['precio_min'])) {
                $query .= " AND a.precio >= :precio_min";
                $params[':precio_min'] = $filtros['precio_min'];
            }
            
            if (!empty($filtros['precio_max'])) {
                $query .= " AND a.precio <= :precio_max";
                $params[':precio_max'] = $filtros['precio_max'];
            }
            
            // Contar total para paginación
            $queryCount = preg_replace(
                '/SELECT .* FROM/',
                'SELECT COUNT(DISTINCT a.id) as total FROM',
                $query
            );
            
            $totalAutopartes = $db->fetchOne($queryCount, $params)['total'] ?? 0;
            $totalPaginas = ceil($totalAutopartes / $porPagina);
            
            // Ordenamiento
            $ordenValido = ['fecha_creacion', 'precio', 'nombre', 'marca'];
            $direccionValida = ['ASC', 'DESC'];
            
            $orden = in_array($filtros['orden'], $ordenValido) ? $filtros['orden'] : 'fecha_creacion';
            $direccion = in_array($filtros['direccion'], $direccionValida) ? $filtros['direccion'] : 'DESC';
            
            $query .= " ORDER BY a.{$orden} {$direccion}";
            
            // Aplicar límite y offset
            $query .= " LIMIT :limit OFFSET :offset";
            $stmt = $db->getConnection()->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $autopartes = $stmt->fetchAll();
            
            // Obtener todas las categorías para el filtro
            $queryCategorias = "SELECT 
                c.id, c.nombre,
                COUNT(a.id) as total_autopartes
                FROM categorias c
                LEFT JOIN autopartes a ON c.id = a.categoria_id AND a.estado = 1
                WHERE c.estado = 1
                GROUP BY c.id
                ORDER BY c.nombre ASC";
            $categorias = $db->fetchAll($queryCategorias);
            
            // Obtener marcas únicas
            $queryMarcas = "SELECT DISTINCT marca FROM autopartes WHERE estado = 1 ORDER BY marca";
            $marcasResult = $db->fetchAll($queryMarcas);
            $marcas = array_column($marcasResult, 'marca');
            
            // Obtener años únicos
            $queryAnios = "SELECT DISTINCT anio FROM autopartes WHERE estado = 1 ORDER BY anio DESC";
            $aniosResult = $db->fetchAll($queryAnios);
            $anios = array_column($aniosResult, 'anio');
            
            $pageTitle = 'Catálogo de Autopartes';
            
            require_once VIEWS_PATH . '/public/catalogo.php';
            
        } catch (Exception $e) {
            error_log("Error en catalogo(): " . $e->getMessage());
            setFlashMessage(MSG_ERROR, 'Error al cargar el catálogo');
            redirect('/index.php');
        }
    }
    
    /**
     * Ver productos de una categoría específica
     */
    public function categoria() {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Categoría no encontrada');
                redirect('/index.php?module=publico&action=catalogo');
            }
            
            $db = Database::getInstance();
            
            // Obtener datos de la categoría
            $queryCategoria = "SELECT * FROM categorias WHERE id = :id AND estado = 1";
            $categoria = $db->fetchOne($queryCategoria, [':id' => $id]);
            
            if (!$categoria) {
                setFlashMessage(MSG_ERROR, 'Categoría no encontrada');
                redirect('/index.php?module=publico&action=catalogo');
            }
            
            // Filtros de búsqueda
            $filtros = [
                'marca' => $_GET['marca'] ?? '',
                'buscar' => $_GET['buscar'] ?? '',
                'anio' => $_GET['anio'] ?? '',
                'precio_min' => $_GET['precio_min'] ?? '',
                'precio_max' => $_GET['precio_max'] ?? '',
                'orden' => $_GET['orden'] ?? 'fecha_creacion',
                'direccion' => $_GET['direccion'] ?? 'DESC'
            ];
            
            // Paginación
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $porPagina = defined('ITEMS_PER_PAGE') ? ITEMS_PER_PAGE : 12;
            $offset = ($pagina - 1) * $porPagina;
            
            // Construir query
            $query = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.anio, a.precio, a.stock, 
                a.thumbnail as imagen_thumb,
                c.nombre as categoria_nombre, c.id as categoria_id
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.estado = 1 AND c.id = :categoria_id";
            
            $params = [':categoria_id' => $id];
            
            // Aplicar filtros adicionales
            if (!empty($filtros['marca'])) {
                $query .= " AND a.marca = :marca";
                $params[':marca'] = $filtros['marca'];
            }
            
            if (!empty($filtros['buscar'])) {
                $query .= " AND (a.nombre LIKE :buscar OR a.marca LIKE :buscar OR a.modelo LIKE :buscar)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
            }
            
            if (!empty($filtros['anio'])) {
                $query .= " AND a.anio = :anio";
                $params[':anio'] = $filtros['anio'];
            }
            
            if (!empty($filtros['precio_min'])) {
                $query .= " AND a.precio >= :precio_min";
                $params[':precio_min'] = $filtros['precio_min'];
            }
            
            if (!empty($filtros['precio_max'])) {
                $query .= " AND a.precio <= :precio_max";
                $params[':precio_max'] = $filtros['precio_max'];
            }
            
            // Contar total
            $queryCount = preg_replace(
                '/SELECT .* FROM/',
                'SELECT COUNT(DISTINCT a.id) as total FROM',
                $query
            );
            
            $totalAutopartes = $db->fetchOne($queryCount, $params)['total'] ?? 0;
            $totalPaginas = ceil($totalAutopartes / $porPagina);
            
            // Ordenamiento
            $ordenValido = ['fecha_creacion', 'precio', 'nombre', 'marca'];
            $direccionValida = ['ASC', 'DESC'];
            
            $orden = in_array($filtros['orden'], $ordenValido) ? $filtros['orden'] : 'fecha_creacion';
            $direccion = in_array($filtros['direccion'], $direccionValida) ? $filtros['direccion'] : 'DESC';
            
            $query .= " ORDER BY a.{$orden} {$direccion}";
            $query .= " LIMIT :limit OFFSET :offset";
            
            $stmt = $db->getConnection()->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            $autopartes = $stmt->fetchAll();
            
            // Obtener marcas de esta categoría
            $queryMarcas = "SELECT DISTINCT a.marca FROM autopartes a WHERE a.estado = 1 AND a.categoria_id = :categoria_id ORDER BY a.marca";
            $marcasResult = $db->fetchAll($queryMarcas, [':categoria_id' => $id]);
            $marcas = array_column($marcasResult, 'marca');
            
            // Obtener años de esta categoría
            $queryAnios = "SELECT DISTINCT a.anio FROM autopartes a WHERE a.estado = 1 AND a.categoria_id = :categoria_id ORDER BY a.anio DESC";
            $aniosResult = $db->fetchAll($queryAnios, [':categoria_id' => $id]);
            $anios = array_column($aniosResult, 'anio');
            
            // Obtener todas las categorías para navegación
            $queryCategorias = "SELECT 
                c.id, c.nombre,
                COUNT(a.id) as total_autopartes
                FROM categorias c
                LEFT JOIN autopartes a ON c.id = a.categoria_id AND a.estado = 1
                WHERE c.estado = 1
                GROUP BY c.id
                ORDER BY c.nombre ASC";
            $categorias = $db->fetchAll($queryCategorias);
            
            $pageTitle = $categoria['nombre'] . ' - Catálogo';
            
            require_once VIEWS_PATH . '/public/catalogo.php';
            
        } catch (Exception $e) {
            error_log("Error en categoria(): " . $e->getMessage());
            setFlashMessage(MSG_ERROR, 'Error al cargar la categoría');
            redirect('/index.php?module=publico&action=catalogo');
        }
    }
    
    /**
     * Detalle de una autoparte
     */
    public function detalle() {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=publico&action=catalogo');
            }
            
            $db = Database::getInstance();
            
            // Obtener autoparte
            $query = "SELECT 
                a.*, 
                c.nombre as categoria_nombre, 
                c.id as categoria_id
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.id = :id AND a.estado = 1";
            
            $autoparte = $db->fetchOne($query, [':id' => $id]);
            
            if (!$autoparte) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=publico&action=catalogo');
            }
            
            // Obtener comentarios publicados
            $queryComentarios = "SELECT 
                co.*, u.nombre as usuario_nombre
                FROM comentarios co
                LEFT JOIN usuarios u ON co.usuario_id = u.id
                WHERE co.autoparte_id = :id AND co.publicar = 1
                ORDER BY co.fecha_creacion DESC";
            $comentarios = $db->fetchAll($queryComentarios, [':id' => $id]);
            
            // Autopartes relacionadas (misma categoría)
            $queryRelacionadas = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.precio, 
                a.thumbnail as imagen_thumb
                FROM autopartes a
                WHERE a.categoria_id = :categoria_id 
                AND a.id != :id 
                AND a.estado = 1 
                AND a.stock > 0
                ORDER BY RAND()
                LIMIT 4";
            $relacionadas = $db->fetchAll($queryRelacionadas, [
                ':categoria_id' => $autoparte['categoria_id'],
                ':id' => $id
            ]);
            
            $pageTitle = $autoparte['nombre'] . ' - ' . $autoparte['marca'];
            
            require_once VIEWS_PATH . '/public/detalle.php';
            
        } catch (Exception $e) {
            error_log("Error en detalle(): " . $e->getMessage());
            setFlashMessage(MSG_ERROR, 'Error al cargar el detalle');
            redirect('/index.php?module=publico&action=catalogo');
        }
    }
    
    /**
     * Agregar comentario a una autoparte
     */
    public function comentar() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            if (!isAuthenticated()) {
                throw new Exception('Debes iniciar sesión para comentar');
            }
            
            $autoparteId = filter_input(INPUT_POST, 'autoparte_id', FILTER_VALIDATE_INT);
            $comentario = trim($_POST['comentario'] ?? '');
            $calificacion = filter_input(INPUT_POST, 'calificacion', FILTER_VALIDATE_INT) ?: 5;
            
            if (!$autoparteId) {
                throw new Exception('Producto no válido');
            }
            
            if (empty($comentario)) {
                throw new Exception('El comentario no puede estar vacío');
            }
            
            if ($calificacion < 1 || $calificacion > 5) {
                $calificacion = 5;
            }
            
            $db = Database::getInstance();
            
            // Verificar que la autoparte existe
            $autoparte = $db->fetchOne("SELECT id FROM autopartes WHERE id = :id AND estado = 1", [':id' => $autoparteId]);
            if (!$autoparte) {
                throw new Exception('Producto no encontrado');
            }
            
            // Insertar comentario (pendiente de aprobación)
            $query = "INSERT INTO comentarios (autoparte_id, usuario_id, comentario, calificacion, publicar) 
                     VALUES (:autoparte_id, :usuario_id, :comentario, :calificacion, 0)";
            
            $stmt = $db->getConnection()->prepare($query);
            $stmt->execute([
                ':autoparte_id' => $autoparteId,
                ':usuario_id' => $_SESSION['usuario_id'],
                ':comentario' => $comentario,
                ':calificacion' => $calificacion
            ]);
            
            // Respuesta AJAX
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                jsonResponse([
                    'success' => true,
                    'message' => 'Comentario enviado. Será publicado después de ser revisado.'
                ]);
            }
            
            setFlashMessage(MSG_SUCCESS, 'Comentario enviado correctamente');
            redirect('/index.php?module=publico&action=detalle&id=' . $autoparteId);
            
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                jsonResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            setFlashMessage(MSG_ERROR, $e->getMessage());
            redirect($_SERVER['HTTP_REFERER'] ?? '/index.php');
        }
    }
    
    /**
     * Búsqueda de autopartes
     */
    public function buscar() {
        $q = $_GET['q'] ?? '';
        redirect('/index.php?module=publico&action=catalogo&buscar=' . urlencode($q));
    }
}
?>