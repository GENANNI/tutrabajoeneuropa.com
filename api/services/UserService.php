<?php
/**
 * Servicio para gestión de usuarios
 */

class UserService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener un usuario por ID
     * 
     * @param string $id ID del usuario
     * @return array|null
     */
    public function getUserById($id) {
        try {
            return $this->db->fetchOne(
                'SELECT id, email, name, created_at FROM users WHERE id = ?',
                [$id]
            );
        } catch (Exception $e) {
            throw new Exception('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Obtener un usuario por email
     * 
     * @param string $email Email del usuario
     * @return array|null
     */
    public function getUserByEmail($email) {
        try {
            return $this->db->fetchOne(
                'SELECT id, email, name, created_at FROM users WHERE email = ?',
                [$email]
            );
        } catch (Exception $e) {
            throw new Exception('Error al obtener el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Crear un nuevo usuario
     * 
     * @param array $data Datos del usuario
     * @return array
     */
    public function createUser($data) {
        try {
            // Validar datos requeridos
            $validator = new Validator();
            $validator->required($data['email'] ?? null, 'email');
            $validator->email($data['email'] ?? null, 'email');
            $validator->required($data['name'] ?? null, 'name');
            
            if ($validator->hasErrors()) {
                throw new Exception('Datos inválidos: ' . json_encode($validator->getErrors()));
            }
            
            // Verificar si el usuario ya existe
            $existingUser = $this->getUserByEmail($data['email']);
            if ($existingUser) {
                throw new Exception('El usuario ya existe');
            }
            
            // Generar ID único
            $id = Crypto::generateUUID();
            
            // Insertar en la base de datos
            $this->db->insert('users', [
                'id' => $id,
                'email' => $data['email'],
                'name' => $data['name'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            
            return [
                'id' => $id,
                'email' => $data['email'],
                'name' => $data['name'],
                'createdAt' => date('Y-m-d H:i:s'),
            ];
        } catch (Exception $e) {
            throw new Exception('Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Listar todos los usuarios
     * 
     * @return array
     */
    public function listUsers() {
        try {
            return $this->db->fetchAll(
                'SELECT id, email, name, created_at FROM users ORDER BY created_at DESC'
            );
        } catch (Exception $e) {
            throw new Exception('Error al listar los usuarios: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un usuario
     * 
     * @param string $id ID del usuario
     * @return bool
     */
    public function deleteUser($id) {
        try {
            // Eliminar CVs del usuario
            $this->db->delete('cvs', 'user_id = ?', [$id]);
            
            // Eliminar usuario
            $rowsAffected = $this->db->delete('users', 'id = ?', [$id]);
            return $rowsAffected > 0;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas
     * 
     * @return array
     */
    public function getStats() {
        try {
            $totalUsers = $this->db->fetchOne('SELECT COUNT(*) as count FROM users');
            $totalCVs = $this->db->fetchOne('SELECT COUNT(*) as count FROM cvs');
            $totalJobs = $this->db->fetchOne('SELECT COUNT(*) as count FROM jobs');
            
            return [
                'totalUsers' => $totalUsers['count'] ?? 0,
                'totalCVs' => $totalCVs['count'] ?? 0,
                'totalJobs' => $totalJobs['count'] ?? 0,
            ];
        } catch (Exception $e) {
            throw new Exception('Error al obtener estadísticas: ' . $e->getMessage());
        }
    }
}
