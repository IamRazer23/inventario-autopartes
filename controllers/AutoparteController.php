<?php
/**
 * Controlador de Autopartes - CORREGIDO
 * Las imágenes se guardan como URLs externas
 * 
 * CAMBIOS:
 * - Corregido: usar $this->autoparteModel->thumbnail en lugar de thumbnail
 * - Limpieza de filtros vacíos antes de pasarlos al modelo
 * 
 * @author Grupo 1SF131
 * @version 2.1
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

class AutoparteController {
    
    private $autoparteModel;
    private $db;
    
    public function __construct() {
        if (!hasRole(ROL_ADMINISTRADOR) && !hasRole(ROL_OPERADOR)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado');
            redirect('/index.php?module=auth&action=login');
        }
        
        $this->autoparteModel = new Autoparte();
        $this->db = Database::getInstance();
    }
    
    /**
     * Lista todas las autopartes con filtros
     */
    public function index() {
        try {
            if (!hasPermission('inventario', 'leer')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para ver el inventario');
                redirect('/index.php?module=admin&action=dashboard');
            }
            
            // Obtener filtros de la URL
            $filtros = [
                'buscar' => trim($_GET['buscar'] ?? ''),
                'categoria_id' => $_GET['categoria'] ?? '',
                'marca' => $_GET['marca'] ?? '',
                'modelo' => $_GET['modelo'] ?? '',
                'anio' => $_GET['anio'] ?? '',
                'precio_min' => $_GET['precio_min'] ?? '',
                'precio_max' => $_GET['precio_max'] ?? '',
                'estado' => $_GET['estado'] ?? '',
                'stock_bajo' => isset($_GET['stock_bajo']) && $_GET['stock_bajo'] ? true : false,
                'orden' => $_GET['orden'] ?? 'fecha_creacion',
                'direccion' => $_GET['direccion'] ?? 'DESC'
            ];
            
            // Paginación
            $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $porPagina = defined('ADMIN_ITEMS_PER_PAGE') ? ADMIN_ITEMS_PER_PAGE : 12;
            
            $filtros['limite'] = $porPagina;
            $filtros['offset'] = ($pagina - 1) * $porPagina;
            
            // Obtener autopartes
            $autopartes = $this->autoparteModel->obtenerTodos($filtros);
            
            // Contar total para paginación (sin límite ni offset)
            $filtrosConteo = $filtros;
            unset($filtrosConteo['limite'], $filtrosConteo['offset']);
            $totalAutopartes = $this->autoparteModel->contarTodos($filtrosConteo);
            $totalPaginas = ceil($totalAutopartes / $porPagina);
            
            // Obtener datos para filtros
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            $anios = $this->autoparteModel->obtenerAnios();
            
            // Estadísticas
            $totalActivas = $this->autoparteModel->contarTodos(['estado' => 1]);
            $totalInactivas = $this->autoparteModel->contarTodos(['estado' => 0]);
            $totalStockBajo = $this->autoparteModel->contarTodos(['stock_bajo' => true, 'estado' => 1]);
            $valorInventario = $this->autoparteModel->obtenerValorInventario();
            
            $pageTitle = 'Inventario de Autopartes - Admin';
            
            require_once VIEWS_PATH . '/admin/inventario/index.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar inventario: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=dashboard');
        }
    }
    
    /**
     * Muestra formulario de creación
     */
    public function crear() {
        try {
            if (!hasPermission('inventario', 'crear')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para agregar autopartes');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            
            $pageTitle = 'Agregar Autoparte - Admin';
            
            require_once VIEWS_PATH . '/admin/inventario/crear.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar formulario');
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Procesa la creación de una autoparte
     */
    public function store() {
        try {
            if (!hasPermission('inventario', 'crear')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para agregar autopartes');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=admin&action=autoparte-crear');
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
            $imagen_thumb = trim($_POST['imagen_thumb_url'] ?? $_POST['thumbnail'] ?? '');
            $imagen_grande = trim($_POST['imagen_grande_url'] ?? $_POST['imagen_grande'] ?? '');
            
            // Validaciones
            $validator = new Validator();
            
            $validator->required($nombre, 'nombre');
            $validator->minLength($nombre, 3, 'nombre');
            $validator->maxLength($nombre, 150, 'nombre');
            
            $validator->required($marca, 'marca');
            $validator->maxLength($marca, 50, 'marca');
            
            $validator->required($modelo, 'modelo');
            $validator->maxLength($modelo, 50, 'modelo');
            
            $validator->required($anio, 'anio');
            $validator->validYear($anio, 'anio');
            
            $validator->required($precio, 'precio');
            $validator->numeric($precio, 'precio');
            
            $validator->required($stock, 'stock');
            $validator->numeric($stock, 'stock');
            
            $validator->required($categoria_id, 'categoria');
            
            // Validar URLs de imágenes
            if (!empty($imagen_thumb) && !filter_var($imagen_thumb, FILTER_VALIDATE_URL)) {
                $validator->addError('imagen_thumb_url', 'La URL del thumbnail no es válida');
            }
            
            if (!empty($imagen_grande) && !filter_var($imagen_grande, FILTER_VALIDATE_URL)) {
                $validator->addError('imagen_grande_url', 'La URL de la imagen grande no es válida');
            }
            
            if ($validator->hasErrors()) {
                $_SESSION['errors'] = $validator->getErrors();
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=admin&action=autoparte-crear');
            }
            
            // Crear autoparte - CORREGIDO: usar nombres correctos
            $this->autoparteModel->nombre = $nombre;
            $this->autoparteModel->descripcion = $descripcion;
            $this->autoparteModel->marca = $marca;
            $this->autoparteModel->modelo = $modelo;
            $this->autoparteModel->anio = $anio;
            $this->autoparteModel->precio = $precio;
            $this->autoparteModel->stock = $stock;
            $this->autoparteModel->categoria_id = $categoria_id;
            $this->autoparteModel->thumbnail = $imagen_thumb;      // CORREGIDO
            $this->autoparteModel->imagen_grande = $imagen_grande;
            $this->autoparteModel->estado = $estado;
            
            $autoparteId = $this->autoparteModel->crear();
            
            if ($autoparteId) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte agregada exitosamente');
                redirect('/index.php?module=admin&action=inventario');
            } else {
                $errors = $this->autoparteModel->getErrors();
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=admin&action=autoparte-crear');
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al crear autoparte: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=autoparte-crear');
        }
    }
    
    /**
     * Muestra formulario de edición
     */
    public function editar() {
        try {
            if (!hasPermission('inventario', 'actualizar')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para editar autopartes');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparte) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            
            $pageTitle = 'Editar Autoparte - Admin';
            
            require_once VIEWS_PATH . '/admin/inventario/editar.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar autoparte');
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Procesa la actualización de una autoparte
     */
    public function update() {
        try {
            if (!hasPermission('inventario', 'actualizar')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para actualizar autopartes');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no válida');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $autoparteActual = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparteActual) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
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
            $imagen_thumb = trim($_POST['imagen_thumb_url'] ?? $_POST['thumbnail'] ?? '');
            $imagen_grande = trim($_POST['imagen_grande_url'] ?? $_POST['imagen_grande'] ?? '');
            
            // Si no hay nueva imagen, mantener la anterior
            if (empty($imagen_thumb)) {
                $imagen_thumb = $autoparteActual['thumbnail'] ?? '';
            }
            if (empty($imagen_grande)) {
                $imagen_grande = $autoparteActual['imagen_grande'] ?? '';
            }
            
            // Validaciones
            $validator = new Validator();
            
            $validator->required($nombre, 'nombre');
            $validator->minLength($nombre, 3, 'nombre');
            $validator->required($marca, 'marca');
            $validator->required($modelo, 'modelo');
            $validator->required($anio, 'anio');
            $validator->required($precio, 'precio');
            $validator->required($stock, 'stock');
            $validator->required($categoria_id, 'categoria');
            
            if ($validator->hasErrors()) {
                $_SESSION['errors'] = $validator->getErrors();
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=admin&action=autoparte-editar&id=' . $id);
            }
            
            // Actualizar autoparte - CORREGIDO
            $this->autoparteModel->id = $id;
            $this->autoparteModel->nombre = $nombre;
            $this->autoparteModel->descripcion = $descripcion;
            $this->autoparteModel->marca = $marca;
            $this->autoparteModel->modelo = $modelo;
            $this->autoparteModel->anio = $anio;
            $this->autoparteModel->precio = $precio;
            $this->autoparteModel->stock = $stock;
            $this->autoparteModel->categoria_id = $categoria_id;
            $this->autoparteModel->thumbnail = $imagen_thumb;      // CORREGIDO
            $this->autoparteModel->imagen_grande = $imagen_grande;
            $this->autoparteModel->estado = $estado;
            
            $resultado = $this->autoparteModel->actualizar();
            
            if ($resultado) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte actualizada exitosamente');
                redirect('/index.php?module=admin&action=inventario');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al actualizar la autoparte');
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=admin&action=autoparte-editar&id=' . $id);
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al procesar: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Elimina (desactiva) una autoparte
     */
    public function eliminar() {
        try {
            if (!hasPermission('inventario', 'eliminar')) {
                if ($this->isAjax()) {
                    jsonResponse(['success' => false, 'message' => 'Sin permiso']);
                }
                setFlashMessage(MSG_ERROR, 'No tienes permiso para eliminar autopartes');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            
            if (!$id) {
                if ($this->isAjax()) {
                    jsonResponse(['success' => false, 'message' => 'ID no válido']);
                }
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $resultado = $this->autoparteModel->eliminar($id);
            
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => $resultado,
                    'message' => $resultado ? 'Autoparte desactivada' : 'Error al desactivar'
                ]);
            }
            
            if ($resultado) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte eliminada exitosamente');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al eliminar la autoparte');
            }
            
            redirect('/index.php?module=admin&action=inventario');
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }
            setFlashMessage(MSG_ERROR, 'Error al eliminar: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Activa una autoparte
     */
    public function activar() {
        try {
            if (!hasPermission('inventario', 'actualizar')) {
                if ($this->isAjax()) {
                    jsonResponse(['success' => false, 'message' => 'Sin permiso']);
                }
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            
            if (!$id) {
                if ($this->isAjax()) {
                    jsonResponse(['success' => false, 'message' => 'ID no válido']);
                }
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $resultado = $this->autoparteModel->activar($id);
            
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => $resultado,
                    'message' => $resultado ? 'Autoparte activada' : 'Error al activar'
                ]);
            }
            
            if ($resultado) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte activada exitosamente');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al activar la autoparte');
            }
            
            redirect('/index.php?module=admin&action=inventario');
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                jsonResponse(['success' => false, 'message' => $e->getMessage()]);
            }
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Ver detalle de autoparte
     */
    public function ver() {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparte) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $historialVentas = $this->autoparteModel->obtenerHistorialVentas($id);
            
            $pageTitle = $autoparte['nombre'] . ' - Detalle';
            
            require_once VIEWS_PATH . '/admin/inventario/ver.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar detalle');
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Detalle de autoparte (AJAX)
     */
    public function detalle() {
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'ID no válido']);
            }
            
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparte) {
                jsonResponse(['success' => false, 'message' => 'Autoparte no encontrada']);
            }
            
            jsonResponse([
                'success' => true,
                'autoparte' => $autoparte
            ]);
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Exportar inventario a CSV
     */
    public function exportar() {
        try {
            if (!hasPermission('inventario', 'leer')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para exportar');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $autopartes = $this->autoparteModel->obtenerTodos(['limite' => 10000]);
            
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=inventario_' . date('Y-m-d_H-i-s') . '.csv');
            
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($output, [
                'ID', 'Nombre', 'Descripción', 'Marca', 'Modelo', 'Año',
                'Precio', 'Stock', 'Categoría', 'Estado', 'Fecha Creación'
            ], ';');
            
            foreach ($autopartes as $item) {
                fputcsv($output, [
                    $item['id'],
                    $item['nombre'],
                    $item['descripcion'] ?? '',
                    $item['marca'],
                    $item['modelo'],
                    $item['anio'],
                    $item['precio'],
                    $item['stock'],
                    $item['categoria_nombre'] ?? '',
                    $item['estado'] == 1 ? 'Activo' : 'Inactivo',
                    $item['fecha_creacion']
                ], ';');
            }
            
            fclose($output);
            exit;
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al exportar: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Actualización rápida de stock (AJAX)
     */
    public function actualizarStock() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            }
            
            if (!hasPermission('inventario', 'actualizar')) {
                jsonResponse(['success' => false, 'message' => 'Sin permiso']);
            }
            
            $id = Validator::sanitizeInt($_POST['id'] ?? 0);
            $stock = Validator::sanitizeInt($_POST['stock'] ?? 0);
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'ID inválido']);
            }
            
            if ($stock < 0) {
                jsonResponse(['success' => false, 'message' => 'El stock no puede ser negativo']);
            }
            
            $resultado = $this->autoparteModel->actualizarStock($id, $stock);
            
            jsonResponse([
                'success' => $resultado,
                'message' => $resultado ? 'Stock actualizado' : 'Error al actualizar',
                'nuevo_stock' => $stock
            ]);
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Obtiene todas las categorías activas
     */
    private function obtenerCategorias() {
        $query = "SELECT id, nombre, descripcion FROM categorias WHERE estado = 1 ORDER BY nombre";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Verifica si es una petición AJAX
     */
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}
?>