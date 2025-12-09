<?php
/**
 * Archivo principal del backend PHP
 * Punto de entrada para todas las solicitudes API
 */

// Cargar configuración
require_once __DIR__ . '/config/config.php';

// Cargar clases
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/utils/Crypto.php';
require_once __DIR__ . '/utils/Response.php';
require_once __DIR__ . '/utils/Validator.php';
require_once __DIR__ . '/middleware/CorsMiddleware.php';
require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/CVService.php';
require_once __DIR__ . '/services/JobService.php';

// Procesar CORS
CorsMiddleware::handle();

// Obtener la ruta solicitada
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api', '', $path);

// Enrutar solicitudes
try {
    // Health check
    if ($path === '/health') {
        Response::success(['status' => 'ok']);
    }
    
    // Rutas de CV
    else if (strpos($path, '/cv') === 0) {
        require_once __DIR__ . '/routes/cv-routes.php';
    }
    
    // Rutas de trabajos
    else if (strpos($path, '/jobs') === 0) {
        require_once __DIR__ . '/routes/job-routes.php';
    }
    
    // Rutas de administración
    else if (strpos($path, '/admin') === 0) {
        require_once __DIR__ . '/routes/admin-routes.php';
    }
    
    // Ruta no encontrada
    else {
        Response::notFound('Ruta no encontrada');
    }
} catch (Exception $e) {
    Response::internalError($e->getMessage());
}
