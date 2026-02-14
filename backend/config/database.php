<?php
/**
 * Database Configuration
 */

/**
 * Database credentials
 *
 * Supports environment variables for cPanel/production, with sane defaults for local dev.
 *
 * Env vars (preferred on production):
 * - HAMRODIGICART_DB_HOST
 * - HAMRODIGICART_DB_PORT
 * - HAMRODIGICART_DB_NAME
 * - HAMRODIGICART_DB_USER
 * - HAMRODIGICART_DB_PASS
 * - HAMRODIGICART_DB_CHARSET
 */
function hamro_env($key, $default = null) {
    $val = getenv($key);
    if ($val === false || $val === null) return $default;
    $val = trim((string)$val);
    return $val === '' ? $default : $val;
}

// Database credentials (env overrides take precedence)
define('DB_HOST', hamro_env('HAMRODIGICART_DB_HOST', 'localhost'));
define('DB_PORT', hamro_env('HAMRODIGICART_DB_PORT', '3308')); // local default kept; set 3306 on cPanel
define('DB_NAME', hamro_env('HAMRODIGICART_DB_NAME', 'hamrodigicart'));
define('DB_USER', hamro_env('HAMRODIGICART_DB_USER', 'root'));
define('DB_PASS', hamro_env('HAMRODIGICART_DB_PASS', ''));
define('DB_CHARSET', hamro_env('HAMRODIGICART_DB_CHARSET', 'utf8mb4'));

/**
 * Get database connection
 */
function getDBConnection() {
    try {
        $portPart = (is_string(DB_PORT) && trim(DB_PORT) !== '') ? (';port=' . DB_PORT) : '';
        $dsn = "mysql:host=" . DB_HOST . $portPart . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

/**
 * Send JSON response
 */
function sendJSON($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Send error response
 */
function sendError($message, $statusCode = 400) {
    sendJSON(['success' => false, 'error' => $message], $statusCode);
}

/**
 * Send success response
 */
function sendSuccess($data = [], $message = 'Success') {
    sendJSON(['success' => true, 'message' => $message, 'data' => $data]);
}

