# Resumen de Migración: Node.js → PHP

## Descripción General

Este documento proporciona un resumen ejecutivo de la migración del backend de la aplicación "Tu Trabajo en Europa" de Node.js (Express/tRPC) a PHP, manteniendo total compatibilidad con el frontend React existente.

## Cambios Principales

### Backend

| Aspecto | Antes (Node.js) | Después (PHP) |
|--------|---|---|
| Framework | Express.js | PHP Nativo |
| ORM | Drizzle ORM | PDO |
| Validación | Zod | Validador Personalizado |
| Enrutamiento | tRPC | REST |
| Cifrado | Node.js Crypto | OpenSSL |
| Servidor | Node.js Runtime | Apache + PHP |

### Frontend

El frontend React **no requiere cambios significativos**. Solo se actualizó la capa de cliente para consumir endpoints REST en lugar de tRPC:

- **Cliente tRPC:** Reemplazado con cliente REST personalizado (`lib/api.ts`)
- **Páginas:** Actualizadas para consumir nuevas rutas REST
- **Componentes:** Sin cambios en la lógica de negocio
- **Estilos:** Sin cambios

## Mapeo de Rutas

### CVs

| Operación | tRPC (Node.js) | REST (PHP) |
|-----------|---|---|
| Subir CV | `trpc.cv.upload` | `POST /api/cv/upload` |
| Listar CVs | `trpc.cv.list` | `GET /api/cv/list` |
| Obtener CV | N/A | `GET /api/cv/:id` |
| Eliminar CV | `trpc.cv.delete` | `DELETE /api/cv/:id` |

### Trabajos

| Operación | tRPC (Node.js) | REST (PHP) |
|-----------|---|---|
| Listar trabajos | N/A | `GET /api/jobs` |
| Obtener trabajo | N/A | `GET /api/jobs/:id` |
| Buscar trabajos | N/A | `GET /api/jobs/search?q=query` |
| Crear trabajo | N/A | `POST /api/jobs` |
| Eliminar trabajo | N/A | `DELETE /api/jobs/:id` |

### Administración

| Operación | tRPC (Node.js) | REST (PHP) |
|-----------|---|---|
| Estadísticas | `trpc.admin.getStats` | `GET /api/admin/stats` |
| Listar usuarios | `trpc.admin.getUsers` | `GET /api/admin/users` |
| Eliminar usuario | `trpc.admin.deleteUser` | `DELETE /api/admin/users/:id` |

## Estructura del Proyecto

```
tutrabajoeneuropa_php/
├── backend/                    # Nuevo backend en PHP
│   ├── config/
│   │   ├── config.php         # Configuración principal
│   │   └── Database.php       # Clase de base de datos
│   ├── middleware/
│   │   └── CorsMiddleware.php # Manejo de CORS
│   ├── routes/
│   │   ├── admin-routes.php   # Rutas de administración
│   │   ├── cv-routes.php      # Rutas de CVs
│   │   └── job-routes.php     # Rutas de trabajos
│   ├── services/
│   │   ├── CVService.php      # Lógica de CVs
│   │   ├── JobService.php     # Lógica de trabajos
│   │   └── UserService.php    # Lógica de usuarios
│   ├── utils/
│   │   ├── Crypto.php         # Cifrado/descifrado
│   │   ├── Response.php       # Respuestas JSON
│   │   └── Validator.php      # Validación
│   ├── index.php              # Punto de entrada
│   └── .htaccess              # Configuración Apache
├── client/                     # Frontend React (sin cambios significativos)
│   ├── src/
│   │   ├── lib/
│   │   │   ├── api.ts         # Cliente REST (NUEVO)
│   │   │   └── trpc.ts        # Cliente tRPC (DEPRECADO)
│   │   ├── pages/
│   │   │   ├── Home.tsx       # Actualizado
│   │   │   ├── CVUpload.tsx   # Actualizado
│   │   │   ├── JobSearch.tsx  # Actualizado
│   │   │   └── AdminDashboard.tsx # Actualizado
│   │   └── App.tsx            # Actualizado con nuevas rutas
│   └── dist/                  # Compilado para producción
├── BACKEND_PHP_README.md      # Documentación del backend
├── SITEGROUND_DEPLOYMENT_GUIDE.md # Guía de despliegue
└── MIGRATION_SUMMARY.md       # Este archivo
```

## Características Preservadas

### Cifrado de CVs

El contenido de los CVs se cifra usando **AES-256-CBC**, idéntico al original:

```php
// Cifrar
$encrypted = Crypto::encrypt($content);

// Descifrar
$decrypted = Crypto::decrypt($encrypted);
```

### Validación de Entrada

Todas las entradas se validan antes de procesarse:

```php
$validator = new Validator();
$validator->required($email, 'email');
$validator->email($email, 'email');
```

### Respuestas JSON Consistentes

Todas las respuestas siguen el mismo formato:

```json
{
  "success": true,
  "data": { /* datos */ }
}
```

### CORS Habilitado

Las solicitudes desde el frontend se procesan correctamente con CORS.

## Ventajas de la Migración

1. **Compatibilidad con Hosting Compartido:** PHP es más ampliamente soportado que Node.js
2. **Menores Costos:** Hosting compartido es más económico que servidores Node.js
3. **Mantenimiento Simplificado:** PHP no requiere gestor de procesos como PM2
4. **Seguridad:** Cifrado AES-256-CBC preservado
5. **Escalabilidad:** Puede escalar horizontalmente en hosting compartido

## Consideraciones de Despliegue

### Requisitos del Servidor

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Extensión `pdo_mysql`
- Extensión `openssl`
- Módulo `mod_rewrite` de Apache

### Variables de Entorno

Crear archivo `.env` en `backend/`:

```env
DB_HOST=localhost
DB_USER=trabajo_user
DB_PASS=contraseña_segura
DB_NAME=tutrabajoeneuropa
CRYPTO_KEY=clave_hexadecimal_64_caracteres
APP_ENV=production
FRONTEND_URL=https://tudominio.com
```

### Pasos de Despliegue

1. Crear base de datos y usuario en cPanel
2. Subir archivos del backend a `public_html/api`
3. Crear archivo `.env` con credenciales
4. Compilar frontend React
5. Subir archivos compilados a `public_html`
6. Configurar `.htaccess` para reescritura de URLs
7. Verificar con health check: `GET /api/health`

## Pruebas Recomendadas

Después del despliegue, verificar:

1. **Health Check:** `GET /api/health` → `{"status": "ok"}`
2. **Listar CVs:** `GET /api/cv/list` → Lista de CVs
3. **Listar Trabajos:** `GET /api/jobs` → Lista de trabajos
4. **Estadísticas:** `GET /api/admin/stats` → Estadísticas
5. **Frontend:** Acceder a `https://tudominio.com` → Página de inicio carga correctamente

## Soporte y Documentación

- **Backend:** Ver `BACKEND_PHP_README.md`
- **Despliegue:** Ver `SITEGROUND_DEPLOYMENT_GUIDE.md`
- **Código:** Comentarios en línea en todos los archivos PHP

## Próximos Pasos

1. Revisar la documentación de despliegue
2. Preparar servidor SiteGround
3. Desplegar backend y frontend
4. Realizar pruebas exhaustivas
5. Monitorear logs de error
6. Configurar copias de seguridad automáticas

## Contacto

Para preguntas o problemas durante la migración, consultar la documentación incluida o contactar al equipo de desarrollo.
