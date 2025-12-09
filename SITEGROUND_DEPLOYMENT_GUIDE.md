# Guía de Despliegue en SiteGround Shared Hosting

## Introducción

Esta guía proporciona instrucciones paso a paso para desplegar la aplicación "Tu Trabajo en Europa" en un servidor compartido de SiteGround. La aplicación consta de un backend en PHP y un frontend en React, ambos funcionando en el mismo servidor.

## Requisitos Previos

Antes de comenzar, asegúrate de tener lo siguiente:

- Una cuenta activa en SiteGround
- Acceso a cPanel
- Un dominio registrado y apuntado a SiteGround
- Conocimientos básicos de FTP/SFTP y cPanel
- PHP 7.4 o superior disponible en tu hosting
- MySQL 5.7 o superior disponible en tu hosting

## Paso 1: Preparación de la Base de Datos

### 1.1 Crear la Base de Datos

1. Inicia sesión en cPanel
2. Busca y haz clic en "MySQL Databases" (o "Bases de Datos MySQL")
3. En la sección "Create New Database", ingresa el nombre de la base de datos (ejemplo: `tutrabajoeneuropa`)
4. Haz clic en "Create Database"

### 1.2 Crear Usuario de Base de Datos

1. En la sección "MySQL Users", ingresa un nombre de usuario (ejemplo: `trabajo_user`)
2. Ingresa una contraseña segura
3. Haz clic en "Create User"

### 1.3 Asignar Permisos

1. En la sección "Add User to Database", selecciona el usuario creado
2. Selecciona la base de datos creada
3. Haz clic en "Add"
4. En la siguiente pantalla, marca todas las casillas de permisos
5. Haz clic en "Make Changes"

### 1.4 Crear Tablas

1. Haz clic en "phpMyAdmin" en cPanel
2. Selecciona la base de datos creada
3. Abre la pestaña "SQL"
4. Copia y pega el siguiente código SQL:

```sql
CREATE TABLE users (
    id VARCHAR(255) PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cvs (
    id VARCHAR(255) PRIMARY KEY,
    user_id VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jobs (
    id VARCHAR(255) PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    company VARCHAR(255) NOT NULL,
    location VARCHAR(255) NOT NULL,
    salary VARCHAR(255),
    description LONGTEXT NOT NULL,
    published BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_published (published),
    FULLTEXT INDEX ft_title (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

5. Haz clic en "Go" para ejecutar el SQL

## Paso 2: Subir Archivos del Backend

### 2.1 Preparar Archivos

1. En tu computadora, crea una carpeta llamada `api`
2. Copia todos los archivos de la carpeta `backend/` del proyecto a esta carpeta `api`

### 2.2 Subir mediante FTP/SFTP

1. Abre tu cliente FTP favorito (FileZilla, WinSCP, etc.)
2. Conéctate a tu servidor SiteGround usando las credenciales de FTP
3. Navega a la carpeta `public_html`
4. Crea una carpeta llamada `api` si no existe
5. Sube todos los archivos de la carpeta `api` local a `public_html/api`

### 2.3 Configurar Permisos

1. En cPanel, ve a "File Manager"
2. Navega a `public_html/api`
3. Selecciona la carpeta `api` y haz clic derecho → "Change Permissions"
4. Establece los permisos a `755`
5. Marca la opción "Recursive" para aplicar a todas las subcarpetas
6. Haz clic en "Change Permissions"

## Paso 3: Configurar Variables de Entorno

### 3.1 Crear Archivo .env

1. En File Manager, navega a `public_html/api`
2. Crea un nuevo archivo llamado `.env`
3. Abre el archivo y agrega el siguiente contenido:

```env
# Configuración de Base de Datos
DB_HOST=localhost
DB_USER=trabajo_user
DB_PASS=tu_contraseña_segura
DB_NAME=tutrabajoeneuropa
DB_PORT=3306

# Configuración de Cifrado
CRYPTO_KEY=tu_clave_hexadecimal_de_64_caracteres

# Configuración de Aplicación
APP_ENV=production
APP_DEBUG=false

# URL del Frontend
FRONTEND_URL=https://tudominio.com
```

**Importante:** Reemplaza los valores con tus datos reales:
- `DB_USER`: El usuario de MySQL que creaste
- `DB_PASS`: La contraseña del usuario de MySQL
- `DB_NAME`: El nombre de la base de datos que creaste
- `CRYPTO_KEY`: Genera una clave segura (ver sección 3.2)
- `FRONTEND_URL`: Tu dominio con HTTPS

### 3.2 Generar Clave de Cifrado

Para generar una clave de cifrado segura:

1. Crea un archivo PHP temporal con el siguiente contenido:

```php
<?php
echo bin2hex(openssl_random_pseudo_bytes(32));
?>
```

2. Sube este archivo a tu servidor
3. Accede a él desde tu navegador (ejemplo: `https://tudominio.com/generar_clave.php`)
4. Copia la clave generada
5. Pégala en el archivo `.env` como valor de `CRYPTO_KEY`
6. Elimina el archivo temporal

## Paso 4: Compilar y Subir Frontend React

### 4.1 Compilar el Frontend

En tu computadora, en la carpeta del proyecto:

```bash
cd client
npm install
npm run build
```

Esto generará una carpeta `dist` con los archivos compilados.

