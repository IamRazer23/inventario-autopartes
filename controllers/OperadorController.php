<?php
/**
 * Controlador Operador
 * Maneja las acciones del operador en el sistema de inventario de autopartes
 * 
 * PERMISOS DEL OPERADOR:
 * ✓ Gestión de Inventario: Crear, Leer, Actualizar autopartes (NO eliminar)
 * ✓ Subir imágenes de autopartes
 * ✓ Buscar y filtrar autopartes
 * ✓ Gestión de Comentarios: Leer, Actualizar, Eliminar (NO crear)
 * ✓ Ver categorías (solo lectura)
 * ✓ Ver ventas (solo lectura)
 * 
 * RESTRICCIONES:
 * ✗ NO puede gestionar usuarios
 * ✗ NO puede gestionar roles ni permisos
 * ✗ NO puede crear, editar o eliminar categorías
 * ✗ NO puede eliminar autopartes
 * ✗ NO puede crear o eliminar ventas
 * ✗ NO tiene acceso a reportes ni estadísticas completas
 * 
 * @author Grupo 1SF131
 * @version 2.0
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
    
    private $db;
    private $autoparteModel;
    
    /**
     * Constructor - Verifica permisos de acceso
     */
    public function __construct() {
        // Verificar que sea operador o administrador
        if (!hasRole(ROL_OPERADOR) && !hasRole(ROL_ADMINISTRADOR)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado. Se requiere rol de operador.');
            redirect('/index.php?module=auth&action=login');
        }
        
        $this->db = Database::getInstance();
        $this->autoparteModel = new Autoparte();
    }
    
    // =========================================================================
    // DASHBOARD
    // =========================================================================
    
    /**
     * Dashboard del operador
     */
    public function dashboard() {
        try {
            // Estadísticas básicas del operador
            
            // Total de autopartes activas
            $queryAutopartes = "SELECT COUNT(*) as total FROM autopartes WHERE estado = 1";
            $totalAutopartes = $this->db->fetchOne($queryAutopartes)['total'];
            
            // Stock bajo (≤ 5 unidades)
            $queryStockBajo = "SELECT COUNT(*) as total FROM autopartes WHERE stock <= 5 AND estado = 1";
            $alertasStock = $this->db->fetchOne($queryStockBajo)['total'];
            
            // Ventas del día (solo lectura)
            $queryVentasHoy = "SELECT COUNT(*) as total FROM ventas WHERE DATE(fecha_venta) = CURDATE()";
            $ventasHoy = $this->db->fetchOne($queryVentasHoy)['total'];
            
            // Autopartes agregadas recientemente
            $queryRecientes = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.stock, a.thumbnail,
                c.nombre as categoria, a.fecha_creacion
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.estado = 1
                ORDER BY a.fecha_creacion DESC
                LIMIT 10";
            $autopartesRecientes = $this->db->fetchAll($queryRecientes);
            
            // Stock bajo detallado
            $queryStockBajoDetalle = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.stock, a.thumbnail,
                c.nombre as categoria
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.stock <= 5 AND a.estado = 1
                ORDER BY a.stock ASC
                LIMIT 15";
            $stockBajo = $this->db->fetchAll($queryStockBajoDetalle);
            
            // Total de categorías (solo lectura)
            $queryCategorias = "SELECT COUNT(*) as total FROM categorias WHERE estado = 1";
            $totalCategorias = $this->db->fetchOne($queryCategorias)['total'];
            
            // Comentarios pendientes de revisión
            $queryComentarios = "SELECT COUNT(*) as total FROM comentarios WHERE publicar = 0";
            $totalComentarios = $this->db->fetchOne($queryComentarios)['total'] ?? 0;
            
            // Variables para la vista
            $pageTitle = 'Panel de Operador - Sistema AutoPartes';
            
            // Incluir la vista
            require_once VIEWS_PATH . '/operador/dashboard.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar el panel: ' . $e->getMessage());
            redirect('/index.php?module=auth&action=login');
        }
    }
    
    // =========================================================================
    // GESTIÓN DE INVENTARIO DE AUTOPARTES
    // =========================================================================
    
    /**
     * Lista todas las autopartes con filtros
     * PERMISO: Leer inventario
     */
    public function inventario() {
        try {
            // Obtener filtros de la URL
            $filtros = [
                'buscar' => $_GET['buscar'] ?? '',
                'categoria_id' => $_GET['categoria'] ?? '',
                'marca' => $_GET['marca'] ?? '',
                'modelo' => $_GET['modelo'] ?? '',
                'anio' => $_GET['anio'] ?? '',
                'precio_min' => $_GET['precio_min'] ?? '',
                'precio_max' => $_GET['precio_max'] ?? '',
                'estado' => $_GET['estado'] ?? '',
                'stock_bajo' => isset($_GET['stock_bajo']),
                'orden' => $_GET['orden'] ?? 'fecha_creacion',
                'direccion' => $_GET['direccion'] ?? 'DESC'
            ];
            
            // Paginación
            $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $porPagina = ADMIN_ITEMS_PER_PAGE ?? 20;
            
            $filtros['limite'] = $porPagina;
            $filtros['offset'] = ($pagina - 1) * $porPagina;
            
            // Obtener datos
            $autopartes = $this->autoparteModel->obtenerTodos($filtros);
            $totalAutopartes = $this->autoparteModel->contarTodos($filtros);
            $totalPaginas = ceil($totalAutopartes / $porPagina);
            
            // Datos para filtros
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            $anios = $this->autoparteModel->obtenerAnios();
            
            // Estadísticas
            $totalActivas = $this->autoparteModel->contarTodos(['estado' => 1]);
            $totalInactivas = $this->autoparteModel->contarTodos(['estado' => 0]);
            $totalStockBajo = $this->autoparteModel->contarTodos(['stock_bajo' => true]);
            $valorInventario = $this->autoparteModel->obtenerValorInventario();
            
            $pageTitle = 'Inventario de Autopartes - Operador';
            $esOperador = true; // Flag para la vista
            
            require_once VIEWS_PATH . '/operador/inventario.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar inventario: ' . $e->getMessage());
            redirect('/index.php?module=operador&action=dashboard');
        }
    }
    
    /**
     * Ver detalle de una autoparte
     * PERMISO: Leer inventario
     */
    public function verAutoparte() {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=operador&action=inventario');
            }
            
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparte) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=operador&action=inventario');
            }
            
            // Obtener comentarios de la autoparte
            $comentarios = $this->obtenerComentariosAutoparte($id);
            
            $pageTitle = 'Detalle de Autoparte - ' . $autoparte['nombre'];
            $esOperador = true;

            require_once VIEWS_PATH . '/operador/ver_autoparte.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar autoparte');
            redirect('/index.php?module=operador&action=inventario');
        }
    }
    
    /**
     * Formulario de creación de autoparte
     * PERMISO: Crear inventario
     */
    public function crearAutoparte() {
        try {
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            
            $pageTitle = 'Agregar Autoparte - Operador';
            $esOperador = true;
            $returnUrl = '/index.php?module=operador&action=inventario';
            
            require_once VIEWS_PATH . '/operador/crear_autoparte.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar formulario');
            redirect('/index.php?module=operador&action=inventario');
        }
    }
    
    /**
     * Procesa la creación de una autoparte
     * PERMISO: Crear inventario
     */
    public function guardarAutoparte() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=operador&action=crear-autoparte');
            }
            
            // Sanitizar datos
            $nombre = Validator::sanitizeString($_POST['nombre'] ?? '');
            $descripcion = Validator::sanitizeString($_POST['descripcion'] ?? '');
            $marca = Validator::sanitizeString($_POST['marca'] ?? '');
            $modelo = Validator::sanitizeString($_POST['modelo'] ?? '');
            $anio = Validator::sanitizeInt($_POST['anio'] ?? 0);
            $precio = Validator::sanitizeFloat($_POST['precio'] ?? 0);
            $stock = Validator::sanitizeInt($_POST['stock'] ?? 0);
            $categoria_id = Validator::sanitizeInt($_POST['categoria_id'] ?? 0);
            $estado = isset($_POST['estado']) ? 1 : 0;
            
            // URLs de imágenes
            $imagen_thumb = trim($_POST['imagen_thumb_url'] ?? '');
            $imagen_grande = trim($_POST['imagen_grande_url'] ?? '');
            
            // Validaciones
            $validator = new Validator();
            
            $validator->required($nombre, 'nombre');
            $validator->minLength($nombre, 3, 'nombre');
            $validator->maxLength($nombre, 150, 'nombre');
            
            $validator->required($marca, 'marca');
            $validator->maxLength($marca, 50, 'marca');
            
            $validator->required($modelo, 'modelo');
            $validator->maxLength($modelo, 50, 'modelo');
            
            $validator->required($anio, 'año');
            $validator->validYear($anio, 'año');
            
            $validator->required($precio, 'precio');
            $validator->numeric($precio, 'precio');
            
            $validator->required($stock, 'stock');
            $validator->numeric($stock, 'stock');
            
            $validator->required($categoria_id, 'categoría');
            
            // Validar URLs de imágenes
            $erroresAdicionales = [];
            if (!empty($imagen_thumb) && !filter_var($imagen_thumb, FILTER_VALIDATE_URL)) {
                $erroresAdicionales['imagen_thumb_url'] = 'La URL del thumbnail no es válida';
            }
            
            if (!empty($imagen_grande) && !filter_var($imagen_grande, FILTER_VALIDATE_URL)) {
                $erroresAdicionales['imagen_grande_url'] = 'La URL de la imagen grande no es válida';
            }
            
            if ($validator->hasErrors() || !empty($erroresAdicionales)) {
                $_SESSION['errors'] = array_merge($validator->getErrors(), $erroresAdicionales);
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=operador&action=crear-autoparte');
            }
            
            // Crear autoparte
            $this->autoparteModel->nombre = $nombre;
            $this->autoparteModel->descripcion = $descripcion;
            $this->autoparteModel->marca = $marca;
            $this->autoparteModel->modelo = $modelo;
            $this->autoparteModel->anio = $anio;
            $this->autoparteModel->precio = $precio;
            $this->autoparteModel->stock = $stock;
            $this->autoparteModel->categoria_id = $categoria_id;
            $this->autoparteModel->thumbnail = $imagen_thumb;
            $this->autoparteModel->imagen_grande = $imagen_grande;
            $this->autoparteModel->estado = $estado;
            
            $autoparteId = $this->autoparteModel->crear();
            
            if ($autoparteId) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte agregada exitosamente');
                redirect('/index.php?module=operador&action=inventario');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al agregar la autoparte');
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=operador&action=crear-autoparte');
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al procesar: ' . $e->getMessage());
            redirect('/index.php?module=operador&action=crear-autoparte');
        }
    }
    
    /**
     * Formulario de edición de autoparte
     * PERMISO: Actualizar inventario
     */
    public function editarAutoparte() {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=operador&action=inventario');
            }
            
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparte) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=operador&action=inventario');
            }
            
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            
            $pageTitle = 'Editar Autoparte - Operador';
            $esOperador = true;
            $returnUrl = '/index.php?module=operador&action=inventario';

            // Pasar la autoparte a la vista de creación para reutilizar el formulario (modo edición)
            require_once VIEWS_PATH . '/operador/crear_autoparte.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar formulario');
            redirect('/index.php?module=operador&action=inventario');
        }
    }
    
    /**
     * Procesa la actualización de una autoparte
     * PERMISO: Actualizar inventario
     */
    public function actualizarAutoparte() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=operador&action=inventario');
            }
            
            $id = Validator::sanitizeInt($_POST['id'] ?? 0);
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=operador&action=inventario');
            }
            
            // Verificar que la autoparte existe
            $autoparteActual = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparteActual) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=operador&action=inventario');
            }
            
            // Sanitizar datos
            $nombre = Validator::sanitizeString($_POST['nombre'] ?? '');
            $descripcion = Validator::sanitizeString($_POST['descripcion'] ?? '');
            $marca = Validator::sanitizeString($_POST['marca'] ?? '');
            $modelo = Validator::sanitizeString($_POST['modelo'] ?? '');
            $anio = Validator::sanitizeInt($_POST['anio'] ?? 0);
            $precio = Validator::sanitizeFloat($_POST['precio'] ?? 0);
            $stock = Validator::sanitizeInt($_POST['stock'] ?? 0);
            $categoria_id = Validator::sanitizeInt($_POST['categoria_id'] ?? 0);
            $estado = isset($_POST['estado']) ? 1 : 0;
            
            // URLs de imágenes
            $imagen_thumb = trim($_POST['imagen_thumb_url'] ?? '');
            $imagen_grande = trim($_POST['imagen_grande_url'] ?? '');
            
            // Validaciones
            $validator = new Validator();
            
            $validator->required($nombre, 'nombre');
            $validator->minLength($nombre, 3, 'nombre');
            $validator->maxLength($nombre, 150, 'nombre');
            
            $validator->required($marca, 'marca');
            $validator->maxLength($marca, 50, 'marca');
            
            $validator->required($modelo, 'modelo');
            $validator->maxLength($modelo, 50, 'modelo');
            
            $validator->required($anio, 'año');
            $validator->validYear($anio, 'año');
            
            $validator->required($precio, 'precio');
            $validator->numeric($precio, 'precio');
            
            $validator->required($stock, 'stock');
            $validator->numeric($stock, 'stock');
            
            $validator->required($categoria_id, 'categoría');
            
            // Validar URLs de imágenes
            $erroresAdicionales = [];
            if (!empty($imagen_thumb) && !filter_var($imagen_thumb, FILTER_VALIDATE_URL)) {
                $erroresAdicionales['imagen_thumb_url'] = 'La URL del thumbnail no es válida';
            }
            
            if (!empty($imagen_grande) && !filter_var($imagen_grande, FILTER_VALIDATE_URL)) {
                $erroresAdicionales['imagen_grande_url'] = 'La URL de la imagen grande no es válida';
            }
            
            if ($validator->hasErrors() || !empty($erroresAdicionales)) {
                $_SESSION['errors'] = array_merge($validator->getErrors(), $erroresAdicionales);
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=operador&action=editar-autoparte&id=' . $id);
            }
            
            // Actualizar autoparte
            $this->autoparteModel->id = $id;
            $this->autoparteModel->nombre = $nombre;
            $this->autoparteModel->descripcion = $descripcion;
            $this->autoparteModel->marca = $marca;
            $this->autoparteModel->modelo = $modelo;
            $this->autoparteModel->anio = $anio;
            $this->autoparteModel->precio = $precio;
            $this->autoparteModel->stock = $stock;
            $this->autoparteModel->categoria_id = $categoria_id;
            $this->autoparteModel->thumbnail = $imagen_thumb;
            $this->autoparteModel->imagen_grande = $imagen_grande;
            $this->autoparteModel->estado = $estado;
            
            $resultado = $this->autoparteModel->actualizar();
            
            if ($resultado) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte actualizada exitosamente');
                redirect('/index.php?module=operador&action=inventario');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al actualizar la autoparte');
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=operador&action=editar-autoparte&id=' . $id);
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al procesar: ' . $e->getMessage());
            redirect('/index.php?module=operador&action=inventario');
        }
    }
    
    /**
     * Actualizar stock de una autoparte (método rápido)
     * PERMISO: Actualizar inventario
     */
    public function actualizarStock() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
            }
            
            $id = Validator::sanitizeInt($_POST['id'] ?? 0);
            $stock = Validator::sanitizeInt($_POST['stock'] ?? 0);
            
            if (!$id || $stock < 0) {
                jsonResponse(['success' => false, 'message' => 'Datos inválidos'], 400);
            }
            
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparte) {
                jsonResponse(['success' => false, 'message' => 'Autoparte no encontrada'], 404);
            }
            
            // Actualizar solo el stock
            $query = "UPDATE autopartes SET stock = :stock, fecha_actualizacion = NOW() WHERE id = :id";
            $resultado = $this->db->execute($query, [':stock' => $stock, ':id' => $id]);
            
            if ($resultado) {
                jsonResponse(['success' => true, 'message' => 'Stock actualizado correctamente', 'nuevo_stock' => $stock]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al actualizar stock'], 500);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Buscar autopartes (AJAX)
     * PERMISO: Leer inventario
     */
    public function buscarAutopartes() {
        try {
            $termino = $_GET['q'] ?? '';
            $categoria = $_GET['categoria'] ?? '';
            $marca = $_GET['marca'] ?? '';
            
            $filtros = [
                'buscar' => $termino,
                'estado' => 1,
                'limite' => 20
            ];
            
            if ($categoria) {
                $filtros['categoria_id'] = $categoria;
            }
            
            if ($marca) {
                $filtros['marca'] = $marca;
            }
            
            $resultados = $this->autoparteModel->obtenerTodos($filtros);
            
            jsonResponse(['success' => true, 'message' => 'Búsqueda completada', 'data' => $resultados]);
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => 'Error en búsqueda: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Ver stock bajo
     * PERMISO: Leer inventario
     */
    public function stockBajo() {
        try {
            $umbral = $_GET['umbral'] ?? 5;
            
            $query = "SELECT 
                a.id, a.nombre, a.marca, a.modelo, a.stock, a.precio,
                a.thumbnail, c.nombre as categoria
                FROM autopartes a
                INNER JOIN categorias c ON a.categoria_id = c.id
                WHERE a.stock <= :umbral AND a.estado = 1
                ORDER BY a.stock ASC";
            
            $stockBajo = $this->db->fetchAll($query, [':umbral' => $umbral]);
            
            $pageTitle = 'Alertas de Stock Bajo - Operador';
            $esOperador = true;
            
            require_once VIEWS_PATH . '/admin/stock_bajo.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar alertas de stock');
            redirect('/index.php?module=operador&action=dashboard');
        }
    }
    
    // =========================================================================
    // GESTIÓN DE COMENTARIOS (Limitada)
    // =========================================================================
    
    /**
     * Lista los comentarios
     * PERMISO: Leer comentarios
     */
    public function comentarios() {
        try {
            $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $porPagina = ADMIN_ITEMS_PER_PAGE ?? 20;
            $offset = ($pagina - 1) * $porPagina;
            
            $filtros = [
                'autoparte_id' => $_GET['autoparte'] ?? '',
                'estado' => $_GET['estado'] ?? '',
                'buscar' => $_GET['buscar'] ?? ''
            ];
            
            // Obtener comentarios con información de autoparte y usuario
            $query = "SELECT c.*, 
                        a.nombre as autoparte_nombre,
                        u.nombre as usuario_nombre,
                        u.email as usuario_email
                     FROM comentarios c
                     LEFT JOIN autopartes a ON c.autoparte_id = a.id
                     LEFT JOIN usuarios u ON c.usuario_id = u.id
                     WHERE 1=1";
            
            $params = [];
            
            if (!empty($filtros['autoparte_id'])) {
                $query .= " AND c.autoparte_id = :autoparte_id";
                $params[':autoparte_id'] = $filtros['autoparte_id'];
            }
            
            if ($filtros['estado'] !== '') {
                $query .= " AND c.publicar = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (!empty($filtros['buscar'])) {
                $query .= " AND (c.comentario LIKE :buscar OR c.nombre_usuario LIKE :buscar2 OR u.nombre LIKE :buscar3)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar2'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar3'] = '%' . $filtros['buscar'] . '%';
            }
            
            // Contar total
            $queryCount = str_replace('c.*, ', 'COUNT(*) as total, ', $query);
            $queryCount = preg_replace('/a\.nombre.*u\.email/', '1', $queryCount);
            $totalComentarios = $this->db->fetchOne($queryCount, $params)['total'] ?? 0;
            $totalPaginas = ceil($totalComentarios / $porPagina);
            
            // Agregar orden y límite
            $query .= " ORDER BY c.fecha_creacion DESC LIMIT :limite OFFSET :offset";
            $params[':limite'] = $porPagina;
            $params[':offset'] = $offset;
            
            $comentarios = $this->db->fetchAll($query, $params);
            
            $pageTitle = 'Gestión de Comentarios - Operador';
            $esOperador = true;
            
            require_once VIEWS_PATH . '/operador/comentarios.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar comentarios: ' . $e->getMessage());
            redirect('/index.php?module=operador&action=dashboard');
        }
    }
    
    /**
     * Ver detalle de un comentario
     * PERMISO: Leer comentarios
     */
    public function verComentario() {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Comentario no encontrado');
                redirect('/index.php?module=operador&action=comentarios');
            }
            
            $query = "SELECT c.*, 
                        a.nombre as autoparte_nombre,
                        a.thumbnail as autoparte_imagen,
                        u.nombre as usuario_nombre,
                        u.email as usuario_email
                     FROM comentarios c
                     LEFT JOIN autopartes a ON c.autoparte_id = a.id
                     LEFT JOIN usuarios u ON c.usuario_id = u.id
                     WHERE c.id = :id";
            
            $comentario = $this->db->fetchOne($query, [':id' => $id]);
            
            if (!$comentario) {
                setFlashMessage(MSG_ERROR, 'Comentario no encontrado');
                redirect('/index.php?module=operador&action=comentarios');
            }
            
            $pageTitle = 'Detalle de Comentario - Operador';
            $esOperador = true;
            
            require_once VIEWS_PATH . '/operador/comentario_detalle.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar comentario');
            redirect('/index.php?module=operador&action=comentarios');
        }
    }
    
    /**
     * Actualizar estado de un comentario
     * PERMISO: Actualizar comentarios
     */
    public function actualizarComentario() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=operador&action=comentarios');
            }
            
            $id = Validator::sanitizeInt($_POST['id'] ?? 0);
            $publicar = isset($_POST['publicar']) ? 1 : 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Comentario no encontrado');
                redirect('/index.php?module=operador&action=comentarios');
            }
            
            $query = "UPDATE comentarios SET publicar = :publicar WHERE id = :id";
            
            $resultado = $this->db->execute($query, [
                ':publicar' => $publicar,
                ':id' => $id
            ]);
            
            if ($resultado) {
                setFlashMessage(MSG_SUCCESS, 'Comentario actualizado exitosamente');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al actualizar comentario');
            }
            
            redirect('/index.php?module=operador&action=comentarios');
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error: ' . $e->getMessage());
            redirect('/index.php?module=operador&action=comentarios');
        }
    }
    
    /**
     * Eliminar un comentario
     * PERMISO: Eliminar comentarios
     */
    public function eliminarComentario() {
        try {
            $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Comentario no encontrado');
                redirect('/index.php?module=operador&action=comentarios');
            }
            
            // Verificar que el comentario existe
            $query = "SELECT id FROM comentarios WHERE id = :id";
            $comentario = $this->db->fetchOne($query, [':id' => $id]);
            
            if (!$comentario) {
                setFlashMessage(MSG_ERROR, 'Comentario no encontrado');
                redirect('/index.php?module=operador&action=comentarios');
            }
            
            // Eliminar comentario (soft delete o hard delete según preferencia)
            $queryDelete = "DELETE FROM comentarios WHERE id = :id";
            $resultado = $this->db->execute($queryDelete, [':id' => $id]);
            
            if ($resultado) {
                setFlashMessage(MSG_SUCCESS, 'Comentario eliminado exitosamente');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al eliminar comentario');
            }
            
            redirect('/index.php?module=operador&action=comentarios');
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error: ' . $e->getMessage());
            redirect('/index.php?module=operador&action=comentarios');
        }
    }
    
    // =========================================================================
    // CONSULTAS (Solo Lectura)
    // =========================================================================
    
    /**
     * Ver categorías (solo lectura)
     * PERMISO: Solo lectura de categorías
     */
    public function categorias() {
        try {
            $query = "SELECT c.id, c.nombre, c.descripcion, c.imagen, c.estado, c.fecha_creacion,
                        (SELECT COUNT(*) FROM autopartes WHERE categoria_id = c.id AND estado = 1) as total_autopartes
                     FROM categorias c 
                     WHERE c.estado = 1 
                     ORDER BY c.nombre ASC";
            
            $categorias = $this->db->fetchAll($query);
            
            $pageTitle = 'Categorías - Operador (Solo Lectura)';
            $esOperador = true;
            $soloLectura = true;
            
            require_once VIEWS_PATH . '/operador/categorias.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar categorías');
            redirect('/index.php?module=operador&action=dashboard');
        }
    }
    
    /**
     * Ver ventas (solo lectura)
     * PERMISO: Solo lectura de ventas
     */
    public function ventas() {
        try {
            $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $porPagina = ADMIN_ITEMS_PER_PAGE ?? 20;
            $offset = ($pagina - 1) * $porPagina;
            
            $filtros = [
                'fecha_inicio' => $_GET['fecha_inicio'] ?? '',
                'fecha_fin' => $_GET['fecha_fin'] ?? '',
                'buscar' => $_GET['buscar'] ?? ''
            ];
            
            $query = "SELECT v.*, 
                        u.nombre as cliente_nombre,
                        u.email as cliente_email,
                        (SELECT COUNT(*) FROM detalle_venta WHERE venta_id = v.id) as total_items
                     FROM ventas v
                     LEFT JOIN usuarios u ON v.usuario_id = u.id
                     WHERE 1=1";
            
            $params = [];
            
            if (!empty($filtros['fecha_inicio'])) {
                $query .= " AND DATE(v.fecha_venta) >= :fecha_inicio";
                $params[':fecha_inicio'] = $filtros['fecha_inicio'];
            }
            
            if (!empty($filtros['fecha_fin'])) {
                $query .= " AND DATE(v.fecha_venta) <= :fecha_fin";
                $params[':fecha_fin'] = $filtros['fecha_fin'];
            }
            
            if (!empty($filtros['buscar'])) {
                $query .= " AND (u.nombre LIKE :buscar OR v.id LIKE :buscar2)";
                $params[':buscar'] = '%' . $filtros['buscar'] . '%';
                $params[':buscar2'] = '%' . $filtros['buscar'] . '%';
            }
            
            // Contar total
            $queryCount = "SELECT COUNT(*) as total FROM ventas v LEFT JOIN usuarios u ON v.usuario_id = u.id WHERE 1=1";
            if (!empty($filtros['fecha_inicio'])) {
                $queryCount .= " AND DATE(v.fecha_venta) >= :fecha_inicio";
            }
            if (!empty($filtros['fecha_fin'])) {
                $queryCount .= " AND DATE(v.fecha_venta) <= :fecha_fin";
            }
            if (!empty($filtros['buscar'])) {
                $queryCount .= " AND (u.nombre LIKE :buscar OR v.id LIKE :buscar2)";
            }
            
            $totalVentas = $this->db->fetchOne($queryCount, $params)['total'] ?? 0;
            $totalPaginas = ceil($totalVentas / $porPagina);
            
            // Agregar orden y límite
            $query .= " ORDER BY v.fecha_venta DESC LIMIT :limite OFFSET :offset";
            $params[':limite'] = $porPagina;
            $params[':offset'] = $offset;
            
            $ventas = $this->db->fetchAll($query, $params);
            
            $pageTitle = 'Historial de Ventas - Operador (Solo Lectura)';
            $esOperador = true;
            $soloLectura = true;
            
            require_once VIEWS_PATH . '/operador/ventas.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar ventas: ' . $e->getMessage());
            redirect('/index.php?module=operador&action=dashboard');
        }
    }
    
    /**
     * Ver detalle de una venta (solo lectura)
     * PERMISO: Solo lectura de ventas
     */
    public function verVenta() {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Venta no encontrada');
                redirect('/index.php?module=operador&action=ventas');
            }
            
            // Obtener datos de la venta
            $queryVenta = "SELECT v.*, 
                            u.nombre as cliente_nombre,
                            u.email as cliente_email
                          FROM ventas v
                          LEFT JOIN usuarios u ON v.usuario_id = u.id
                          WHERE v.id = :id";
            
            $venta = $this->db->fetchOne($queryVenta, [':id' => $id]);
            
            if (!$venta) {
                setFlashMessage(MSG_ERROR, 'Venta no encontrada');
                redirect('/index.php?module=operador&action=ventas');
            }
            
            // Obtener detalles de la venta
            $queryDetalles = "SELECT dv.*, 
                               a.nombre as autoparte_nombre,
                               a.marca, a.modelo, a.thumbnail
                             FROM detalle_venta dv
                             LEFT JOIN autopartes a ON dv.autoparte_id = a.id
                             WHERE dv.venta_id = :venta_id";
            
            $detalles = $this->db->fetchAll($queryDetalles, [':venta_id' => $id]);
            
            $pageTitle = 'Detalle de Venta #' . $id . ' - Operador';
            $esOperador = true;
            $soloLectura = true;
            
            require_once VIEWS_PATH . '/operador/venta_detalle.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar venta');
            redirect('/index.php?module=operador&action=ventas');
        }
    }
    
    // =========================================================================
    // PERFIL DEL OPERADOR
    // =========================================================================
    
    /**
     * Ver perfil del operador
     */
    public function perfil() {
        try {
            $usuarioId = $_SESSION['usuario_id'];
            
            $query = "SELECT u.*, r.nombre as rol_nombre 
                     FROM usuarios u
                     LEFT JOIN roles r ON u.rol_id = r.id
                     WHERE u.id = :id";
            
            $usuario = $this->db->fetchOne($query, [':id' => $usuarioId]);
            
            if (!$usuario) {
                setFlashMessage(MSG_ERROR, 'Usuario no encontrado');
                redirect('/index.php?module=operador&action=dashboard');
            }
            
            $pageTitle = 'Mi Perfil - Operador';
            $esOperador = true;
            
            require_once VIEWS_PATH . '/operador/perfil.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar perfil');
            redirect('/index.php?module=operador&action=dashboard');
        }
    }
    
    /**
     * Actualizar perfil del operador
     */
    public function actualizarPerfil() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=operador&action=perfil');
            }
            
            $usuarioId = $_SESSION['usuario_id'];
            
            $nombre = Validator::sanitizeString($_POST['nombre'] ?? '');
            $email = Validator::sanitizeEmail($_POST['email'] ?? '');
            $telefono = Validator::sanitizeString($_POST['telefono'] ?? '');
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            
            // Validaciones
            $validator = new Validator();
            
            $validator->required($nombre, 'nombre');
            $validator->minLength($nombre, 3, 'nombre');
            
            $validator->required($email, 'email');
            $validator->email($email, 'email');
            
            // Verificar que el email no esté en uso por otro usuario
            $queryEmail = "SELECT id FROM usuarios WHERE email = :email AND id != :id";
            $existeEmail = $this->db->fetchOne($queryEmail, [':email' => $email, ':id' => $usuarioId]);
            
            $erroresAdicionales = [];
            if ($existeEmail) {
                $erroresAdicionales['email'] = 'Este email ya está registrado';
            }
            
            // Validar contraseña si se proporciona
            if (!empty($password)) {
                $validator->minLength($password, 6, 'contraseña');
                
                if ($password !== $passwordConfirm) {
                    $erroresAdicionales['password_confirm'] = 'Las contraseñas no coinciden';
                }
            }
            
            if ($validator->hasErrors() || !empty($erroresAdicionales)) {
                $_SESSION['errors'] = array_merge($validator->getErrors(), $erroresAdicionales);
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=operador&action=perfil');
            }
            
            // Actualizar usuario
            $query = "UPDATE usuarios SET 
                        nombre = :nombre,
                        email = :email,
                        telefono = :telefono,
                        fecha_actualizacion = NOW()";
            
            $params = [
                ':nombre' => $nombre,
                ':email' => $email,
                ':telefono' => $telefono,
                ':id' => $usuarioId
            ];
            
            // Agregar contraseña si se proporciona
            if (!empty($password)) {
                $query .= ", password = :password";
                $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            
            $query .= " WHERE id = :id";
            
            $resultado = $this->db->execute($query, $params);
            
            if ($resultado) {
                // Actualizar sesión
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_email'] = $email;
                
                setFlashMessage(MSG_SUCCESS, 'Perfil actualizado exitosamente');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al actualizar perfil');
            }
            
            redirect('/index.php?module=operador&action=perfil');
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error: ' . $e->getMessage());
            redirect('/index.php?module=operador&action=perfil');
        }
    }
    
    // =========================================================================
    // MÉTODOS AUXILIARES PRIVADOS
    // =========================================================================
    
    /**
     * Obtiene las categorías activas (solo lectura)
     * 
     * @return array
     */
    private function obtenerCategorias() {
        $query = "SELECT id, nombre FROM categorias WHERE estado = 1 ORDER BY nombre ASC";
        return $this->db->fetchAll($query);
    }
    

    
    /**
     * Obtiene comentarios de una autoparte
     * 
     * @param int $autoparteId
     * @return array
     */
    private function obtenerComentariosAutoparte($autoparteId) {
        try {
            $query = "SELECT c.*, u.nombre as usuario_nombre 
                     FROM comentarios c
                     LEFT JOIN usuarios u ON c.usuario_id = u.id
                     WHERE c.autoparte_id = :autoparte_id
                     ORDER BY c.fecha_creacion DESC";
            
            return $this->db->fetchAll($query, [':autoparte_id' => $autoparteId]);
        } catch (Exception $e) {
            return [];
        }
    }
    
    // =========================================================================
    // RESTRICCIONES - Métodos que NO puede ejecutar el operador
    // =========================================================================
    
    /**
     * Bloquea acceso a gestión de usuarios
     */
    public function usuarios() {
        setFlashMessage(MSG_ERROR, 'No tienes permisos para gestionar usuarios');
        redirect('/index.php?module=operador&action=dashboard');
    }
    
    /**
     * Bloquea eliminación de autopartes
     */
    public function eliminarAutoparte() {
        setFlashMessage(MSG_ERROR, 'No tienes permisos para eliminar autopartes');
        redirect('/index.php?module=operador&action=inventario');
    }
    
    /**
     * Bloquea creación de categorías
     */
    public function crearCategoria() {
        setFlashMessage(MSG_ERROR, 'No tienes permisos para crear categorías');
        redirect('/index.php?module=operador&action=categorias');
    }
    
    /**
     * Bloquea edición de categorías
     */
    public function editarCategoria() {
        setFlashMessage(MSG_ERROR, 'No tienes permisos para editar categorías');
        redirect('/index.php?module=operador&action=categorias');
    }
    
    /**
     * Bloquea eliminación de categorías
     */
    public function eliminarCategoria() {
        setFlashMessage(MSG_ERROR, 'No tienes permisos para eliminar categorías');
        redirect('/index.php?module=operador&action=categorias');
    }
    
    /**
     * Bloquea acceso a estadísticas completas
     */
    public function estadisticas() {
        setFlashMessage(MSG_ERROR, 'No tienes permisos para ver estadísticas completas');
        redirect('/index.php?module=operador&action=dashboard');
    }
    
    /**
     * Bloquea acceso a reportes
     */
    public function reportes() {
        setFlashMessage(MSG_ERROR, 'No tienes permisos para generar reportes');
        redirect('/index.php?module=operador&action=dashboard');
    }
}
?>