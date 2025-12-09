<?php
/**
 * Servicio para gestión de CVs
 * Replica la funcionalidad de cv-service.ts
 */

class CVService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Subir un CV
     * 
     * @param string $userId ID del usuario
     * @param string $filename Nombre del archivo
     * @param string $content Contenido del CV
     * @return array
     */
    public function uploadCV($userId, $filename, $content) {
        try {
            // Cifrar el contenido del CV
            $encryptedContent = Crypto::encrypt($content);
            
            // Generar ID único
            $id = Crypto::generateUUID();
            
            // Insertar en la base de datos
            $this->db->insert('cvs', [
                'id' => $id,
                'user_id' => $userId,
                'filename' => $filename,
                'content' => $encryptedContent,
                'uploaded_at' => date('Y-m-d H:i:s'),
            ]);
            
            return [
                'id' => $id,
                'filename' => $filename,
                'uploadedAt' => date('Y-m-d H:i:s'),
            ];
        } catch (Exception $e) {
            throw new Exception('Error al subir el CV: ' . $e->getMessage());
        }
    }

    /**
     * Obtener un CV por ID
     * 
     * @param string $id ID del CV
     * @return array|null
     */
    public function getCV($id) {
        try {
            $cv = $this->db->fetchOne(
                'SELECT * FROM cvs WHERE id = ?',
                [$id]
            );
            
            if (!$cv) {
                return null;
            }
            
            // Descifrar el contenido
            $cv['content'] = Crypto::decrypt($cv['content']);
            
            return $cv;
        } catch (Exception $e) {
            throw new Exception('Error al obtener el CV: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un CV
     * 
     * @param string $id ID del CV
     * @return bool
     */
    public function deleteCV($id) {
        try {
            $rowsAffected = $this->db->delete('cvs', 'id = ?', [$id]);
            return $rowsAffected > 0;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar el CV: ' . $e->getMessage());
        }
    }

    /**
     * Obtener todos los CVs de un usuario
     * 
     * @param string $userId ID del usuario
     * @return array
     */
    public function getUserCVs($userId) {
        try {
            $cvs = $this->db->fetchAll(
                'SELECT * FROM cvs WHERE user_id = ? ORDER BY uploaded_at DESC',
                [$userId]
            );
            
            // Descifrar el contenido de cada CV
            foreach ($cvs as &$cv) {
                $cv['content'] = Crypto::decrypt($cv['content']);
            }
            
            return $cvs;
        } catch (Exception $e) {
            throw new Exception('Error al obtener los CVs del usuario: ' . $e->getMessage());
        }
    }

    /**
     * Listar todos los CVs
     * 
     * @return array
     */
    public function listCVs() {
        try {
            return $this->db->fetchAll(
                'SELECT id, user_id, filename, uploaded_at FROM cvs ORDER BY uploaded_at DESC'
            );
        } catch (Exception $e) {
            throw new Exception('Error al listar los CVs: ' . $e->getMessage());
        }
    }
}
