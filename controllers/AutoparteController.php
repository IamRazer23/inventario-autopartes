<?php
/**
 * Controlador de Autopartes - MODIFICADO PARA URLs DE IMÁGENES
 * Maneja el CRUD completo del inventario de autopartes
 * Las imágenes se guardan como URLs externas, no se suben al servidor
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
            
            $filtros = [
                'buscar' => $_GET['buscar'] ?? '',
                'categoria_id' => $_GET['categoria'] ?? '',
                'seccion_id' => $_GET['seccion'] ?? '',
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
            
            $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $porPagina = ADMIN_ITEMS_PER_PAGE ?? 10;
            
            $filtros['limite'] = $porPagina;
            $filtros['offset'] = ($pagina - 1) * $porPagina;
            
            $autopartes = $this->autoparteModel->obtenerTodos($filtros);
            $totalAutopartes = $this->autoparteModel->contarTodos($filtros);
            $totalPaginas = ceil($totalAutopartes / $porPagina);
            
            $categorias = $this->obtenerCategorias();
            $secciones = $this->obtenerSecciones();
            $marcas = $this->autoparteModel->obtenerMarcas();
            $anios = $this->autoparteModel->obtenerAnios();
            
            $totalActivas = $this->autoparteModel->contarTodos(['estado' => 1]);
            $totalInactivas = $this->autoparteModel->contarTodos(['estado' => 0]);
            $totalStockBajo = $this->autoparteModel->contarTodos(['stock_bajo' => true]);
            $valorInventario = $this->autoparteModel->obtenerValorInventario();
            
            $pageTitle = 'Inventario de Autopartes - Admin';
            
            require_once VIEWS_PATH . '/admin/autopartes/index.php';
            
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
            $secciones = $this->obtenerSecciones();
            $marcas = $this->autoparteModel->obtenerMarcas();
            
            $pageTitle = 'Agregar Autoparte - Admin';
            
            require_once VIEWS_PATH . '/admin/autopartes/crear.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar formulario');
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Procesa la creación de una autoparte
     * MODIFICADO: Ahora acepta URLs de imágenes en lugar de subir archivos
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
            $seccion_id = Validator::sanitizeInt($_POST['seccion_id'] ?? 0);
            $estado = isset($_POST['estado']) ? 1 : 0;
            
            // URLs de imágenes (nuevo)
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
            
            $validator->required($anio, 'anio');
            $validator->validYear($anio, 'anio');
            
            $validator->required($precio, 'precio');
            $validator->numeric($precio, 'precio');
            
            $validator->required($stock, 'stock');
            $validator->numeric($stock, 'stock');
            
            $validator->required($categoria_id, 'categoria');
            
            // Validar URLs de imágenes (opcional pero si se proporciona debe ser válida)
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
            
            // Crear autoparte
            $this->autoparteModel->nombre = $nombre;
            $this->autoparteModel->descripcion = $descripcion;
            $this->autoparteModel->marca = $marca;
            $this->autoparteModel->modelo = $modelo;
            $this->autoparteModel->anio = $anio;
            $this->autoparteModel->precio = $precio;
            $this->autoparteModel->stock = $stock;
            $this->autoparteModel->categoria_id = $categoria_id;
            $this->autoparteModel->seccion_id = $seccion_id ?: null;
            $this->autoparteModel->thumbnail = $imagen_thumb;
            $this->autoparteModel->imagen_grande = $imagen_grande;
            $this->autoparteModel->estado = $estado;
            $this->autoparteModel->usuario_id = $_SESSION['usuario_id'];
            
            $autoparteId = $this->autoparteModel->crear();
            
            if ($autoparteId) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte agregada exitosamente');
                redirect('/index.php?module=admin&action=inventario');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al agregar la autoparte');
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=admin&action=autoparte-crear');
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al procesar: ' . $e->getMessage());
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
            $secciones = $this->obtenerSecciones();
            $marcas = $this->autoparteModel->obtenerMarcas();
            
            $pageTitle = 'Editar Autoparte - Admin';
            
            require_once VIEWS_PATH . '/admin/autopartes/editar.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar formulario');
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Procesa la actualización de una autoparte
     * MODIFICADO: Ahora acepta URLs de imágenes
     */
    public function update() {
        try {
            if (!hasPermission('inventario', 'actualizar')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para editar autopartes');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $id = Validator::sanitizeInt($_POST['id'] ?? 0);
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            // Obtener autoparte actual
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
            $seccion_id = Validator::sanitizeInt($_POST['seccion_id'] ?? 0);
            $estado = isset($_POST['estado']) ? 1 : 0;
            
            // URLs de imágenes (nuevo)
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
                redirect('/index.php?module=admin&action=autoparte-editar&id=' . $id);
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
            $this->autoparteModel->seccion_id = $seccion_id ?: null;
            $this->autoparteModel->thumbnail = $imagen_thumb;
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
     * Elimina una autoparte
     */
    public function eliminar() {
        try {
            if (!hasPermission('inventario', 'eliminar')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para eliminar autopartes');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $id = $_GET['id'] ?? $_POST['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparte) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            // Ya no necesitamos eliminar archivos locales porque usamos URLs
            
            $resultado = $this->autoparteModel->eliminar($id);
            
            if ($resultado) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte eliminada exitosamente');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al eliminar la autoparte');
            }
            
            redirect('/index.php?module=admin&action=inventario');
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al eliminar: ' . $e->getMessage());
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
            
            // Obtener historial de ventas
            $historialVentas = $this->autoparteModel->obtenerHistorialVentas($id);
            
            $pageTitle = $autoparte['nombre'] . ' - Detalle';
            
            require_once VIEWS_PATH . '/admin/autopartes/ver.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar detalle');
            redirect('/index.php?module=admin&action=inventario');
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
            
            // BOM para Excel
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Encabezados
            fputcsv($output, [
                'ID', 'Nombre', 'Descripción', 'Marca', 'Modelo', 'Año',
                'Precio', 'Stock', 'Categoría', 'Estado', 'Imagen Thumbnail',
                'Imagen Grande', 'Fecha Creación'
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
                    $item['thumbnail'] ?? '',
                    $item['imagen_grande'] ?? '',
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
            
            if ($resultado) {
                jsonResponse(['success' => true, 'message' => 'Stock actualizado', 'nuevo_stock' => $stock]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al actualizar']);
            }
            
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
     * Obtiene todas las secciones activas
     */
    private function obtenerSecciones() {
        $query = "SELECT id, nombre, descripcion FROM secciones WHERE estado = 1 ORDER BY nombre";
        return $this->db->fetchAll($query);
    }
}
?>