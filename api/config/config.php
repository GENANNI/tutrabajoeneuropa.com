<?php
/**
 * Configuración principal de la aplicación
 * Carga variables de entorno y establece constantes globales
 */

// Cargar variables de entorno desde .env
if (file_exists(__DIR__ . '/../../.env')) {
    $env = parse_ini_file(__DIR__ . '/../../.env');
    foreach ($env as $key => $value) {
        putenv("$key=$value");
    }
}

// Configuración de base de datos
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'tutrabajoeneuropa');
define('DB_PORT', getenv('DB_PORT') ?: 3306);

// Configuración de cifrado
define('CRYPTO_KEY', getenv('CRYPTO_KEY') ?: 'default-key-change-in-production');

// Configuración de aplicación
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', getenv('APP_DEBUG') ?: true);

// Configuración de CORS
define('ALLOWED_ORIGINS', [
    'http://localhost:3000',
    'http://localhost:5173',
    'http://localhost',
    getenv('FRONTEND_URL') ?: 'http://localhost:3000'
]);

// Configuración de respuestas
define('JSON_RESPONSE_CHARSET', 'utf-8');

// Configuración de archivos
define('UPLOAD_DIR', __DIR__ . '/../../uploads/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10 MB

// Crear directorio de uploads si no existe
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Configuración de sesión
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', APP_ENV === 'production');
ini_set('session.cookie_samesite', 'Lax');

// Configuración de errores
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// Configuración de zona horaria
date_default_timezone_set('UTC');
