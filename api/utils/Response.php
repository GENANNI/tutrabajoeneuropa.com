<?php
/**
 * Clase Response para manejar respuestas JSON consistentes
 */

class Response {
    /**
     * Enviar respuesta de éxito
     * 
     * @param mixed $data Datos a enviar
     * @param int $statusCode Código HTTP
     */
    public static function success($data = null, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => true,
            'data' => $data,
        ];
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Enviar respuesta de error
     * 
     * @param string $message Mensaje de error
     * @param int $statusCode Código HTTP
     * @param mixed $errors Errores adicionales
     */
    public static function error($message, $statusCode = 400, $errors = null) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        $response = [
            'success' => false,
            'message' => $message,
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Enviar respuesta de validación fallida
     * 
     * @param array $errors Errores de validación
     */
    public static function validationError($errors) {
        self::error('Validación fallida', 422, $errors);
    }

    /**
     * Enviar respuesta no autorizada
     * 
     * @param string $message Mensaje de error
     */
    public static function unauthorized($message = 'No autorizado') {
        self::error($message, 401);
    }

    /**
     * Enviar respuesta no encontrada
     * 
     * @param string $message Mensaje de error
     */
    public static function notFound($message = 'Recurso no encontrado') {
        self::error($message, 404);
    }

    /**
     * Enviar respuesta de error interno del servidor
     * 
     * @param string $message Mensaje de error
     */
    public static function internalError($message = 'Error interno del servidor') {
        self::error($message, 500);
    }
}
