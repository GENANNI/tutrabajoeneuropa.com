<?php
/**
 * Clase Validator para validación de entrada
 * Reemplaza la funcionalidad de Zod de TypeScript
 */

class Validator {
    private $errors = [];

    /**
     * Validar que un campo sea una cadena
     * 
     * @param mixed $value Valor a validar
     * @param string $field Nombre del campo
     * @param int $minLength Longitud mínima
     * @param int $maxLength Longitud máxima
     * @return bool
     */
    public function string($value, $field, $minLength = 0, $maxLength = 255) {
        if (!is_string($value)) {
            $this->addError($field, 'Debe ser una cadena de texto');
            return false;
        }
        
        $length = strlen($value);
        
        if ($minLength > 0 && $length < $minLength) {
            $this->addError($field, "Debe tener al menos $minLength caracteres");
            return false;
        }
        
        if ($maxLength > 0 && $length > $maxLength) {
            $this->addError($field, "No debe exceder $maxLength caracteres");
            return false;
        }
        
        return true;
    }

    /**
     * Validar que un campo sea requerido
     * 
     * @param mixed $value Valor a validar
     * @param string $field Nombre del campo
     * @return bool
     */
    public function required($value, $field) {
        if (empty($value) && $value !== '0' && $value !== 0 && $value !== false) {
            $this->addError($field, 'Este campo es requerido');
            return false;
        }
        return true;
    }

    /**
     * Validar que un campo sea un email
     * 
     * @param string $value Valor a validar
     * @param string $field Nombre del campo
     * @return bool
     */
    public function email($value, $field) {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'Debe ser un email válido');
            return false;
        }
        return true;
    }

    /**
     * Validar que un campo sea un número
     * 
     * @param mixed $value Valor a validar
     * @param string $field Nombre del campo
     * @return bool
     */
    public function number($value, $field) {
        if (!is_numeric($value)) {
            $this->addError($field, 'Debe ser un número');
            return false;
        }
        return true;
    }

    /**
     * Validar que un campo sea un entero
     * 
     * @param mixed $value Valor a validar
     * @param string $field Nombre del campo
     * @return bool
     */
    public function integer($value, $field) {
        if (!is_int($value) && (!is_string($value) || !ctype_digit($value))) {
            $this->addError($field, 'Debe ser un número entero');
            return false;
        }
        return true;
    }

    /**
     * Validar que un campo sea un booleano
     * 
     * @param mixed $value Valor a validar
     * @param string $field Nombre del campo
     * @return bool
     */
    public function boolean($value, $field) {
        if (!is_bool($value)) {
            $this->addError($field, 'Debe ser un booleano');
            return false;
        }
        return true;
    }

    /**
     * Validar que un campo esté en una lista de valores permitidos
     * 
     * @param mixed $value Valor a validar
     * @param array $allowed Valores permitidos
     * @param string $field Nombre del campo
     * @return bool
     */
    public function enum($value, $allowed, $field) {
        if (!in_array($value, $allowed, true)) {
            $this->addError($field, 'Valor no permitido');
            return false;
        }
        return true;
    }

    /**
     * Validar que un campo sea un UUID
     * 
     * @param string $value Valor a validar
     * @param string $field Nombre del campo
     * @return bool
     */
    public function uuid($value, $field) {
        $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
        if (!preg_match($pattern, $value)) {
            $this->addError($field, 'Debe ser un UUID válido');
            return false;
        }
        return true;
    }

    /**
     * Sanitizar una cadena de texto
     * 
     * @param string $value Valor a sanitizar
     * @return string
     */
    public function sanitizeString($value) {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Agregar un error
     * 
     * @param string $field Nombre del campo
     * @param string $message Mensaje de error
     */
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    /**
     * Obtener los errores
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Verificar si hay errores
     * 
     * @return bool
     */
    public function hasErrors() {
        return !empty($this->errors);
    }

    /**
     * Limpiar los errores
     */
    public function clearErrors() {
        $this->errors = [];
    }
}
