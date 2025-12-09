<?php
/**
 * Rutas para administración
 * Reemplaza admin-router.ts de Node.js
 */

$userService = new UserService();

// Obtener el método y ruta de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/admin', '', $path);

try {
    // GET /api/admin/stats - Obtener estadísticas
    if ($method === 'GET' && $path === '/stats') {
        $stats = $userService->getStats();
        Response::success($stats);
    }
    
    // GET /api/admin/users - Listar usuarios
    else if ($method === 'GET' && $path === '/users') {
        $users = $userService->listUsers();
        Response::success($users);
    }
    
    // DELETE /api/admin/users/:id - Eliminar usuario
    else if ($method === 'DELETE' && preg_match('/^\/users\/([a-f0-9\-]+)$/', $path, $matches)) {
        $id = $matches[1];
        
        $success = $userService->deleteUser($id);
        
        if (!$success) {
            Response::notFound('Usuario no encontrado');
        }
        
        Response::success(['success' => true]);
    }
    
    // Ruta no encontrada
    else {
        Response::notFound('Ruta no encontrada');
    }
} catch (Exception $e) {
    Response::internalError($e->getMessage());
}
