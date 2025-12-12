<?php
/**
 * Controlador de Usuarios
 * Maneja el CRUD completo de usuarios (Admin, Operador, Cliente)
 */

// Cargar dependencias necesarias
if (!class_exists('Database')) {
    require_once __DIR__ . '/../config/Database.php';
}
if (!class_exists('Usuario')) {
    require_once __DIR__ . '/../models/Usuario.php';
}
if (!class_exists('Validator')) {
    require_once __DIR__ . '/../core/Validator.php';
}

class UsuarioController {
    
    private $usuarioModel;
    
    public function __construct() {
        // Verificar que sea administrador
        if (!hasRole(ROL_ADMINISTRADOR)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado');
            redirect('/index.php?module=auth&action=login');
        }
        
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * Lista todos los usuarios con filtros
     */
    public function index() {
        try {
            // Verificar permiso
            if (!hasPermission('usuarios', 'leer')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para ver usuarios');
                redirect('/index.php?module=admin&action=dashboard');
            }
            
            $db = Database::getInstance();
            
            // Obtener filtros
            $filtros = [
                'buscar' => $_GET['buscar'] ?? '',
                'rol_id' => $_GET['rol'] ?? '',
                'estado' => $_GET['estado'] ?? ''
            ];
            
            // Paginación
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $porPagina = ADMIN_ITEMS_PER_PAGE;
            
            // Obtener usuarios
            $usuarios = $this->usuarioModel->obtenerTodos($filtros);
            
            // Obtener todos los roles para el filtro
            $queryRoles = "SELECT id, nombre FROM roles ORDER BY nombre";
            $roles = $db->fetchAll($queryRoles);
            
            // Estadísticas
            $totalUsuarios = $this->usuarioModel->contarTodos();
            $totalActivos = $this->usuarioModel->contarTodos(['estado' => 1]);
            $totalInactivos = $this->usuarioModel->contarTodos(['estado' => 0]);
            
            // Contar por rol
            $statsRoles = [];
            foreach ($roles as $rol) {
                $statsRoles[$rol['nombre']] = $this->usuarioModel->contarTodos(['rol_id' => $rol['id']]);
            }
            
            // Variables para la vista
            $pageTitle = 'Gestión de Usuarios - Admin';
            $breadcrumbs = [
                ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
                ['text' => 'Usuarios', 'url' => '']
            ];
            
            // Incluir vista
            require_once VIEWS_PATH . '/admin/usuarios/index.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar usuarios: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=dashboard');
        }
    }
    
    /**
     * Muestra formulario de creación
     */
    public function crear() {
        try {
            // Verificar permiso
            if (!hasPermission('usuarios', 'crear')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para crear usuarios');
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            $db = Database::getInstance();
            
            // Obtener roles
            $queryRoles = "SELECT id, nombre, descripcion FROM roles ORDER BY nombre";
            $roles = $db->fetchAll($queryRoles);
            
            // Variables para la vista
            $pageTitle = 'Crear Usuario - Admin';
            $breadcrumbs = [
                ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
                ['text' => 'Usuarios', 'url' => BASE_URL . '/index.php?module=admin&action=usuarios'],
                ['text' => 'Crear', 'url' => '']
            ];
            
            // Incluir vista
            require_once VIEWS_PATH . '/admin/usuarios/crear.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar formulario');
            redirect('/index.php?module=admin&action=usuarios');
        }
    }
    
