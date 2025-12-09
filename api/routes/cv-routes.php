<?php
/**
 * Rutas para gestión de CVs
 * Reemplaza cv-router.ts de Node.js
 */

$cvService = new CVService();

// Obtener el método y ruta de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/cv', '', $path);

try {
    // POST /api/cv/upload - Subir un CV
    if ($method === 'POST' && $path === '/upload') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validar entrada
        $validator = new Validator();
        $validator->required($input['userId'] ?? null, 'userId');
        $validator->required($input['filename'] ?? null, 'filename');
        $validator->required($input['content'] ?? null, 'content');
        
        if ($validator->hasErrors()) {
            Response::validationError($validator->getErrors());
        }
        
        // Subir CV
        $result = $cvService->uploadCV(
            $input['userId'],
            $input['filename'],
            $input['content']
        );
        
        Response::success([
            'success' => true,
            'message' => 'CV uploaded successfully',
            'data' => $result,
        ], 201);
    }
    
    // GET /api/cv/list - Listar CVs
    else if ($method === 'GET' && $path === '/list') {
        $cvs = $cvService->listCVs();
        Response::success($cvs);
    }
    
    // GET /api/cv/:id - Obtener un CV
    else if ($method === 'GET' && preg_match('/^\/([a-f0-9\-]+)$/', $path, $matches)) {
        $id = $matches[1];
        $cv = $cvService->getCV($id);
        
        if (!$cv) {
            Response::notFound('CV no encontrado');
        }
        
        Response::success($cv);
    }
    
    // DELETE /api/cv/:id - Eliminar un CV
    else if ($method === 'DELETE' && preg_match('/^\/([a-f0-9\-]+)$/', $path, $matches)) {
        $id = $matches[1];
        
        $success = $cvService->deleteCV($id);
        
        if (!$success) {
            Response::notFound('CV no encontrado');
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
