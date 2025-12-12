<?php
/**
 * Clase Database
 * Maneja la conexión a la base de datos usando PDO
 */

class Database {
    private static $instance = null;
    private $conn;
    
    // Configuración de la base de datos
    private $host = 'localhost';
    private $db_name = 'inventario_autopartes';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    /**
     * Constructor privado para implementar Singleton
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset}"
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }
    
    /**
     * Obtiene la instancia única de Database
     * 
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Obtiene la conexión PDO
     * 
     * @return PDO
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Inicia una transacción
     * 
     * @return bool
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Confirma una transacción
     * 
     * @return bool
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Revierte una transacción
     * 
     * @return bool
     */
    public function rollBack() {
        return $this->conn->rollBack();
    }
    
    /**
     * Obtiene el último ID insertado
     * 
     * @return string
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Ejecuta una consulta preparada
     * 
     * @param string $query
     * @param array $params
     * @return PDOStatement
     */
    public function query($query, $params = []) {
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch(PDOException $e) {
            throw new Exception("Error en consulta: " . $e->getMessage());
        }
    }
    
    /**
     * Ejecuta un SELECT y retorna todos los resultados
     * 
     * @param string $query
     * @param array $params
     * @return array
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Ejecuta un SELECT y retorna un solo resultado
     * 
     * @param string $query
     * @param array $params
     * @return array|false
     */
    public function fetchOne($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->fetch();
    }
    
    /**
     * Ejecuta un INSERT/UPDATE/DELETE
     * 
     * @param string $query
     * @param array $params
     * @return int Número de filas afectadas
     */
    public function execute($query, $params = []) {
        $stmt = $this->query($query, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Previene la clonación del objeto
     */
    private function __clone() {}
    
    /**
     * Previene la deserialización del objeto
     */
    public function __wakeup() {
        throw new Exception("No se puede deserializar un singleton");
    }
}
?>