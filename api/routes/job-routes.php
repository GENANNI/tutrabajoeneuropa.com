<?php
/**
 * Rutas para gestión de trabajos
 * Reemplaza job-router.ts de Node.js
 */

$jobService = new JobService();

// Obtener el método y ruta de la solicitud
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/api/jobs', '', $path);

try {
    // GET /api/jobs/search - Buscar trabajos
    if ($method === 'GET' && $path === '/search') {
        $query = $_GET['q'] ?? '';
        
        if (empty($query)) {
            Response::validationError(['q' => 'El parámetro de búsqueda es requerido']);
        }
        
        $jobs = $jobService->searchJobs($query);
        Response::success($jobs);
    }
    
    // GET /api/jobs - Listar todos los trabajos
    else if ($method === 'GET' && $path === '') {
        $jobs = $jobService->getAllJobs();
        Response::success($jobs);
    }
    
    // GET /api/jobs/:id - Obtener un trabajo
    else if ($method === 'GET' && preg_match('/^\/([a-f0-9\-]+)$/', $path, $matches)) {
        $id = $matches[1];
        $job = $jobService->getJobById($id);
        
        if (!$job) {
            Response::notFound('Trabajo no encontrado');
        }
        
        Response::success($job);
    }
    
    // POST /api/jobs - Crear un nuevo trabajo
    else if ($method === 'POST' && $path === '') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        // Validar entrada
        $validator = new Validator();
        $validator->required($input['title'] ?? null, 'title');
        $validator->required($input['company'] ?? null, 'company');
        $validator->required($input['location'] ?? null, 'location');
        $validator->required($input['description'] ?? null, 'description');
        
        if ($validator->hasErrors()) {
            Response::validationError($validator->getErrors());
        }
        
        // Crear trabajo
        $result = $jobService->createJob($input);
        Response::success($result, 201);
    }
    
    // DELETE /api/jobs/:id - Eliminar un trabajo
    else if ($method === 'DELETE' && preg_match('/^\/([a-f0-9\-]+)$/', $path, $matches)) {
        $id = $matches[1];
        
        $success = $jobService->deleteJob($id);
        
        if (!$success) {
            Response::notFound('Trabajo no encontrado');
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
