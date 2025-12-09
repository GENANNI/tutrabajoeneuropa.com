<?php
/**
 * Clase Crypto para cifrado y descifrado AES-256-CBC
 * Replica la funcionalidad del módulo crypto.ts de Node.js
 */

class Crypto {
    const CIPHER_METHOD = 'aes-256-cbc';
    const IV_LENGTH = 16;

    /**
     * Cifrar texto usando AES-256-CBC
     * 
     * @param string $text Texto a cifrar
     * @return string Texto cifrado en formato: iv:encrypted
     */
    public static function encrypt($text) {
        // Obtener la clave de cifrado
        $key = self::getKey();
        
        // Generar IV aleatorio
        $iv = openssl_random_pseudo_bytes(self::IV_LENGTH);
        
        // Cifrar el texto
        $encrypted = openssl_encrypt(
            $text,
            self::CIPHER_METHOD,
            $key,
            OPENSSL_RAW_DATA
        );
        
        if ($encrypted === false) {
            throw new Exception('Error al cifrar el texto');
        }
        
        // Retornar IV:encrypted en formato hexadecimal
        return bin2hex($iv) . ':' . bin2hex($encrypted);
    }

    /**
     * Descifrar texto usando AES-256-CBC
     * 
     * @param string $encryptedText Texto cifrado en formato: iv:encrypted
     * @return string Texto descifrado
     */
    public static function decrypt($encryptedText) {
        // Obtener la clave de cifrado
        $key = self::getKey();
        
        // Separar IV y texto cifrado
        $parts = explode(':', $encryptedText);
        
        if (count($parts) !== 2) {
            throw new Exception('Formato de texto cifrado inválido');
        }
        
        $iv = hex2bin($parts[0]);
        $encrypted = hex2bin($parts[1]);
        
        if ($iv === false || $encrypted === false) {
            throw new Exception('Error al decodificar el texto cifrado');
        }
        
        // Descifrar el texto
        $decrypted = openssl_decrypt(
            $encrypted,
            self::CIPHER_METHOD,
            $key,
            OPENSSL_RAW_DATA
        );
        
        if ($decrypted === false) {
            throw new Exception('Error al descifrar el texto');
        }
        
        return $decrypted;
    }

    /**
     * Obtener la clave de cifrado
     * Convierte la clave hexadecimal a binaria si es necesario
     * 
     * @return string Clave en formato binario
     */
    private static function getKey() {
        $cryptoKey = CRYPTO_KEY;
        
        // Si la clave está en formato hexadecimal, convertirla a binaria
        if (ctype_xdigit($cryptoKey) && strlen($cryptoKey) === 64) {
            return hex2bin($cryptoKey);
        }
        
        // Si la clave no es de 32 bytes, usar hash SHA-256
        if (strlen($cryptoKey) !== 32) {
            return hash('sha256', $cryptoKey, true);
        }
        
        return $cryptoKey;
    }

    /**
     * Generar una clave segura para cifrado
     * 
     * @return string Clave en formato hexadecimal
     */
    public static function generateKey() {
        $key = openssl_random_pseudo_bytes(32);
        return bin2hex($key);
    }

    /**
     * Generar un UUID v4
     * 
     * @return string UUID v4
     */
    public static function generateUUID() {
        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
