<?php
/**
 * Controlador de Autenticación
 * Maneja login, registro y logout
 */

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    
    private $usuarioModel;
    
    public function __construct() {
        $this->usuarioModel = new Usuario();
    }
    
    /**
     * Muestra el formulario de login
     */
    public function login() {
        // Si ya está autenticado, redirigir
        if (isAuthenticated()) {
            $this->redirectByRole();
        }
        
        require_once VIEWS_PATH . '/auth/login.php';
    }
    
    /**
     * Procesa el login
     */
    public function doLogin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?module=auth&action=login');
        }
        
        try {
            // Sanitizar datos
            $email = Validator::sanitizeEmail($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            
            // Validar que no estén vacíos
            if (empty($email) || empty($password)) {
                setFlashMessage(MSG_ERROR, 'Por favor complete todos los campos');
                redirect('/index.php?module=auth&action=login');
            }
            
            // Intentar autenticar
            $usuario = $this->usuarioModel->autenticar($email, $password);
            
            if ($usuario) {
                // Crear sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['rol_id'] = $usuario['rol_id'];
                $_SESSION['rol_nombre'] = $usuario['rol_nombre'];
                
                // Obtener y guardar permisos
                $permisos = $this->usuarioModel->obtenerPermisos($usuario['rol_id']);
                $_SESSION['permisos'] = $permisos;
                
                // Regenerar ID de sesión por seguridad
                session_regenerate_id(true);
                
                setFlashMessage(MSG_SUCCESS, 'Bienvenido ' . $usuario['nombre']);
                
                // Redirigir según rol
                $this->redirectByRole();
                
            } else {
                setFlashMessage(MSG_ERROR, 'Credenciales incorrectas o usuario inactivo');
                redirect('/index.php?module=auth&action=login');
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al iniciar sesión');
            redirect('/index.php?module=auth&action=login');
        }
    }
    
    /**
     * Muestra el formulario de registro
     */
    public function register() {
        // Si ya está autenticado, redirigir
        if (isAuthenticated()) {
            $this->redirectByRole();
        }
        
        require_once VIEWS_PATH . '/auth/register.php';
    }
    
    /**
     * Procesa el registro de un nuevo cliente
     */
    public function doRegister() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/index.php?module=auth&action=register');
        }
        
        try {
            // Sanitizar datos
            $nombre = Validator::sanitizeString($_POST['nombre'] ?? '');
            $email = Validator::sanitizeEmail($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';
            
            // Validaciones
            $validator = new Validator();
            
            $validator->required($nombre, 'nombre');
            $validator->minLength($nombre, 3, 'nombre');
            
            $validator->required($email, 'email');
            $validator->email($email, 'email');
            
            $validator->required($password, 'password');
            $validator->minLength($password, 6, 'password');
            $validator->passwordMatch($password, $password_confirm);
            
            // Verificar si el email ya existe
            if ($this->usuarioModel->emailExiste($email)) {
                $validator->getErrors()['email'] = 'El email ya está registrado';
            }
            
            if ($validator->hasErrors()) {
                $_SESSION['errors'] = $validator->getErrors();
                $_SESSION['old'] = $_POST;
                redirect('/index.php?module=auth&action=register');
            }
            
            // Crear usuario
            $this->usuarioModel->nombre = $nombre;
            $this->usuarioModel->email = $email;
            $this->usuarioModel->password = $password;
            $this->usuarioModel->rol_id = ROL_CLIENTE; // Siempre cliente en registro público
            $this->usuarioModel->estado = ESTADO_ACTIVO;
            
            $userId = $this->usuarioModel->crear();
            
            if ($userId) {
                setFlashMessage(MSG_SUCCESS, 'Registro exitoso. Ya puedes iniciar sesión');
                redirect('/index.php?module=auth&action=login');
            } else {
                setFlashMessage(MSG_ERROR, 'Error al crear usuario');
                redirect('/index.php?module=auth&action=register');
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al procesar registro');
            redirect('/index.php?module=auth&action=register');
        }
    }
    
    /**
     * Cierra la sesión
     */
    public function logout() {
        clearSession();
        setFlashMessage(MSG_INFO, 'Sesión cerrada exitosamente');
        redirect('/index.php?module=auth&action=login');
    }
    
    /**
     * Redirige según el rol del usuario
     */
    private function redirectByRole() {
        switch ($_SESSION['rol_id']) {
            case ROL_ADMINISTRADOR:
                redirect('/index.php?module=admin&action=dashboard');
                break;
            case ROL_OPERADOR:
                redirect('/index.php?module=operador&action=dashboard');
                break;
            case ROL_CLIENTE:
                redirect('/index.php?module=cliente&action=dashboard');
                break;
            default:
                redirect('/index.php');
        }
    }
}
?>