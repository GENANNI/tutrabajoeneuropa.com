# Backend PHP - Tu Trabajo en Europa

Este documento describe la migración del backend de Node.js a PHP para la aplicación "Tu Trabajo en Europa".

## Estructura del Proyecto

```
backend/
├── config/
│   ├── config.php          # Configuración principal y constantes
│   └── Database.php        # Clase para gestión de conexiones MySQL
├── middleware/
│   └── CorsMiddleware.php  # Middleware para manejo de CORS
├── routes/
│   ├── admin-routes.php    # Rutas de administración
│   ├── cv-routes.php       # Rutas de gestión de CVs
│   └── job-routes.php      # Rutas de gestión de trabajos
├── services/
│   ├── CVService.php       # Servicio de CVs
│   ├── JobService.php      # Servicio de trabajos
│   └── UserService.php     # Servicio de usuarios
├── utils/
│   ├── Crypto.php          # Utilidades de cifrado/descifrado
│   ├── Response.php        # Manejo de respuestas JSON
│   └── Validator.php       # Validación de entrada
├── index.php               # Archivo principal
└── .htaccess               # Configuración de Apache
```

## Requisitos

- **PHP:** 7.4 o superior
- **MySQL:** 5.7 o superior
- **Extensiones PHP:**
  - `pdo_mysql` - Driver PDO para MySQL
  - `openssl` - Para cifrado AES-256-CBC
  - `json` - Para manejo de JSON

## Configuración

### 1. Variables de Entorno

Crear un archivo `.env` en la raíz del proyecto backend con la siguiente estructura:

```env
# Configuración de Base de Datos
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=tutrabajoeneuropa
DB_PORT=3306

# Configuración de Cifrado
CRYPTO_KEY=your-hex-encoded-key-here

# Configuración de Aplicación
APP_ENV=production
APP_DEBUG=false

# URL del Frontend
FRONTEND_URL=https://yourdomain.com
```

### 2. Clave de Cifrado

La clave de cifrado debe ser una cadena hexadecimal de 64 caracteres (32 bytes) para AES-256. Puedes generar una clave segura con:

```php
echo bin2hex(openssl_random_pseudo_bytes(32));
```

### 3. Base de Datos

Crear la base de datos y las tablas necesarias:

```sql
CREATE DATABASE tutrabajoeneuropa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE tutrabajoeneuropa;

CREATE TABLE users (
    id VARCHAR(255) PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cvs (
    id VARCHAR(255) PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE jobs (
    id VARCHAR(255) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    salary VARCHAR(255),
    description LONGTEXT NOT NULL,
    published BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Rutas de API

### CVs

- **GET** `/api/cv/list` - Listar todos los CVs
- **GET** `/api/cv/:id` - Obtener un CV específico
- **POST** `/api/cv/upload` - Subir un nuevo CV
- **DELETE** `/api/cv/:id` - Eliminar un CV

### Trabajos

- **GET** `/api/jobs` - Listar todos los trabajos
- **GET** `/api/jobs/:id` - Obtener un trabajo específico
- **GET** `/api/jobs/search?q=query` - Buscar trabajos
- **POST** `/api/jobs` - Crear un nuevo trabajo
- **DELETE** `/api/jobs/:id` - Eliminar un trabajo

### Administración

- **GET** `/api/admin/stats` - Obtener estadísticas
- **GET** `/api/admin/users` - Listar usuarios
- **DELETE** `/api/admin/users/:id` - Eliminar un usuario

## Características Principales

### 1. Cifrado AES-256-CBC

El contenido de los CVs se cifra automáticamente usando AES-256-CBC. La clase `Crypto.php` proporciona métodos para cifrar y descifrar:

```php
$encrypted = Crypto::encrypt($content);
$decrypted = Crypto::decrypt($encrypted);
```

### 2. Validación de Entrada

La clase `Validator.php` proporciona métodos para validar entrada:

```php
$validator = new Validator();
$validator->required($email, 'email');
$validator->email($email, 'email');

if ($validator->hasErrors()) {
    Response::validationError($validator->getErrors());
}
```

### 3. Respuestas JSON Consistentes

La clase `Response.php` proporciona métodos para enviar respuestas JSON consistentes:

```php
Response::success($data);           // Respuesta exitosa
Response::error($message, 400);     // Error genérico
Response::notFound($message);       // 404
Response::unauthorized($message);   // 401
Response::internalError($message);  // 500
```

### 4. CORS

El middleware `CorsMiddleware.php` maneja automáticamente las solicitudes CORS. Los orígenes permitidos se configuran en `config.php`.

## Despliegue en SiteGround

### 1. Preparación

1. Crear una base de datos MySQL en cPanel
2. Subir los archivos del backend a la carpeta `public_html/api/`
3. Crear un archivo `.env` con las credenciales de la base de datos

### 2. Configuración de Apache

Asegurarse de que `mod_rewrite` está habilitado. Esto se puede verificar en cPanel bajo "Apache Modules".

### 3. Permisos de Archivos

Establecer los permisos correctos:

```bash
chmod 755 backend/
chmod 644 backend/*.php
chmod 644 backend/.htaccess
chmod 755 backend/config/
chmod 755 backend/routes/
chmod 755 backend/services/
chmod 755 backend/utils/
chmod 755 backend/middleware/
```

### 4. Verificar Instalación

Acceder a `https://yourdomain.com/api/health` para verificar que el backend está funcionando.

## Migración desde Node.js

### Mapeo de Rutas

| Node.js (tRPC) | PHP (REST) |
|---|---|
| `trpc.cv.upload` | `POST /api/cv/upload` |
| `trpc.cv.list` | `GET /api/cv/list` |
| `trpc.cv.delete` | `DELETE /api/cv/:id` |
| `trpc.admin.getStats` | `GET /api/admin/stats` |
| `trpc.admin.getUsers` | `GET /api/admin/users` |
| `trpc.admin.deleteUser` | `DELETE /api/admin/users/:id` |

### Cambios en el Frontend

El frontend React necesita actualizar las llamadas a la API para usar REST en lugar de tRPC:

```javascript
// Antes (tRPC)
const { data } = trpc.cv.list.useQuery();

// Después (REST)
const { data } = useQuery({
    queryKey: ['cvs'],
    queryFn: () => fetch('/api/cv/list').then(r => r.json())
});
```

## Solución de Problemas

### Error: "Error de conexión a la base de datos"

- Verificar las credenciales en `.env`
- Verificar que la base de datos existe
- Verificar que el usuario de MySQL tiene permisos

### Error: "Error al cifrar el texto"

- Verificar que la extensión `openssl` está habilitada
- Verificar que la clave de cifrado es válida

### Error 404 en rutas

- Verificar que `mod_rewrite` está habilitado
- Verificar que el archivo `.htaccess` existe en el directorio correcto
- Verificar los permisos del archivo `.htaccess`

## Seguridad

- Cambiar la clave de cifrado en producción
- Usar HTTPS en todas las conexiones
- Validar y sanitizar todas las entradas
- Usar consultas preparadas (PDO)
- Implementar autenticación y autorización

## Soporte

Para más información sobre la migración o problemas técnicos, consultar la documentación original del proyecto o contactar al equipo de desarrollo.
