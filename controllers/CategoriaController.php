<?php
/**
 * Controlador de Categorías
 * Maneja el CRUD de categorías de autopartes
 */

require_once __DIR__ . '/../models/Categoria.php';

class CategoriaController {
    
    private $categoriaModel;
    
    public function __construct() {
        // Verificar permisos
        if (!hasRole(ROL_ADMINISTRADOR)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado');
            redirect('/index.php?module=auth&action=login');
        }
        
        $this->categoriaModel = new Categoria();
    }
    
    /**
     * Lista todas las categorías
     */
    public function index() {
        try {
            // Obtener filtros
            $filtros = [
                'buscar' => $_GET['buscar'] ?? '',
                'estado' => $_GET['estado'] ?? ''
            ];
            
            // Obtener categorías
            $categorias = $this->categoriaModel->obtenerTodas($filtros);
            
            // Variables para la vista
            $pageTitle = 'Gestión de Categorías';
            $breadcrumbs = [
                ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
                ['text' => 'Categorías', 'url' => '']
            ];
            
            // Incluir vista
            require_once VIEWS_PATH . '/admin/categorias/index.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar las categorías');
            redirect('/index.php?module=admin&action=dashboard');
        }
    }
    
    /**
     * Muestra el formulario para crear categoría
     */
    public function crear() {
        if (!hasPermission('categorias', 'crear')) {
            setFlashMessage(MSG_ERROR, 'No tienes permiso para crear categorías');
            redirect('/index.php?module=admin&action=categorias');
        }
        
        $pageTitle = 'Nueva Categoría';
        $breadcrumbs = [
            ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
            ['text' => 'Categorías', 'url' => BASE_URL . '/index.php?module=admin&action=categorias'],
            ['text' => 'Nueva', 'url' => '']
        ];
        
        // Acción del formulario
        $action = 'store';
        $categoria = null;
        
        require_once VIEWS_PATH . '/admin/categorias/form.php';
    }
    
    /**
     * Guarda una nueva categoría
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?module=admin&action=categorias');
        }
        
        if (!hasPermission('categorias', 'crear')) {
            setFlashMessage(MSG_ERROR, 'No tienes permiso para crear categorías');
            redirect('/index.php?module=admin&action=categorias');
        }
        
        try {
            // Sanitizar datos
            $this->categoriaModel->nombre = Validator::sanitizeString($_POST['nombre'] ?? '');
            $this->categoriaModel->descripcion = Validator::sanitizeString($_POST['descripcion'] ?? '');
            $this->categoriaModel->estado = isset($_POST['estado']) ? 1 : 0;
            $this->categoriaModel->imagen = null;
            
            // Procesar imagen si existe
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreImagen = $this->categoriaModel->guardarImagen($_FILES['imagen']);
                if ($nombreImagen) {
                    $this->categoriaModel->imagen = $nombreImagen;
                } else {
                    // Si hay error en la imagen, obtener errores del validador
                    $errors = $this->categoriaModel->getErrors();
                    if (!empty($errors)) {
                        $_SESSION['errors'] = $errors;
                        $_SESSION['old'] = $_POST;
                        redirect('/index.php?module=admin&action=categoria-crear');
                    }
                }
            }
            
            // Crear categoría
            $id = $this->categoriaModel->crear();
            
            if ($id) {
                setFlashMessage(MSG_SUCCESS, 'Categoría creada exitosamente');
                redirect('/index.php?module=admin&action=categorias');
            } else {
                $_SESSION['errors'] = $this->categoriaModel->getErrors();
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=admin&action=categoria-crear');
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al crear la categoría');
            redirect('/index.php?module=admin&action=categoria-crear');
        }
    }
    
    /**
     * Muestra el formulario para editar categoría
     */
    public function editar() {
        if (!hasPermission('categorias', 'actualizar')) {
            setFlashMessage(MSG_ERROR, 'No tienes permiso para editar categorías');
            redirect('/index.php?module=admin&action=categorias');
        }
        
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            setFlashMessage(MSG_ERROR, 'Categoría no encontrada');
            redirect('/index.php?module=admin&action=categorias');
        }
        
        try {
            $categoria = $this->categoriaModel->obtenerPorId($id);
            
            if (!$categoria) {
                setFlashMessage(MSG_ERROR, 'Categoría no encontrada');
                redirect('/index.php?module=admin&action=categorias');
            }
            
            // Obtener estadísticas
            $estadisticas = $this->categoriaModel->obtenerEstadisticas($id);
            
            $pageTitle = 'Editar Categoría';
            $breadcrumbs = [
                ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
                ['text' => 'Categorías', 'url' => BASE_URL . '/index.php?module=admin&action=categorias'],
                ['text' => 'Editar', 'url' => '']
            ];
            
            // Acción del formulario
            $action = 'update';
            
            require_once VIEWS_PATH . '/admin/categorias/form.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar la categoría');
            redirect('/index.php?module=admin&action=categorias');
        }
    }
    
    /**
     * Actualiza una categoría
     */
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?module=admin&action=categorias');
        }
        
        if (!hasPermission('categorias', 'actualizar')) {
            setFlashMessage(MSG_ERROR, 'No tienes permiso para actualizar categorías');
            redirect('/index.php?module=admin&action=categorias');
        }
        
        try {
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Categoría no encontrada');
                redirect('/index.php?module=admin&action=categorias');
            }
            
            // Obtener categoría actual
            $categoriaActual = $this->categoriaModel->obtenerPorId($id);
            
            if (!$categoriaActual) {
                setFlashMessage(MSG_ERROR, 'Categoría no encontrada');
                redirect('/index.php?module=admin&action=categorias');
            }
            
            // Sanitizar datos
            $this->categoriaModel->id = $id;
            $this->categoriaModel->nombre = Validator::sanitizeString($_POST['nombre'] ?? '');
            $this->categoriaModel->descripcion = Validator::sanitizeString($_POST['descripcion'] ?? '');
            $this->categoriaModel->estado = isset($_POST['estado']) ? 1 : 0;
            $this->categoriaModel->imagen = $categoriaActual['imagen'];
            
            // Procesar nueva imagen si existe
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $nombreImagen = $this->categoriaModel->guardarImagen($_FILES['imagen']);
                if ($nombreImagen) {
                    // Eliminar imagen anterior
                    if ($categoriaActual['imagen']) {
                        $this->categoriaModel->eliminarImagen($categoriaActual['imagen']);
                    }
                    $this->categoriaModel->imagen = $nombreImagen;
                }
            }
            
            // Actualizar categoría
            if ($this->categoriaModel->actualizar()) {
                setFlashMessage(MSG_SUCCESS, 'Categoría actualizada exitosamente');
                redirect('/index.php?module=admin&action=categorias');
            } else {
                $_SESSION['errors'] = $this->categoriaModel->getErrors();
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=admin&action=categoria-editar&id=' . $id);
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al actualizar la categoría');
            redirect('/index.php?module=admin&action=categorias');
        }
    }
    
    /**
     * Elimina/Desactiva una categoría (AJAX)
     */
    public function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Método no permitido']);
        }
        
        if (!hasPermission('categorias', 'eliminar')) {
            jsonResponse(['success' => false, 'message' => 'No tienes permiso']);
        }
        
        try {
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'ID no válido']);
            }
            
            if ($this->categoriaModel->eliminar($id)) {
                jsonResponse([
                    'success' => true, 
                    'message' => 'Categoría desactivada exitosamente'
                ]);
            } else {
                $errors = $this->categoriaModel->getErrors();
                $mensaje = !empty($errors) ? reset($errors) : 'Error al desactivar la categoría';
                jsonResponse(['success' => false, 'message' => $mensaje]);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => 'Error al procesar la solicitud']);
        }
    }
    
    /**
     * Activa una categoría (AJAX)
     */
    public function activar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(['success' => false, 'message' => 'Método no permitido']);
        }
        
        if (!hasPermission('categorias', 'actualizar')) {
            jsonResponse(['success' => false, 'message' => 'No tienes permiso']);
        }
        
        try {
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'ID no válido']);
            }
            
            if ($this->categoriaModel->activar($id)) {
                jsonResponse([
                    'success' => true, 
                    'message' => 'Categoría activada exitosamente'
                ]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al activar la categoría']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => 'Error al procesar la solicitud']);
        }
    }
    
    /**
     * Obtiene detalles de una categoría (AJAX)
     */
    public function detalle() {
        if (!hasPermission('categorias', 'leer')) {
            jsonResponse(['success' => false, 'message' => 'No tienes permiso']);
        }
        
        try {
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'ID no válido']);
            }
            
            $categoria = $this->categoriaModel->obtenerPorId($id);
            $estadisticas = $this->categoriaModel->obtenerEstadisticas($id);
            
            if ($categoria) {
                jsonResponse([
                    'success' => true,
                    'categoria' => $categoria,
                    'estadisticas' => $estadisticas
                ]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Categoría no encontrada']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => 'Error al obtener información']);
        }
    }
}
?>