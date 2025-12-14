<?php
/**
 * Controlador del Catálogo Público
 * Permite a invitados y clientes ver las autopartes disponibles
 * Cumple con requisitos 8 y 9: Página pública con catálogo y detalle
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

class CatalogoController {
    
    private $autoparteModel;
    private $db;
    
    public function __construct() {
        $this->autoparteModel = new Autoparte();
        $this->db = Database::getInstance();
    }
    
    /**
     * Página principal del catálogo
     * Cumple con requisito 8: Página pública con listado de autopartes
     */
    public function index() {
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
                'orden' => $_GET['orden'] ?? 'fecha_creacion',
                'direccion' => $_GET['direccion'] ?? 'DESC',
                'estado' => 1 // Solo activos
            ];
            
            // Paginación
            $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $porPagina = 12; // 12 productos por página para grid
            
            $filtros['limite'] = $porPagina;
            $filtros['offset'] = ($pagina - 1) * $porPagina;
            
            // Obtener autopartes
            $autopartes = $this->autoparteModel->obtenerParaCatalogo($filtros);
            
            // Contar total para paginación (con los mismos filtros aplicados)
            $filtrosConteo = [
                'estado' => 1,
                'buscar' => $filtros['buscar'],
                'categoria_id' => $filtros['categoria_id'],
                'marca' => $filtros['marca'],
                'modelo' => $filtros['modelo'],
                'anio' => $filtros['anio'],
                'precio_min' => $filtros['precio_min'],
                'precio_max' => $filtros['precio_max']
            ];
            $totalAutopartes = $this->autoparteModel->contarTodos($filtrosConteo);
            $totalPaginas = ceil($totalAutopartes / $porPagina);
            
            // Obtener datos para filtros
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            $anios = $this->autoparteModel->obtenerAnios();
            
            // Variables para la vista
            $pageTitle = 'Catálogo de Autopartes - Rastro';
            
            // Incluir vista
            require_once VIEWS_PATH . '/publico/catalogo.php';
            
        } catch (Exception $e) {
            // Log del error
            error_log("Error en catálogo: " . $e->getMessage());
            
            // Mostrar página de error amigable
            $pageTitle = 'Error - Rastro';
            $errorMessage = 'Lo sentimos, ha ocurrido un error al cargar el catálogo.';
            require_once VIEWS_PATH . '/publico/error.php';
        }
    }
    
    /**
     * Ver detalle de una autoparte
     * Cumple con requisito 9: Detalle con imagen, costo, unidades y comentarios
     */
    public function detalle() {
        try {
            $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if (!$id) {
                redirect('/index.php?module=publico&action=catalogo');
            }
            
            // Obtener autoparte
            $autoparte = $this->autoparteModel->obtenerPorId($id);
            
            // Verificar que existe y está activa
            if (!$autoparte || $autoparte['estado'] != 1) {
                setFlashMessage(MSG_ERROR, 'Autoparte no encontrada o no disponible');
                redirect('/index.php?module=publico&action=catalogo');
            }
            
            // Obtener comentarios aprobados
            $comentarios = $this->obtenerComentarios($id);
            
            // Obtener autopartes relacionadas (misma categoría)
            $relacionadas = $this->obtenerRelacionadas($id, $autoparte['categoria_id']);
            
            // Variables para la vista
            $pageTitle = $autoparte['nombre'] . ' - Rastro';
            
            // Incluir vista
            require_once VIEWS_PATH . '/publico/detalle.php';
            
        } catch (Exception $e) {
            error_log("Error en detalle: " . $e->getMessage());
            setFlashMessage(MSG_ERROR, 'Error al cargar el detalle');
            redirect('/index.php?module=publico&action=catalogo');
        }
    }
    
    /**
     * Ver autopartes por categoría
     */
    public function categoria() {
        try {
            $categoriaId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            
            if (!$categoriaId) {
                redirect('/index.php?module=publico&action=catalogo');
            }
            
            // Obtener información de la categoría
            $categoria = $this->obtenerCategoriaPorId($categoriaId);
            
            if (!$categoria) {
                setFlashMessage(MSG_ERROR, 'Categoría no encontrada');
                redirect('/index.php?module=publico&action=catalogo');
            }
            
            // Obtener filtros
            $filtros = [
                'categoria_id' => $categoriaId,
                'buscar' => $_GET['buscar'] ?? '',
                'marca' => $_GET['marca'] ?? '',
                'anio' => $_GET['anio'] ?? '',
                'orden' => $_GET['orden'] ?? 'fecha_creacion',
                'direccion' => $_GET['direccion'] ?? 'DESC',
                'estado' => 1
            ];
            
            // Paginación
            $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
            $porPagina = 12;
            
            $filtros['limite'] = $porPagina;
            $filtros['offset'] = ($pagina - 1) * $porPagina;
            
            // Obtener autopartes
            $autopartes = $this->autoparteModel->obtenerParaCatalogo($filtros);
            
            // Contar total
            $totalAutopartes = $this->autoparteModel->contarTodos(['categoria_id' => $categoriaId, 'estado' => 1]);
            $totalPaginas = ceil($totalAutopartes / $porPagina);
            
            // Obtener datos para filtros
            $categorias = $this->obtenerCategorias();
            $marcas = $this->autoparteModel->obtenerMarcas();
            $anios = $this->autoparteModel->obtenerAnios();
            
            // Variables para la vista
            $pageTitle = $categoria['nombre'] . ' - Catálogo - Rastro';
            
            // Incluir vista (reutiliza catálogo)
            require_once VIEWS_PATH . '/publico/catalogo.php';
            
        } catch (Exception $e) {
            error_log("Error en categoría: " . $e->getMessage());
            redirect('/index.php?module=publico&action=catalogo');
        }
    }
    
    /**
     * Buscar autopartes (AJAX)
     */
    public function buscar() {
        try {
            $termino = Validator::sanitizeString($_GET['q'] ?? '');
            
            if (strlen($termino) < 2) {
                jsonResponse(['results' => []]);
            }
            
            $filtros = [
                'buscar' => $termino,
                'estado' => 1,
                'limite' => 8
            ];
            
            $autopartes = $this->autoparteModel->obtenerParaCatalogo($filtros);
            
            $results = array_map(function($item) {
                return [
                    'id' => $item['id'],
                    'nombre' => $item['nombre'],
                    'marca' => $item['marca'],
                    'modelo' => $item['modelo'],
                    'anio' => $item['anio'],
                    'precio' => number_format($item['precio'], 2),
                    'imagen' => $item['imagen_thumb'] ? UPLOADS_URL . '/' . $item['imagen_thumb'] : null,
                    'url' => BASE_URL . '/index.php?module=publico&action=detalle&id=' . $item['id']
                ];
            }, $autopartes);
            
            jsonResponse(['results' => $results]);
            
        } catch (Exception $e) {
            jsonResponse(['results' => [], 'error' => $e->getMessage()]);
        }
    }
    
    /**
     * Agregar comentario a una autoparte
     * Cumple con requisito 9: Permitir agregar comentarios
     */
    public function agregarComentario() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                jsonResponse(['success' => false, 'message' => 'Método no permitido']);
            }
            
            // Verificar que el usuario esté logueado
            if (!isLoggedIn()) {
                jsonResponse(['success' => false, 'message' => 'Debes iniciar sesión para comentar']);
            }
            
            $autoparteId = Validator::sanitizeInt($_POST['autoparte_id'] ?? 0);
            $comentario = Validator::sanitizeString($_POST['comentario'] ?? '');
            $calificacion = Validator::sanitizeInt($_POST['calificacion'] ?? 5);
            
            // Validaciones
            if (!$autoparteId) {
                jsonResponse(['success' => false, 'message' => 'Autoparte no válida']);
            }
            
            if (empty($comentario) || strlen($comentario) < 10) {
                jsonResponse(['success' => false, 'message' => 'El comentario debe tener al menos 10 caracteres']);
            }
            
            if ($calificacion < 1 || $calificacion > 5) {
                $calificacion = 5;
            }
            
            // Verificar que la autoparte existe y está activa
            $autoparte = $this->autoparteModel->obtenerPorId($autoparteId);
            if (!$autoparte || $autoparte['estado'] != 1) {
                jsonResponse(['success' => false, 'message' => 'Autoparte no disponible']);
            }
            
            // Insertar comentario (estado 0 = pendiente de aprobación)
            $query = "INSERT INTO comentarios (autoparte_id, usuario_id, comentario, calificacion, estado)
                     VALUES (:autoparte_id, :usuario_id, :comentario, :calificacion, 0)";
            
            $result = $this->db->execute($query, [
                ':autoparte_id' => $autoparteId,
                ':usuario_id' => $_SESSION['usuario_id'],
                ':comentario' => $comentario,
                ':calificacion' => $calificacion
            ]);
            
            if ($result) {
                jsonResponse([
                    'success' => true, 
                    'message' => 'Comentario enviado. Será visible después de ser aprobado.'
                ]);
            } else {
                jsonResponse(['success' => false, 'message' => 'Error al guardar el comentario']);
            }
            
        } catch (Exception $e) {
            jsonResponse(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * Obtiene los comentarios aprobados de una autoparte
     * 
     * @param int $autoparteId
     * @return array
     */
    private function obtenerComentarios($autoparteId) {
        try {
            $query = "SELECT c.*, u.nombre as usuario_nombre
                     FROM comentarios c
                     INNER JOIN usuarios u ON c.usuario_id = u.id
                     WHERE c.autoparte_id = :autoparte_id AND c.estado = 1
                     ORDER BY c.fecha_creacion DESC
                     LIMIT 20";
            
            return $this->db->fetchAll($query, [':autoparte_id' => $autoparteId]);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtiene autopartes relacionadas
     * 
     * @param int $autoparteId ID a excluir
     * @param int $categoriaId
     * @return array
     */
    private function obtenerRelacionadas($autoparteId, $categoriaId) {
        try {
            $query = "SELECT * FROM autopartes 
                     WHERE categoria_id = :categoria_id 
                     AND id != :id 
                     AND estado = 1 
                     AND stock > 0
                     ORDER BY RAND()
                     LIMIT 4";
            
            return $this->db->fetchAll($query, [
                ':categoria_id' => $categoriaId,
                ':id' => $autoparteId
            ]);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtiene todas las categorías activas
     * 
     * @return array
     */
    private function obtenerCategorias() {
        try {
            $query = "SELECT c.*, COUNT(a.id) as total_autopartes
                     FROM categorias c
                     LEFT JOIN autopartes a ON c.id = a.categoria_id AND a.estado = 1
                     WHERE c.estado = 1
                     GROUP BY c.id
                     ORDER BY c.nombre";
            
            return $this->db->fetchAll($query);
            
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Obtiene una categoría por ID
     * 
     * @param int $id
     * @return array|false
     */
    private function obtenerCategoriaPorId($id) {
        try {
            $query = "SELECT * FROM categorias WHERE id = :id AND estado = 1";
            return $this->db->fetchOne($query, [':id' => $id]);
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Página de inicio del sitio público
     */
    public function inicio() {
        try {
            // Autopartes destacadas (más recientes)
            $destacadas = $this->autoparteModel->obtenerParaCatalogo([
                'estado' => 1,
                'orden' => 'fecha_creacion',
                'direccion' => 'DESC',
                'limite' => 8
            ]);
            
            // Categorías con conteo
            $categorias = $this->obtenerCategorias();
            
            // Estadísticas para mostrar
            $totalAutopartes = $this->autoparteModel->contarTodos(['estado' => 1]);
            $totalCategorias = count($categorias);
            $totalMarcas = count($this->autoparteModel->obtenerMarcas());
            
            // Variables para la vista
            $pageTitle = 'Rastro - Autopartes';
            
            require_once VIEWS_PATH . '/publico/inicio.php';
            
        } catch (Exception $e) {
            error_log("Error en inicio: " . $e->getMessage());
            require_once VIEWS_PATH . '/publico/inicio.php';
        }
    }
}
?>