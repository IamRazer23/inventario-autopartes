<?php
/**
 * Controlador de Autopartes
 * Maneja el CRUD completo del inventario de autopartes
 */

// Cargar dependencias necesarias
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
        // Verificar que sea administrador u operador
        if (!hasRole(ROL_ADMINISTRADOR) && !hasRole(ROL_OPERADOR)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado');
            redirect('/index.php?module=auth&action=login');
        }
        
        $this->autoparteModel = new Autoparte();
        $this->db = Database::getInstance();
    }
    
    /**
     * Lista todas las autopartes con filtros
     * Cumple con requisito 7: El módulo debe permitir consultas
     */
    public function index() {
        try {
            // Verificar permiso
            if (!hasPermission('inventario', 'leer')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para ver el inventario');
                redirect('/index.php?module=admin&action=dashboard');
            }
            
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
            $porPagina = ADMIN_ITEMS_PER_PAGE ?? 10;
            
            $filtros['limite'] = $porPagina;
            $filtros['offset'] = ($pagina - 1) * $porPagina;
            
            // Obtener autopartes
            $autopartes = $this->autoparteModel->obtenerTodos($filtros);
            
            // Contar total para paginación
            $totalAutopartes = $this->autoparteModel->contarTodos($filtros);
            $totalPaginas = ceil($totalAutopartes / $porPagina);
            
            // Obtener datos para filtros
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            $anios = $this->autoparteModel->obtenerAnios();
            
            // Estadísticas
            $totalActivas = $this->autoparteModel->contarTodos(['estado' => 1]);
            $totalInactivas = $this->autoparteModel->contarTodos(['estado' => 0]);
            $totalStockBajo = $this->autoparteModel->contarTodos(['stock_bajo' => true]);
            $valorInventario = $this->autoparteModel->obtenerValorInventario();
            
            // Variables para la vista
            $pageTitle = 'Inventario de Autopartes - Admin';
            $breadcrumbs = [
                ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
                ['text' => 'Inventario', 'url' => '']
            ];
            
            // Incluir vista
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
            // Verificar permiso
            if (!hasPermission('inventario', 'crear')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para agregar autopartes');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            // Obtener datos para el formulario
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            
            // Variables para la vista
            $pageTitle = 'Agregar Autoparte - Admin';
            $breadcrumbs = [
                ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
                ['text' => 'Inventario', 'url' => BASE_URL . '/index.php?module=admin&action=inventario'],
                ['text' => 'Agregar', 'url' => '']
            ];
            
            // Incluir vista
            require_once VIEWS_PATH . '/admin/inventario/crear.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar formulario');
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Procesa la creación de una autoparte
     * Cumple con requisito 3: CRUD de autopartes con imágenes
     */
    public function store() {
        try {
            // Verificar permiso
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
            
            // Procesar imágenes
            $imagen_thumb = '';
            $imagen_grande = '';
            
            // Subir thumbnail
            if (isset($_FILES['imagen_thumb']) && $_FILES['imagen_thumb']['error'] === UPLOAD_ERR_OK) {
                $resultThumb = $this->subirImagen($_FILES['imagen_thumb'], 'thumb');
                if ($resultThumb['success']) {
                    $imagen_thumb = $resultThumb['path'];
                } else {
                    $validator->getErrors()['imagen_thumb'] = $resultThumb['error'];
                }
            }
            
            // Subir imagen grande
            if (isset($_FILES['imagen_grande']) && $_FILES['imagen_grande']['error'] === UPLOAD_ERR_OK) {
                $resultGrande = $this->subirImagen($_FILES['imagen_grande'], 'grande');
                if ($resultGrande['success']) {
                    $imagen_grande = $resultGrande['path'];
                } else {
                    $validator->getErrors()['imagen_grande'] = $resultGrande['error'];
                }
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
            $this->autoparteModel->imagen_thumb = $imagen_thumb;
            $this->autoparteModel->imagen_grande = $imagen_grande;
            $this->autoparteModel->estado = $estado;
            $this->autoparteModel->usuario_id = $_SESSION['usuario_id'];
            
            $autoparteId = $this->autoparteModel->crear();
            
            if ($autoparteId) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte agregada exitosamente');
                redirect('/index.php?module=admin&action=inventario');
            } else {
                // Eliminar imágenes subidas si falla
                if ($imagen_thumb && file_exists(UPLOADS_PATH . '/' . $imagen_thumb)) {
                    unlink(UPLOADS_PATH . '/' . $imagen_thumb);
                }
                if ($imagen_grande && file_exists(UPLOADS_PATH . '/' . $imagen_grande)) {
                    unlink(UPLOADS_PATH . '/' . $imagen_grande);
                }
                
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
            // Verificar permiso
            if (!hasPermission('inventario', 'actualizar')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para editar autopartes');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            // Obtener autoparte
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            if (!$autoparte) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            // Obtener datos para el formulario
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            
            // Variables para la vista
            $pageTitle = 'Editar Autoparte - Admin';
            $breadcrumbs = [
                ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
                ['text' => 'Inventario', 'url' => BASE_URL . '/index.php?module=admin&action=inventario'],
                ['text' => 'Editar', 'url' => '']
            ];
            
            // Incluir vista
            require_once VIEWS_PATH . '/admin/autopartes/editar.php';
            
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
            // Verificar permiso
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
            $estado = isset($_POST['estado']) ? 1 : 0;
            
            // Validaciones
            $validator = new Validator();
            
            $validator->required($nombre, 'nombre');
            $validator->minLength($nombre, 3, 'nombre');
            $validator->maxLength($nombre, 150, 'nombre');
            
            $validator->required($marca, 'marca');
            $validator->required($modelo, 'modelo');
            $validator->required($anio, 'anio');
            $validator->validYear($anio, 'anio');
            $validator->required($precio, 'precio');
            $validator->numeric($precio, 'precio');
            $validator->required($stock, 'stock');
            $validator->numeric($stock, 'stock');
            $validator->required($categoria_id, 'categoria');
            
            // Procesar nuevas imágenes si se suben
            $imagen_thumb = '';
            $imagen_grande = '';
            
            if (isset($_FILES['imagen_thumb']) && $_FILES['imagen_thumb']['error'] === UPLOAD_ERR_OK) {
                $resultThumb = $this->subirImagen($_FILES['imagen_thumb'], 'thumb');
                if ($resultThumb['success']) {
                    $imagen_thumb = $resultThumb['path'];
                    // Eliminar imagen anterior
                    if ($autoparteActual['imagen_thumb'] && file_exists(UPLOADS_PATH . '/' . $autoparteActual['imagen_thumb'])) {
                        unlink(UPLOADS_PATH . '/' . $autoparteActual['imagen_thumb']);
                    }
                } else {
                    $validator->getErrors()['imagen_thumb'] = $resultThumb['error'];
                }
            }
            
            if (isset($_FILES['imagen_grande']) && $_FILES['imagen_grande']['error'] === UPLOAD_ERR_OK) {
                $resultGrande = $this->subirImagen($_FILES['imagen_grande'], 'grande');
                if ($resultGrande['success']) {
                    $imagen_grande = $resultGrande['path'];
                    // Eliminar imagen anterior
                    if ($autoparteActual['imagen_grande'] && file_exists(UPLOADS_PATH . '/' . $autoparteActual['imagen_grande'])) {
                        unlink(UPLOADS_PATH . '/' . $autoparteActual['imagen_grande']);
                    }
                } else {
                    $validator->getErrors()['imagen_grande'] = $resultGrande['error'];
                }
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
            $this->autoparteModel->imagen_thumb = $imagen_thumb;
            $this->autoparteModel->imagen_grande = $imagen_grande;
            $this->autoparteModel->estado = $estado;
            
            if ($this->autoparteModel->actualizar()) {
                setFlashMessage(MSG_SUCCESS, 'Autoparte actualizada exitosamente');
                redirect('/index.php?module=admin&action=inventario');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al actualizar la autoparte');
                redirect('/index.php?module=admin&action=autoparte-editar&id=' . $id);
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al procesar: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Ver detalle de una autoparte
     */
    public function ver() {
        try {
            if (!hasPermission('inventario', 'leer')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para ver autopartes');
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
            
            // Variables para la vista
            $pageTitle = $autoparte['nombre'] . ' - Detalle';
            $breadcrumbs = [
                ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
                ['text' => 'Inventario', 'url' => BASE_URL . '/index.php?module=admin&action=inventario'],
                ['text' => 'Detalle', 'url' => '']
            ];
            
            require_once VIEWS_PATH . '/admin/autopartes/ver.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar autoparte');
            redirect('/index.php?module=admin&action=inventario');
        }
    }
    
    /**
     * Desactiva una autoparte (AJAX)
     */
    public function desactivar() {
        try {
            if (!hasPermission('inventario', 'eliminar')) {
                jsonResponse(['success' => false, 'message' => 'Sin permisos']);
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            }
            
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Autoparte no válida']);
            }
            
            if ($this->autoparteModel->desactivar($id)) {
                jsonResponse(['success' => true, 'message' => 'Autoparte desactivada']);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al desactivar']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Activa una autoparte (AJAX)
     */
    public function activar() {
        try {
            if (!hasPermission('inventario', 'eliminar')) {
                jsonResponse(['success' => false, 'message' => 'Sin permisos']);
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            }
            
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Autoparte no válida']);
            }
            
            if ($this->autoparteModel->activar($id)) {
                jsonResponse(['success' => true, 'message' => 'Autoparte activada']);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al activar']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Actualiza stock (AJAX)
     */
    public function actualizarStock() {
        try {
            if (!hasPermission('inventario', 'actualizar')) {
                jsonResponse(['success' => false, 'message' => 'Sin permisos']);
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            }
            
            $id = Validator::sanitizeInt($_POST['id'] ?? 0);
            $cantidad = Validator::sanitizeInt($_POST['cantidad'] ?? 0);
            $tipo = $_POST['tipo'] ?? 'agregar'; // 'agregar' o 'restar'
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Autoparte no válida']);
            }
            
            if ($cantidad <= 0) {
                jsonResponse(['success' => false, 'message' => 'Cantidad inválida']);
            }
            
            // Si es restar, convertir a negativo
            if ($tipo === 'restar') {
                $cantidad = -$cantidad;
            }
            
            if ($this->autoparteModel->actualizarStock($id, $cantidad)) {
                $autoparte = $this->autoparteModel->obtenerPorId($id);
                jsonResponse([
                    'success' => true, 
                    'message' => 'Stock actualizado',
                    'nuevo_stock' => $autoparte['stock']
                ]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Stock insuficiente o error al actualizar']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Obtiene el detalle de una autoparte (AJAX)
     */
    public function detalle() {
        try {
            if (!hasPermission('inventario', 'leer')) {
                jsonResponse(['success' => false, 'message' => 'Sin permisos']);
            }
            
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Autoparte no válida']);
            }
            
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            if ($autoparte) {
                jsonResponse([
                    'success' => true,
                    'autoparte' => $autoparte
                ]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Autoparte no encontrada']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Sube una imagen al servidor
     * Cumple con requisito 3: Guardar thumbnail e imágenes grandes
     * 
     * @param array $file $_FILES['name']
     * @param string $tipo 'thumb' o 'grande'
     * @return array ['success' => bool, 'path' => string, 'error' => string]
     */
    private function subirImagen($file, $tipo = 'thumb') {
        $validator = new Validator();
        
        // Validar imagen
        $maxSize = ($tipo === 'thumb') ? 2097152 : 5242880; // 2MB thumb, 5MB grande
        
        if (!$validator->validateImage($file, $maxSize, 'imagen')) {
            return ['success' => false, 'error' => $validator->getFirstError()];
        }
        
        // Generar nombre único
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $nombreArchivo = $tipo . '_' . uniqid() . '_' . time() . '.' . $extension;
        
        // Crear directorio si no existe
        $directorio = UPLOADS_PATH . '/autopartes';
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }
        
        $rutaCompleta = $directorio . '/' . $nombreArchivo;
        
        // Mover archivo
        if (move_uploaded_file($file['tmp_name'], $rutaCompleta)) {
            // Redimensionar si es necesario
            if ($tipo === 'thumb') {
                $this->redimensionarImagen($rutaCompleta, 300, 300);
            } else {
                $this->redimensionarImagen($rutaCompleta, 800, 800);
            }
            
            return ['success' => true, 'path' => 'autopartes/' . $nombreArchivo];
        }
        
        return ['success' => false, 'error' => 'Error al subir la imagen'];
    }
    
    /**
     * Redimensiona una imagen manteniendo proporción
     * 
     * @param string $ruta
     * @param int $maxAncho
     * @param int $maxAlto
     */
    private function redimensionarImagen($ruta, $maxAncho, $maxAlto) {
        list($ancho, $alto, $tipo) = getimagesize($ruta);
        
        // Calcular nuevas dimensiones
        $ratio = min($maxAncho / $ancho, $maxAlto / $alto);
        
        if ($ratio >= 1) {
            return; // No redimensionar si es más pequeña
        }
        
        $nuevoAncho = round($ancho * $ratio);
        $nuevoAlto = round($alto * $ratio);
        
        // Crear imagen según tipo
        switch ($tipo) {
            case IMAGETYPE_JPEG:
                $origen = imagecreatefromjpeg($ruta);
                break;
            case IMAGETYPE_PNG:
                $origen = imagecreatefrompng($ruta);
                break;
            case IMAGETYPE_WEBP:
                $origen = imagecreatefromwebp($ruta);
                break;
            default:
                return;
        }
        
        // Crear imagen destino
        $destino = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
        
        // Preservar transparencia para PNG
        if ($tipo === IMAGETYPE_PNG) {
            imagealphablending($destino, false);
            imagesavealpha($destino, true);
        }
        
        // Redimensionar
        imagecopyresampled($destino, $origen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $ancho, $alto);
        
        // Guardar
        switch ($tipo) {
            case IMAGETYPE_JPEG:
                imagejpeg($destino, $ruta, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($destino, $ruta, 8);
                break;
            case IMAGETYPE_WEBP:
                imagewebp($destino, $ruta, 85);
                break;
        }
        
        // Liberar memoria
        imagedestroy($origen);
        imagedestroy($destino);
    }
    
    /**
     * Obtiene todas las categorías activas
     * 
     * @return array
     */
    private function obtenerCategorias() {
        $query = "SELECT id, nombre, descripcion FROM categorias WHERE estado = 1 ORDER BY nombre";
        return $this->db->fetchAll($query);
    }
    
    /**
     * Búsqueda AJAX para autocompletado
     */
    public function buscarAjax() {
        try {
            $termino = Validator::sanitizeString($_GET['q'] ?? '');
            
            if (strlen($termino) < 2) {
                jsonResponse(['results' => []]);
            }
            
            $filtros = [
                'buscar' => $termino,
                'estado' => 1,
                'limite' => 10
            ];
            
            $autopartes = $this->autoparteModel->obtenerTodos($filtros);
            
            $results = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'text' => $item['nombre'] . ' - ' . $item['marca'] . ' ' . $item['modelo'],
                    'precio' => $item['precio'],
                    'stock' => $item['stock']
                ];
            }, $autopartes);
            
            jsonResponse(['results' => $results]);
            
        } catch (Exception $e) {
            jsonResponse(['results' => [], 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Obtiene modelos por marca (AJAX)
     */
    public function obtenerModelosPorMarca() {
        try {
            $marca = Validator::sanitizeString($_GET['marca'] ?? '');
            
            if (empty($marca)) {
                jsonResponse(['modelos' => []]);
            }
            
            $modelos = $this->autoparteModel->obtenerModelos($marca);
            
            jsonResponse(['modelos' => $modelos]);
            
        } catch (Exception $e) {
            jsonResponse(['modelos' => [], 'error' => $e->getMessage()]);
        }
    }

     public function exportar() {
        try {
            // Verificar permiso
            if (!hasPermission('inventario', 'leer')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para exportar');
                redirect('/index.php?module=admin&action=inventario');
            }
            
            $formato = $_GET['formato'] ?? 'csv';
            
            // Obtener todos los datos
            $query = "SELECT 
                a.id as 'ID',
                a.nombre as 'Nombre',
                a.descripcion as 'Descripción',
                a.marca as 'Marca',
                a.modelo as 'Modelo',
                a.anio as 'Año',
                a.precio as 'Precio',
                a.stock as 'Stock',
                c.nombre as 'Categoría',
                CASE WHEN a.estado = 1 THEN 'Activo' ELSE 'Inactivo' END as 'Estado',
                a.fecha_creacion as 'Fecha Creación'
                FROM autopartes a
                LEFT JOIN categorias c ON a.categoria_id = c.id
                ORDER BY a.nombre ASC";
            
            $autopartes = $this->db->fetchAll($query);
            
            if ($formato === 'csv') {
                // Configurar headers para descarga CSV
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename=inventario_' . date('Y-m-d_H-i-s') . '.csv');
                header('Pragma: no-cache');
                header('Expires: 0');
                
                $output = fopen('php://output', 'w');
                
                // BOM para Excel (reconozca UTF-8)
                fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Encabezados
                if (!empty($autopartes)) {
                    fputcsv($output, array_keys($autopartes[0]), ';');
                }
                
                // Datos
                foreach ($autopartes as $autoparte) {
                    fputcsv($output, $autoparte, ';');
                }
                
                fclose($output);
                exit;
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al exportar: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=inventario');
        }
    }
}
?>