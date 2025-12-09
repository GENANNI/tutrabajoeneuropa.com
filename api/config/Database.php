<?php
/**
 * Clase Database para gestión de conexiones MySQL
 * Utiliza PDO para conexiones seguras y preparadas
 */

class Database {
    private static $instance = null;
    private $connection;
    private $lastError = null;

    private function __construct() {
        $this->connect();
    }

    /**
     * Obtener instancia singleton de la base de datos
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Conectar a la base de datos
     */
    private function connect() {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            
            $this->connection = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                ]
            );
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            if (APP_DEBUG) {
                throw new Exception('Error de conexión a la base de datos: ' . $e->getMessage());
            } else {
                throw new Exception('Error de conexión a la base de datos');
            }
        }
    }

    /**
     * Ejecutar una consulta preparada
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para la consulta
     * @return PDOStatement
     */
    public function execute($query, $params = []) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            $this->lastError = $e->getMessage();
            throw new Exception('Error en la consulta: ' . ($APP_DEBUG ? $e->getMessage() : ''));
        }
    }

    /**
     * Obtener un registro
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para la consulta
     * @return array|null
     */
    public function fetchOne($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener múltiples registros
     * 
     * @param string $query Consulta SQL
     * @param array $params Parámetros para la consulta
     * @return array
     */
    public function fetchAll($query, $params = []) {
        $stmt = $this->execute($query, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insertar un registro
     * 
     * @param string $table Nombre de la tabla
     * @param array $data Datos a insertar
     * @return string ID del registro insertado
     */
    public function insert($table, $data) {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');
        
        $query = 'INSERT INTO ' . $table . ' (' . implode(',', $columns) . ') VALUES (' . implode(',', $placeholders) . ')';
        
        $this->execute($query, array_values($data));
        return $this->connection->lastInsertId();
    }

    /**
     * Actualizar registros
     * 
     * @param string $table Nombre de la tabla
     * @param array $data Datos a actualizar
     * @param string $where Condición WHERE
     * @param array $whereParams Parámetros para la condición WHERE
     * @return int Número de filas afectadas
     */
    public function update($table, $data, $where, $whereParams = []) {
        $set = [];
        foreach (array_keys($data) as $column) {
            $set[] = $column . ' = ?';
        }
        
        $query = 'UPDATE ' . $table . ' SET ' . implode(',', $set) . ' WHERE ' . $where;
        $params = array_merge(array_values($data), $whereParams);
        
        $stmt = $this->execute($query, $params);
        return $stmt->rowCount();
    }

    /**
     * Eliminar registros
     * 
     * @param string $table Nombre de la tabla
     * @param string $where Condición WHERE
     * @param array $whereParams Parámetros para la condición WHERE
     * @return int Número de filas afectadas
     */
    public function delete($table, $where, $whereParams = []) {
        $query = 'DELETE FROM ' . $table . ' WHERE ' . $where;
        $stmt = $this->execute($query, $whereParams);
        return $stmt->rowCount();
    }

    /**
     * Obtener el último error
     */
    public function getLastError() {
        return $this->lastError;
    }

    /**
     * Cerrar conexión
     */
    public function close() {
        $this->connection = null;
    }
}