### 4.2 Subir Archivos Compilados

1. En File Manager, navega a `public_html`
2. Sube todos los archivos de la carpeta `client/dist` a `public_html`
3. Asegúrate de que el archivo `index.html` esté en la raíz de `public_html`

## Paso 5: Configurar Apache

### 5.1 Verificar mod_rewrite

1. En cPanel, ve a "Apache Modules"
2. Busca `mod_rewrite` en la lista
3. Si está marcado como "Enabled", puedes continuar
4. Si no está habilitado, contacta al soporte de SiteGround

### 5.2 Crear Archivo .htaccess Principal

En `public_html`, crea un archivo `.htaccess` con el siguiente contenido:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Permitir acceso a archivos y directorios reales
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Reescribir solicitudes a /api a backend/index.php
    RewriteCond %{REQUEST_URI} ^/api
    RewriteRule ^api/(.*)$ /api/index.php [L,QSA]
    
    # Reescribir todas las demás solicitudes a index.html para React Router
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /index.html [L,QSA]
</IfModule>

# Configuración de seguridad
<Files "*.php">
    Deny from all
</Files>

<Files "index.php">
    Allow from all
</Files>

# Permitir acceso a archivos estáticos
<FilesMatch "\.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$">
    Allow from all
</FilesMatch>

# Deshabilitar listado de directorios
Options -Indexes
```

## Paso 6: Verificar Instalación

### 6.1 Health Check del Backend

Abre tu navegador y accede a:

```
https://tudominio.com/api/health
```

Deberías ver una respuesta JSON:

```json
{
  "success": true,
  "data": {
    "status": "ok"
  }
}
```

### 6.2 Acceder a la Aplicación

Abre tu navegador y accede a:

```
https://tudominio.com
```

Deberías ver la página de inicio de la aplicación.

## Paso 7: Configuración de HTTPS

SiteGround proporciona certificados SSL gratuitos. Para asegurar que tu sitio usa HTTPS:

1. En cPanel, ve a "SSL/TLS Status"
2. Si tu dominio aparece con un certificado, ya está configurado
3. Si no, haz clic en "Install" para instalar un certificado Let's Encrypt gratuito

## Solución de Problemas

### Error 404 en rutas de API

**Problema:** Las solicitudes a `/api/...` devuelven 404

**Soluciones:**
- Verifica que `mod_rewrite` esté habilitado
- Verifica que el archivo `.htaccess` esté en la raíz de `public_html`
- Verifica que el archivo `.htaccess` en `/api` existe y tiene permisos 644
- Contacta al soporte de SiteGround para verificar la configuración de Apache

### Error de conexión a base de datos

**Problema:** "Error de conexión a la base de datos"

**Soluciones:**
- Verifica que los datos en `.env` son correctos
- Verifica que el usuario de MySQL tiene permisos en la base de datos
- Verifica que la base de datos existe
- En phpMyAdmin, intenta conectarte con las mismas credenciales

### Error de cifrado

**Problema:** "Error al cifrar el texto"

**Soluciones:**
- Verifica que la extensión `openssl` está habilitada
- En cPanel, ve a "Select PHP Version" y asegúrate de que `openssl` está marcado
- Verifica que la clave de cifrado es válida (64 caracteres hexadecimales)

### React Router no funciona

**Problema:** Las rutas de React devuelven 404

**Soluciones:**
- Verifica que el archivo `.htaccess` en `public_html` contiene la regla para React Router
- Verifica que `mod_rewrite` está habilitado
- Verifica que el archivo `index.html` está en la raíz de `public_html`

## Mantenimiento

### Actualizar el Backend

1. Descarga los archivos actualizados del backend
2. Sube los archivos a `public_html/api` usando FTP
3. No elimines el archivo `.env`
4. Si hay cambios en la base de datos, ejecuta el SQL en phpMyAdmin

### Actualizar el Frontend

1. Compila el frontend: `npm run build`
2. Sube los archivos de `client/dist` a `public_html`
3. Reemplaza los archivos existentes

### Respaldar la Base de Datos

1. En cPanel, ve a "Backup"
2. Haz clic en "Backup Now" para crear un respaldo
3. O usa phpMyAdmin para exportar la base de datos

## Seguridad

Para mantener tu aplicación segura:

1. **Cambiar la clave de cifrado:** Genera una nueva clave y actualiza `.env`
2. **Usar HTTPS:** Asegúrate de que tu sitio usa HTTPS
3. **Validar entrada:** El backend valida todas las entradas
4. **Usar consultas preparadas:** El backend usa PDO con consultas preparadas
5. **Limitar acceso:** Solo expone los endpoints necesarios
6. **Monitorear logs:** Revisa regularmente los logs de error en cPanel

## Contacto y Soporte

Si encuentras problemas durante el despliegue:

1. Consulta la sección "Solución de Problemas" arriba
2. Revisa los logs de error en cPanel → "Error log"
3. Contacta al soporte de SiteGround si el problema está relacionado con el servidor
4. Contacta al equipo de desarrollo si el problema está en la aplicación

## Referencias

- [Documentación de SiteGround](https://www.siteground.com/tutorials/)
- [Documentación de PHP](https://www.php.net/manual/)
- [Documentación de MySQL](https://dev.mysql.com/doc/)
- [Documentación de Apache](https://httpd.apache.org/docs/)
