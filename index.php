<?php
/**
 * Punto de entrada principal del sistema
 * Enrutador simple para el proyecto
 * 
 * @author Grupo 1SF131
 * @version 1.0
 */

// Cargar configuración (que a su vez carga las clases core)
$configFile = __DIR__ . '/config/config.php';
if (!file_exists($configFile)) {
    die('Error: No se encuentra el archivo config/config.php');
}
require_once $configFile;

// Obtener la acción de la URL
$action = $_GET['action'] ?? 'home';
$module = $_GET['module'] ?? 'public';

// Si está autenticado y trata de ir al login, redirigir al dashboard
if ($action === 'login' && isAuthenticated()) {
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
    }
}

// Enrutamiento básico
switch ($module) {
    
    // =========================================================================
    // MÓDULO: AUTENTICACIÓN
    // =========================================================================
    case 'auth':
        require_once CONTROLLERS_PATH . '/AuthController.php';
        $controller = new AuthController();
        
        switch ($action) {
            case 'login':
                $controller->login();
                break;
            case 'do_login':
                $controller->doLogin();
                break;
            case 'register':
            case 'registro':
                $controller->register();
                break;
            case 'do_register':
            case 'do_registro':
                $controller->doRegister();
                break;
            case 'logout':
                $controller->logout();
                break;
            default:
                $controller->login();
        }
        break;
    
    // =========================================================================
    // MÓDULO: ADMINISTRADOR
    // =========================================================================
    case 'admin':
        // Verificar si es administrador
        if (!hasRole(ROL_ADMINISTRADOR)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado');
            redirect('/index.php?module=auth&action=login');
        }
        
        require_once CONTROLLERS_PATH . '/AdminController.php';
        $controller = new AdminController();
        
        switch ($action) {
            case 'dashboard':
                $controller->dashboard();
                break;
                
            // === PERFIL DEL ADMIN ===
            case 'perfil':
                $controller->perfil();
                break;
                
            case 'perfil-update':
            case 'perfilUpdate':
                $controller->perfilUpdate();
                break;
                
            // === VENTAS ===
            case 'ventas':
                $controller->ventas();
                break;
                
            case 'venta-detalle':
                $controller->ventaDetalle();
                break;
                
            case 'exportar-ventas':
            case 'exportarVentas':
                $controller->exportarVentas();
                break;
                
            // === STOCK BAJO ===
            case 'stock-bajo':
            case 'stockBajo':
                $controller->stockBajo();
                break;
                
            // === ESTADÍSTICAS ===
            case 'estadisticas':
                $controller->estadisticas();
                break;
                
            case 'getEstadisticas':
                $controller->getEstadisticas();
                break;
                
            case 'getGraficoVentas':
                $controller->getGraficoVentas();
                break;
                
            case 'configuracion':
                $controller->configuracion();
                break;
                
            // === RUTAS DE USUARIOS ===
            case 'usuarios':
                require_once CONTROLLERS_PATH . '/UsuarioController.php';
                $usuarioController = new UsuarioController();
                $usuarioController->index();
                break;
                
            case 'usuario-crear':
                require_once CONTROLLERS_PATH . '/UsuarioController.php';
                $usuarioController = new UsuarioController();
                $usuarioController->crear();
                break;
                
            case 'usuario-store':
                require_once CONTROLLERS_PATH . '/UsuarioController.php';
                $usuarioController = new UsuarioController();
                $usuarioController->store();
                break;
                
            case 'usuario-editar':
                require_once CONTROLLERS_PATH . '/UsuarioController.php';
                $usuarioController = new UsuarioController();
                $usuarioController->editar();
                break;
                
            case 'usuario-update':
                require_once CONTROLLERS_PATH . '/UsuarioController.php';
                $usuarioController = new UsuarioController();
                $usuarioController->update();
                break;
                
            case 'usuario-eliminar':
                require_once CONTROLLERS_PATH . '/UsuarioController.php';
                $usuarioController = new UsuarioController();
                $usuarioController->eliminar();
                break;
                
            case 'usuario-activar':
                require_once CONTROLLERS_PATH . '/UsuarioController.php';
                $usuarioController = new UsuarioController();
                $usuarioController->activar();
                break;
                
            case 'usuario-detalle':
                require_once CONTROLLERS_PATH . '/UsuarioController.php';
                $usuarioController = new UsuarioController();
                $usuarioController->detalle();
                break;
                
            // === RUTAS DE CATEGORÍAS ===
            case 'categorias':
                require_once CONTROLLERS_PATH . '/CategoriaController.php';
                $categoriaController = new CategoriaController();
                $categoriaController->index();
                break;
                
            case 'categoria-crear':
                require_once CONTROLLERS_PATH . '/CategoriaController.php';
                $categoriaController = new CategoriaController();
                $categoriaController->crear();
                break;
                
            case 'categoria-store':
                require_once CONTROLLERS_PATH . '/CategoriaController.php';
                $categoriaController = new CategoriaController();
                $categoriaController->store();
                break;
                
            case 'categoria-editar':
                require_once CONTROLLERS_PATH . '/CategoriaController.php';
                $categoriaController = new CategoriaController();
                $categoriaController->editar();
                break;
                
            case 'categoria-update':
                require_once CONTROLLERS_PATH . '/CategoriaController.php';
                $categoriaController = new CategoriaController();
                $categoriaController->update();
                break;
                
            case 'categoria-eliminar':
                require_once CONTROLLERS_PATH . '/CategoriaController.php';
                $categoriaController = new CategoriaController();
                $categoriaController->eliminar();
                break;
                
            case 'categoria-activar':
                require_once CONTROLLERS_PATH . '/CategoriaController.php';
                $categoriaController = new CategoriaController();
                $categoriaController->activar();
                break;
                
            case 'categoria-detalle':
                require_once CONTROLLERS_PATH . '/CategoriaController.php';
                $categoriaController = new CategoriaController();
                $categoriaController->detalle();
                break;
                
            // === RUTAS DE AUTOPARTES/INVENTARIO ===
            case 'autopartes':
            case 'inventario':
                require_once CONTROLLERS_PATH . '/AutoparteController.php';
                $autoparteController = new AutoparteController();
                $autoparteController->index();
                break;
            
            case 'exportar-inventario':
            case 'exportar-excel':
            case 'reporte-inventario':
                require_once CONTROLLERS_PATH . '/AutoparteController.php';
                $autoparteController = new AutoparteController();
                $autoparteController->exportar();
                break;
            
            case 'inventario-bajo':
                $controller->stockBajo();
                break;
            
            case 'inventario-agregar':
            case 'autoparte-crear':
                require_once CONTROLLERS_PATH . '/AutoparteController.php';
                $autoparteController = new AutoparteController();
                $autoparteController->crear();
                break;
                
            case 'autoparte-store':
                require_once CONTROLLERS_PATH . '/AutoparteController.php';
                $autoparteController = new AutoparteController();
                $autoparteController->store();
                break;
                
            case 'autoparte-editar':
                require_once CONTROLLERS_PATH . '/AutoparteController.php';
                $autoparteController = new AutoparteController();
                $autoparteController->editar();
                break;
                
            case 'autoparte-update':
                require_once CONTROLLERS_PATH . '/AutoparteController.php';
                $autoparteController = new AutoparteController();
                $autoparteController->update();
                break;
                
            case 'autoparte-eliminar':
                require_once CONTROLLERS_PATH . '/AutoparteController.php';
                $autoparteController = new AutoparteController();
                $autoparteController->eliminar();
                break;
                
            case 'autoparte-detalle':
                require_once CONTROLLERS_PATH . '/AutoparteController.php';
                $autoparteController = new AutoparteController();
                $autoparteController->detalle();
                break;
                
            // === RUTAS DE VENTAS ===
            case 'ventas':
                $controller->ventas();
                break;
                
            case 'venta-detalle':
                $controller->ventaDetalle();
                break;
                
            // === RUTAS DE REPORTES ===
            case 'reportes':
                $controller->reportes();
                break;
                
            default:
                $controller->dashboard();
        }
        break;
    
    // =========================================================================
    // MÓDULO: OPERADOR
    // =========================================================================
    case 'operador':
        // Verificar si es operador o admin
        if (!hasRole(ROL_OPERADOR) && !hasRole(ROL_ADMINISTRADOR)) {
            setFlashMessage(MSG_ERROR, 'Acceso denegado');
            redirect('/index.php?module=auth&action=login');
        }
        
        require_once CONTROLLERS_PATH . '/OperadorController.php';
        $controller = new OperadorController();
        
        switch ($action) {
            case 'dashboard':
                $controller->dashboard();
                break;
                
            // === GESTIÓN DE INVENTARIO ===
            case 'inventario':
                $controller->inventario();
                break;
            
            case 'ver-autoparte':
                $controller->verAutoparte();
                break;
                
            case 'crear-autoparte':
                $controller->crearAutoparte();
                break;
            
            case 'guardar-autoparte':
                $controller->guardarAutoparte();
                break;
                
            case 'editar-autoparte':
                $controller->editarAutoparte();
                break;
            
            case 'actualizar-autoparte':
                $controller->actualizarAutoparte();
                break;
            
            case 'actualizar-stock':
                $controller->actualizarStock();
                break;
            
            case 'buscar-autopartes':
                $controller->buscarAutopartes();
                break;
            
            case 'stock-bajo':
                $controller->stockBajo();
                break;
            
            // Restricción: NO puede eliminar autopartes
            case 'eliminar-autoparte':
                $controller->eliminarAutoparte();
                break;
                
            // === GESTIÓN DE COMENTARIOS ===
            case 'comentarios':
                $controller->comentarios();
                break;
            
            case 'ver-comentario':
                $controller->verComentario();
                break;
            
            case 'actualizar-comentario':
                $controller->actualizarComentario();
                break;
                
            case 'eliminar-comentario':
                $controller->eliminarComentario();
                break;
            
            // === CONSULTAS (SOLO LECTURA) ===
            case 'categorias':
                $controller->categorias();
                break;
            
            case 'ventas':
                $controller->ventas();
                break;
            
            case 'ver-venta':
                $controller->verVenta();
                break;
            
            // === PERFIL ===
            case 'perfil':
                $controller->perfil();
                break;
            
            case 'actualizar-perfil':
                $controller->actualizarPerfil();
                break;
            
            // === RESTRICCIONES - Acciones bloqueadas ===
            case 'usuarios':
            case 'crear-usuario':
            case 'editar-usuario':
            case 'eliminar-usuario':
                $controller->usuarios();
                break;
            
            case 'crear-categoria':
            case 'editar-categoria':
            case 'eliminar-categoria':
                $controller->crearCategoria();
                break;
            
            case 'estadisticas':
            case 'reportes':
                $controller->estadisticas();
                break;
                
            default:
                $controller->dashboard();
        }
        break;
    
    // =========================================================================
    // MÓDULO: CLIENTE
    // =========================================================================
    case 'cliente':
        // Verificar si es cliente autenticado
        if (!hasRole(ROL_CLIENTE)) {
            setFlashMessage(MSG_ERROR, 'Debes iniciar sesión');
            redirect('/index.php?module=auth&action=login');
        }
        
        require_once CONTROLLERS_PATH . '/ClienteController.php';
        $controller = new ClienteController();
        
        switch ($action) {
            case 'dashboard':
                $controller->dashboard();
                break;
                
            case 'perfil':
                $controller->perfil();
                break;
                
            case 'perfil-update':
                $controller->perfilUpdate();
                break;
                
            case 'carrito':
                // Redirigir al módulo carrito
                redirect('/index.php?module=carrito&action=ver');
                break;
                
            case 'cart_count':
                $controller->cart_count();
                break;
                
            default:
                $controller->dashboard();
        }
        break;
    
    // =========================================================================
    // MÓDULO: CARRITO DE COMPRAS
    // =========================================================================
    case 'carrito':
        require_once CONTROLLERS_PATH . '/CarritoController.php';
        $controller = new CarritoController();
        
        switch ($action) {
            // === VER CARRITO ===
            case 'ver':
            case 'index':
                $controller->ver();
                break;
                
            // === AGREGAR AL CARRITO ===
            case 'agregar':
            case 'add':
                $controller->agregar();
                break;
                
            // === ACTUALIZAR CANTIDAD ===
            case 'actualizar':
            case 'update':
                $controller->actualizar();
                break;
                
            // === ELIMINAR ITEM ===
            case 'eliminar':
            case 'remove':
                $controller->eliminar();
                break;
                
            // === VACIAR CARRITO ===
            case 'vaciar':
            case 'clear':
                $controller->vaciar();
                break;
                
            // === CHECKOUT ===
            case 'checkout':
                $controller->checkout();
                break;
                
            // === PROCESAR COMPRA ===
            case 'procesar':
            case 'process':
                $controller->procesar();
                break;
                
            // === CONFIRMACIÓN DE COMPRA ===
            case 'confirmacion':
            case 'confirmation':
                $controller->confirmacion();
                break;
                
            // === HISTORIAL DE COMPRAS ===
            case 'historial':
            case 'history':
                $controller->historial();
                break;
                
            // === DETALLE DE COMPRA ===
            case 'detalle_compra':
            case 'detalle-compra':
            case 'order-detail':
                $controller->detalle_compra();
                break;
                
            // === CONTADOR (AJAX) ===
            case 'contador':
            case 'count':
                $controller->contador();
                break;
                
            // === MINI CARRITO (AJAX) ===
            case 'mini':
                $controller->mini();
                break;
                
            default:
                $controller->ver();
        }
        break;
    
    // =========================================================================
    // MÓDULO: CATÁLOGO PÚBLICO
    // =========================================================================
    case 'publico':
        require_once CONTROLLERS_PATH . '/PublicController.php';
        $controller = new PublicController();
        
        switch ($action) {
            case 'catalogo':
                $controller->catalogo();
                break;
                
            case 'categoria':
                $controller->categoria();
                break;
                
            case 'detalle':
                $controller->detalle();
                break;
                
            case 'buscar':
                $controller->buscar();
                break;
                
            case 'comentar':
                $controller->comentar();
                break;
                
            case 'home':
            case 'inicio':
            default:
                $controller->home();
        }
        break;
    
    // =========================================================================
    // MÓDULO: CATÁLOGO (ALIAS)
    // =========================================================================
    case 'catalogo':
        require_once CONTROLLERS_PATH . '/CatalogoController.php';
        $controller = new CatalogoController();
        
        switch ($action) {
            case 'index':
            case 'lista':
                $controller->index();
                break;
                
            case 'detalle':
            case 'ver':
                $controller->detalle();
                break;
                
            case 'categoria':
                $controller->categoria();
                break;
                
            case 'buscar':
                $controller->buscar();
                break;
                
            default:
                $controller->index();
        }
        break;
    
    // =========================================================================
    // MÓDULO: PÚBLICO (DEFAULT)
    // =========================================================================
    case 'public':
    default:
        // Parte pública (catálogo)
        require_once CONTROLLERS_PATH . '/PublicController.php';
        $controller = new PublicController();
        
        switch ($action) {
            case 'catalogo':
                $controller->catalogo();
                break;
                
            case 'categoria':
                $controller->categoria();
                break;
                
            case 'detalle':
                $controller->detalle();
                break;
                
            case 'buscar':
                $controller->buscar();
                break;
                
            case 'comentar':
                $controller->comentar();
                break;
                
            case 'home':
            case 'inicio':
            default:
                $controller->home();
        }
        break;
}
?>