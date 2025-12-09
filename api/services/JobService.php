<?php
/**
 * Servicio para gestión de trabajos
 * Replica la funcionalidad de job-service.ts
 */

class JobService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Buscar trabajos por consulta
     * 
     * @param string $query Consulta de búsqueda
     * @return array
     */
    public function searchJobs($query) {
        try {
            $searchQuery = '%' . $query . '%';
            
            return $this->db->fetchAll(
                'SELECT * FROM jobs WHERE title LIKE ? AND published = 1 LIMIT 20',
                [$searchQuery]
            );
        } catch (Exception $e) {
            throw new Exception('Error al buscar trabajos: ' . $e->getMessage());
        }
    }

    /**
     * Obtener un trabajo por ID
     * 
     * @param string $id ID del trabajo
     * @return array|null
     */
    public function getJobById($id) {
        try {
            return $this->db->fetchOne(
                'SELECT * FROM jobs WHERE id = ?',
                [$id]
            );
        } catch (Exception $e) {
            throw new Exception('Error al obtener el trabajo: ' . $e->getMessage());
        }
    }

    /**
     * Obtener todos los trabajos publicados
     * 
     * @return array
     */
    public function getAllJobs() {
        try {
            return $this->db->fetchAll(
                'SELECT * FROM jobs WHERE published = 1 LIMIT 50'
            );
        } catch (Exception $e) {
            throw new Exception('Error al obtener los trabajos: ' . $e->getMessage());
        }
    }

    /**
     * Crear un nuevo trabajo
     * 
     * @param array $data Datos del trabajo
     * @return array
     */
    public function createJob($data) {
        try {
            // Validar datos requeridos
            $validator = new Validator();
            $validator->required($data['title'] ?? null, 'title');
            $validator->required($data['company'] ?? null, 'company');
            $validator->required($data['location'] ?? null, 'location');
            $validator->required($data['description'] ?? null, 'description');
            
            if ($validator->hasErrors()) {
                throw new Exception('Datos inválidos: ' . json_encode($validator->getErrors()));
            }
            
            // Generar ID único
            $id = Crypto::generateUUID();
            
            // Insertar en la base de datos
            $this->db->insert('jobs', [
                'id' => $id,
                'title' => $data['title'],
                'company' => $data['company'],
                'location' => $data['location'],
                'salary' => $data['salary'] ?? null,
                'description' => $data['description'],
                'published' => $data['published'] ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            return [
                'id' => $id,
                'title' => $data['title'],
                'company' => $data['company'],
                'location' => $data['location'],
                'salary' => $data['salary'] ?? null,
                'description' => $data['description'],
                'published' => $data['published'] ?? 1,
                'createdAt' => date('Y-m-d H:i:s'),
            ];
        } catch (Exception $e) {
            throw new Exception('Error al crear el trabajo: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un trabajo
     * 
     * @param string $id ID del trabajo
     * @return bool
     */
    public function deleteJob($id) {
        try {
            $rowsAffected = $this->db->delete('jobs', 'id = ?', [$id]);
            return $rowsAffected > 0;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar el trabajo: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar un trabajo
     * 
     * @param string $id ID del trabajo
     * @param array $data Datos a actualizar
     * @return bool
     */
    public function updateJob($id, $data) {
        try {
            $rowsAffected = $this->db->update(
                'jobs',
                $data,
                'id = ?',
                [$id]
            );
            return $rowsAffected > 0;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar el trabajo: ' . $e->getMessage());
        }
    }
}
