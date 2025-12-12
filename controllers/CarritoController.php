<?php
/**
 * Controlador Carrito
 * Maneja todas las operaciones del carrito de compras
 * 
 * @author Grupo 1SF131
 * @version 1.0
 */

// Cargar dependencias necesarias
if (!class_exists('Database')) {
    require_once __DIR__ . '/../config/Database.php';
}
if (!class_exists('Carrito')) {
    require_once __DIR__ . '/../models/Carrito.php';
}

class CarritoController {
    
    private $carrito;
    private $usuarioId;
    
    public function __construct() {
        // Verificar que sea cliente autenticado
        if (!isAuthenticated()) {
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Debe iniciar sesión para acceder al carrito',
                    'redirect' => BASE_URL . '/index.php?module=auth&action=login'
                ]);
            }
            setFlashMessage(MSG_WARNING, 'Debe iniciar sesión para acceder al carrito');
            redirect('/index.php?module=auth&action=login');
        }
        
        if (!hasRole(ROL_CLIENTE)) {
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => false,
                    'message' => 'Solo los clientes pueden usar el carrito'
                ]);
            }
            setFlashMessage(MSG_ERROR, 'Solo los clientes pueden usar el carrito');
            redirect('/index.php');
        }
        
        $this->carrito = new Carrito();
        $this->usuarioId = $_SESSION['usuario_id'];
        
        // Actualizar contador en sesión
        $this->actualizarContadorSesion();
    }
    
    /**
     * Verifica si es una petición AJAX
     */
    private function isAjax() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
    
    /**
     * Actualiza el contador del carrito en la sesión
     */
    private function actualizarContadorSesion() {
        $_SESSION['carrito_items'] = $this->carrito->contarItems($this->usuarioId);
    }
    
    /**
     * Muestra la vista del carrito
     */
    public function ver() {
        try {
            $items = $this->carrito->obtenerCarrito($this->usuarioId);
            $totales = $this->carrito->calcularTotales($this->usuarioId);
            
            $pageTitle = 'Mi Carrito de Compras';
            
            require_once VIEWS_PATH . '/cliente/Carrito.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar el carrito');
            redirect('/index.php');
        }
    }
    
    /**
     * Agrega un producto al carrito (AJAX)
     */
    public function agregar() {
        try {
            // Verificar CSRF en peticiones POST
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $autoparteId = filter_input(INPUT_POST, 'autoparte_id', FILTER_VALIDATE_INT);
            $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT) ?: 1;
            
            if (!$autoparteId) {
                throw new Exception('Producto no válido');
            }
            
            if ($cantidad < 1) {
                $cantidad = 1;
            }
            
            $this->carrito->agregar($this->usuarioId, $autoparteId, $cantidad);
            $this->actualizarContadorSesion();
            
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => true,
                    'message' => 'Producto agregado al carrito',
                    'cart_count' => $_SESSION['carrito_items']
                ]);
            }
            
            setFlashMessage(MSG_SUCCESS, 'Producto agregado al carrito');
            redirect('/index.php?module=carrito&action=ver');
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
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
     * Actualiza la cantidad de un item (AJAX)
     */
    public function actualizar() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $autoparteId = filter_input(INPUT_POST, 'autoparte_id', FILTER_VALIDATE_INT);
            $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
            
            if (!$autoparteId) {
                throw new Exception('Producto no válido');
            }
            
            if ($cantidad === null || $cantidad === false) {
                throw new Exception('Cantidad no válida');
            }
            
            $this->carrito->actualizarCantidad($this->usuarioId, $autoparteId, $cantidad);
            $this->actualizarContadorSesion();
            
            $totales = $this->carrito->calcularTotales($this->usuarioId);
            
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => true,
                    'message' => 'Cantidad actualizada',
                    'cart_count' => $_SESSION['carrito_items'],
                    'totales' => [
                        'subtotal' => formatCurrency($totales['subtotal']),
                        'itbms' => formatCurrency($totales['itbms']),
                        'total' => formatCurrency($totales['total'])
                    ]
                ]);
            }
            
            setFlashMessage(MSG_SUCCESS, 'Cantidad actualizada');
            redirect('/index.php?module=carrito&action=ver');
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            setFlashMessage(MSG_ERROR, $e->getMessage());
            redirect('/index.php?module=carrito&action=ver');
        }
    }
    
    /**
     * Elimina un item del carrito
     */
    public function eliminar() {
        try {
            $autoparteId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) 
                         ?: filter_input(INPUT_POST, 'autoparte_id', FILTER_VALIDATE_INT);
            
            if (!$autoparteId) {
                throw new Exception('Producto no válido');
            }
            
            $this->carrito->eliminar($this->usuarioId, $autoparteId);
            $this->actualizarContadorSesion();
            
            if ($this->isAjax()) {
                $totales = $this->carrito->calcularTotales($this->usuarioId);
                jsonResponse([
                    'success' => true,
                    'message' => 'Producto eliminado del carrito',
                    'cart_count' => $_SESSION['carrito_items'],
                    'totales' => [
                        'subtotal' => formatCurrency($totales['subtotal']),
                        'itbms' => formatCurrency($totales['itbms']),
                        'total' => formatCurrency($totales['total'])
                    ]
                ]);
            }
            
            setFlashMessage(MSG_SUCCESS, 'Producto eliminado del carrito');
            redirect('/index.php?module=carrito&action=ver');
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            setFlashMessage(MSG_ERROR, $e->getMessage());
            redirect('/index.php?module=carrito&action=ver');
        }
    }
    
    /**
     * Vacía todo el carrito
     */
    public function vaciar() {
        try {
            $this->carrito->vaciar($this->usuarioId);
            $this->actualizarContadorSesion();
            
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => true,
                    'message' => 'Carrito vaciado',
                    'cart_count' => 0
                ]);
            }
            
            setFlashMessage(MSG_SUCCESS, 'Carrito vaciado exitosamente');
            redirect('/index.php?module=carrito&action=ver');
            
        } catch (Exception $e) {
            if ($this->isAjax()) {
                jsonResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            setFlashMessage(MSG_ERROR, $e->getMessage());
            redirect('/index.php?module=carrito&action=ver');
        }
    }
    
    /**
     * Muestra la vista de checkout
     */
    public function checkout() {
        try {
            $items = $this->carrito->obtenerCarrito($this->usuarioId);
            
            if (empty($items)) {
                setFlashMessage(MSG_WARNING, 'Tu carrito está vacío');
                redirect('/index.php?module=carrito&action=ver');
            }
            
            // Verificar stock
            $sinStock = $this->carrito->verificarStock($this->usuarioId);
            if (!empty($sinStock)) {
                $mensajes = [];
                foreach ($sinStock as $item) {
                    $mensajes[] = "{$item['nombre']}: solicitas {$item['cantidad_carrito']}, disponible {$item['stock_disponible']}";
                }
                setFlashMessage(MSG_ERROR, 'Stock insuficiente: ' . implode(', ', $mensajes));
                redirect('/index.php?module=carrito&action=ver');
            }
            
            $totales = $this->carrito->calcularTotales($this->usuarioId);
            
            $pageTitle = 'Finalizar Compra';
            
            require_once VIEWS_PATH . '/cliente/checkout.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al procesar el checkout');
            redirect('/index.php?module=carrito&action=ver');
        }
    }
    
    /**
     * Procesa la compra
     */
    public function procesar() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            // Verificar CSRF
            $csrfToken = $_POST['csrf_token'] ?? '';
            if (!verifyCSRFToken($csrfToken)) {
                throw new Exception('Token de seguridad inválido');
            }
            
            $ventaId = $this->carrito->procesarCompra($this->usuarioId);
            
            if ($ventaId) {
                $this->actualizarContadorSesion();
                
                setFlashMessage(MSG_SUCCESS, '¡Compra realizada exitosamente! Tu número de orden es: #' . $ventaId);
                redirect('/index.php?module=carrito&action=confirmacion&id=' . $ventaId);
            } else {
                throw new Exception('Error al procesar la compra');
            }
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, $e->getMessage());
            redirect('/index.php?module=carrito&action=checkout');
        }
    }
    
    /**
     * Muestra la confirmación de compra
     */
    public function confirmacion() {
        try {
            $ventaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            
            if (!$ventaId) {
                throw new Exception('Orden no válida');
            }
            
            $compra = $this->carrito->obtenerDetalleCompra($ventaId, $this->usuarioId);
            
            if (!$compra) {
                throw new Exception('Orden no encontrada');
            }
            
            $pageTitle = 'Confirmación de Compra #' . $ventaId;
            
            require_once VIEWS_PATH . '/cliente/confirmacion.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, $e->getMessage());
            redirect('/index.php?module=cliente&action=dashboard');
        }
    }
    
    /**
     * Muestra el historial de compras
     */
    public function historial() {
        try {
            $compras = $this->carrito->obtenerHistorialCompras($this->usuarioId, 20);
            
            $pageTitle = 'Historial de Compras';
            
            require_once VIEWS_PATH . '/cliente/historial.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, 'Error al cargar el historial');
            redirect('/index.php?module=cliente&action=dashboard');
        }
    }
    
    /**
     * Muestra el detalle de una compra
     */
    public function detalle_compra() {
        try {
            $ventaId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            
            if (!$ventaId) {
                throw new Exception('Orden no válida');
            }
            
            $compra = $this->carrito->obtenerDetalleCompra($ventaId, $this->usuarioId);
            
            if (!$compra) {
                throw new Exception('Orden no encontrada');
            }
            
            $pageTitle = 'Detalle de Compra #' . $ventaId;
            
            require_once VIEWS_PATH . '/cliente/detalle_compra.php';
            
        } catch (Exception $e) {
            setFlashMessage(MSG_ERROR, $e->getMessage());
            redirect('/index.php?module=carrito&action=historial');
        }
    }
    
    /**
     * Obtiene el contador del carrito (AJAX)
     */
    public function contador() {
        jsonResponse([
            'success' => true,
            'count' => $this->carrito->contarItems($this->usuarioId)
        ]);
    }
    
    /**
     * Obtiene el mini-carrito (AJAX)
     */
    public function mini() {
        try {
            $items = $this->carrito->obtenerCarrito($this->usuarioId);
            $totales = $this->carrito->calcularTotales($this->usuarioId);
            
            // Solo los primeros 3 items para el mini-carrito
            $items = array_slice($items, 0, 3);
            
            jsonResponse([
                'success' => true,
                'items' => $items,
                'totales' => $totales,
                'cart_count' => $_SESSION['carrito_items']
            ]);
            
        } catch (Exception $e) {
            jsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>