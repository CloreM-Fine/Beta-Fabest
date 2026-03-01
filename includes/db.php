<?php
/**
 * Anteas Lucca - Database Connection
 * PDO MySQL with error handling
 */

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/**
 * Get database connection
 * 
 * @return PDO
 * @throws PDOException
 */
function getDB(): PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci"
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            throw new PDOException("Errore di connessione al database. Riprova più tardi.");
        }
    }
    
    return $pdo;
}

/**
 * Execute a prepared query
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return PDOStatement
 */
function executeQuery(string $sql, array $params = []): PDOStatement {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Get single record
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array|null
 */
function fetchOne(string $sql, array $params = []): ?array {
    $stmt = executeQuery($sql, $params);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Get multiple records
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return array
 */
function fetchAll(string $sql, array $params = []): array {
    $stmt = executeQuery($sql, $params);
    return $stmt->fetchAll();
}

/**
 * Insert record and get last ID
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return int Last insert ID
 */
function insert(string $sql, array $params = []): int {
    $db = getDB();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return (int) $db->lastInsertId();
}

/**
 * Update records and get affected rows
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return int Affected rows
 */
function update(string $sql, array $params = []): int {
    $stmt = executeQuery($sql, $params);
    return $stmt->rowCount();
}

/**
 * Delete records and get affected rows
 * 
 * @param string $sql SQL query
 * @param array $params Parameters
 * @return int Affected rows
 */
function delete(string $sql, array $params = []): int {
    return update($sql, $params);
}