    /**
     * Procesa la creación de un usuario
     */
    public function store() {
        try {
            // Verificar permiso
            if (!hasPermission('usuarios', 'crear')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para crear usuarios');
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=admin&action=usuario-crear');
            }
            
            // Sanitizar datos
            $nombre = Validator::sanitizeString($_POST['nombre'] ?? '');
            $email = Validator::sanitizeEmail($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $rol_id = Validator::sanitizeInt($_POST['rol_id'] ?? 0);
            $estado = isset($_POST['estado']) ? 1 : 0;
            
            // Validaciones
            $validator = new Validator();
            
            $validator->required($nombre, 'nombre');
            $validator->minLength($nombre, 3, 'nombre');
            $validator->maxLength($nombre, 100, 'nombre');
            
            $validator->required($email, 'email');
            $validator->email($email, 'email');
            
            $validator->required($password, 'password');
            $validator->minLength($password, 6, 'password');
            $validator->passwordMatch($password, $password_confirm);
            
            $validator->required($rol_id, 'rol');
            
            // Verificar si el email ya existe
            if ($this->usuarioModel->emailExiste($email)) {
                $validator->getErrors()['email'] = 'El email ya está registrado';
            }
            
            if ($validator->hasErrors()) {
                $_SESSION['errors'] = $validator->getErrors();
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=admin&action=usuario-crear');
            }
            
            // Crear usuario
            $this->usuarioModel->nombre = $nombre;
            $this->usuarioModel->email = $email;
            $this->usuarioModel->password = $password;
            $this->usuarioModel->rol_id = $rol_id;
            $this->usuarioModel->estado = $estado;
            
            $userId = $this->usuarioModel->crear();
            
            if ($userId) {
                setFlashMessage(MSG_SUCCESS, 'Usuario creado exitosamente');
                redirect('/index.php?module=admin&action=usuarios');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al crear el usuario');
                redirect('/index.php?module=admin&action=usuario-crear');
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al procesar: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=usuario-crear');
        }
    }
    
    /**
     * Muestra formulario de edición
     */
    public function editar() {
        try {
            // Verificar permiso
            if (!hasPermission('usuarios', 'actualizar')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para editar usuarios');
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Usuario no encontrado');
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            $db = Database::getInstance();
            
            // Obtener usuario
            $usuario = $this->usuarioModel->obtenerPorId($id);
            
            if (!$usuario) {
                setFlashMessage(MSG_ERROR, 'Usuario no encontrado');
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            // No permitir editar el propio usuario admin desde aquí
            if ($usuario['id'] == $_SESSION['usuario_id']) {
                setFlashMessage(MSG_WARNING, 'Para editar tu propio perfil, usa la opción "Mi Perfil"');
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            // Obtener roles
            $queryRoles = "SELECT id, nombre, descripcion FROM roles ORDER BY nombre";
            $roles = $db->fetchAll($queryRoles);
            
            // Variables para la vista
            $pageTitle = 'Editar Usuario - Admin';
            $breadcrumbs = [
                ['text' => 'Dashboard', 'url' => BASE_URL . '/index.php?module=admin&action=dashboard'],
                ['text' => 'Usuarios', 'url' => BASE_URL . '/index.php?module=admin&action=usuarios'],
                ['text' => 'Editar', 'url' => '']
            ];
            
            // Incluir vista
            require_once VIEWS_PATH . '/admin/usuarios/editar.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar usuario');
            redirect('/index.php?module=admin&action=usuarios');
        }
    }
    
    /**
     * Procesa la actualización de un usuario
     */
    public function update() {
        try {
            // Verificar permiso
            if (!hasPermission('usuarios', 'actualizar')) {
                setFlashMessage(MSG_ERROR, 'No tienes permiso para actualizar usuarios');
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                setFlashMessage(MSG_ERROR, 'Usuario no válido');
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            // Obtener usuario actual
            $usuarioActual = $this->usuarioModel->obtenerPorId($id);
            
            if (!$usuarioActual) {
                setFlashMessage(MSG_ERROR, 'Usuario no encontrado');
                redirect('/index.php?module=admin&action=usuarios');
            }
            
            // Sanitizar datos
            $nombre = Validator::sanitizeString($_POST['nombre'] ?? '');
            $email = Validator::sanitizeEmail($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            $rol_id = Validator::sanitizeInt($_POST['rol_id'] ?? 0);
            $estado = isset($_POST['estado']) ? 1 : 0;
            
            // Validaciones
            $validator = new Validator();
            
            $validator->required($nombre, 'nombre');
            $validator->minLength($nombre, 3, 'nombre');
            $validator->maxLength($nombre, 100, 'nombre');
            
            $validator->required($email, 'email');
            $validator->email($email, 'email');
            
            // Validar contraseña solo si se proporciona
            if (!empty($password)) {
                $validator->minLength($password, 6, 'password');
                $validator->passwordMatch($password, $password_confirm);
            }
            
            $validator->required($rol_id, 'rol');
            
            // Verificar si el email ya existe (excluyendo el usuario actual)
            if ($this->usuarioModel->emailExiste($email, $id)) {
                $validator->getErrors()['email'] = 'El email ya está registrado';
            }
            
            if ($validator->hasErrors()) {
                $_SESSION['errors'] = $validator->getErrors();
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=admin&action=usuario-editar&id=' . $id);
            }
            
            // Actualizar usuario
            $this->usuarioModel->id = $id;
            $this->usuarioModel->nombre = $nombre;
            $this->usuarioModel->email = $email;
            $this->usuarioModel->password = $password; // Se actualiza solo si no está vacío
            $this->usuarioModel->rol_id = $rol_id;
            $this->usuarioModel->estado = $estado;
            
            if ($this->usuarioModel->actualizar()) {
                setFlashMessage(MSG_SUCCESS, 'Usuario actualizado exitosamente');
                redirect('/index.php?module=admin&action=usuarios');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al actualizar el usuario');
                redirect('/index.php?module=admin&action=usuario-editar&id=' . $id);
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al procesar: ' . $e->getMessage());
            redirect('/index.php?module=admin&action=usuarios');
        }
    }
    
    /**
     * Desactiva un usuario (AJAX)
     */
    public function desactivar() {
        try {
            // Verificar permiso
            if (!hasPermission('usuarios', 'eliminar')) {
                jsonResponse(['success' => false, 'message' => 'Sin permisos']);
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            }
            
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Usuario no válido']);
            }
            
            // No permitir desactivar el propio usuario
            if ($id == $_SESSION['usuario_id']) {
                jsonResponse(['success' => false, 'message' => 'No puedes desactivar tu propio usuario']);
            }
            
            if ($this->usuarioModel->desactivar($id)) {
                jsonResponse(['success' => true, 'message' => 'Usuario desactivado']);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al desactivar']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Activa un usuario (AJAX)
     */
    public function activar() {
        try {
            // Verificar permiso
            if (!hasPermission('usuarios', 'eliminar')) {
                jsonResponse(['success' => false, 'message' => 'Sin permisos']);
            }
            
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            }
            
            $id = $_POST['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Usuario no válido']);
            }
            
            if ($this->usuarioModel->activar($id)) {
                jsonResponse(['success' => true, 'message' => 'Usuario activado']);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al activar']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Obtiene el detalle de un usuario (AJAX)
     */
    public function detalle() {
        try {
            if (!hasPermission('usuarios', 'leer')) {
                jsonResponse(['success' => false, 'message' => 'Sin permisos']);
            }
            
            $id = $_GET['id'] ?? 0;
            
            if (!$id) {
                jsonResponse(['success' => false, 'message' => 'Usuario no válido']);
            }
            
            $usuario = $this->usuarioModel->obtenerPorId($id);
            
            if ($usuario) {
                // No enviar la contraseña
                unset($usuario['password']);
                
                jsonResponse([
                    'success' => true,
                    'usuario' => $usuario
                ]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Usuario no encontrado']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
?>