<p align="center"></p>

<p align="center">
   <a href="https://themeselection.com/item/materio-free-bootstrap-html-laravel-admin-template/" target="_blank">
      <img src="https://cdn.themeselection.com/ts-assets/materio/logo/logo.png" alt="materio-logo" width="40px" height="auto">
   </a>
</p>
# Guía Completa de Despliegue - SIGERUTA Laravel 11

## 📋 Tabla de Contenidos

1. [Requisitos Previos](#requisitos-previos)
2. [Infraestructura Base](#infraestructura-base)
3. [Instalación de Dependencias](#instalación-de-dependencias)
4. [Configuración del Proyecto](#configuración-del-proyecto)
5. [Configuración de PHP-FPM](#configuración-de-php-fpm)
6. [Configuración de Nginx](#configuración-de-nginx)
7. [Configuración de IIS (Proxy Reverso)](#configuración-de-iis-proxy-reverso)
8. [Configuración de Laravel](#configuración-de-laravel)
9. [Compilación de Assets](#compilación-de-assets)
10. [Permisos y Seguridad](#permisos-y-seguridad)
11. [Verificación Final](#verificación-final)
12. [Troubleshooting](#troubleshooting)

---

## 🎯 Requisitos Previos

### Hardware/Servidor

- Windows Server 2016+ o Windows 10/11 Pro
- 4GB RAM mínimo (8GB recomendado)
- 20GB espacio en disco
- Conexión a Internet estable

### Software Base

- Windows con IIS 10+
- PHP 8.1 o superior
- Nginx para Windows
- MySQL 8.0+
- Composer 2.x
- Node.js 18+ y Yarn/NPM

---

## 🌐 Infraestructura Base

### 1. Dominio y DNS

#### Comprar Dominio

1. Proveedor recomendado: Namecheap, GoDaddy, Cloudflare
2. Registrar: `sigeruta.tech`

#### Configurar DNS

```dns
Tipo    Nombre    Valor              TTL
A       @         20.121.137.55      1800
CNAME   www       sigeruta.tech      1800
```

### 2. Certificado SSL

#### Certificado Comercial

1. Comprar en: namecheap o tu proveedor de dominio
2. Generar CSR: CERTIFICATE SERVER REQUEST

```powershell
# En IIS Manager:
# Server Certificates > Create Certificate Request
```

2.1 Completar Certificado de Dominio
2.2 Generar Key a partir del .pfx 3. Descargar certificados (`.crt` y `.key`) 4. Colocar en: `C:\etc\ssl\`

- `sigeruta.crt`
- `sigeruta.key`

---

## 🔧 Instalación de Dependencias

### 1. Instalar PHP 8.3.15

#### Descargar PHP

```powershell
# Descargar desde: https://windows.php.net/download/
# Versión: PHP 8.1+ Non-Thread Safe (NTS)

# Extraer a:
C:\php\
```

#### Configurar php.ini

```ini
# C:\php\php.ini

; Extensiones necesarias (descomentar)
extension=curl
extension=fileinfo
extension=gd
extension=intl
extension=mbstring
extension=mysqli
extension=pdo_mysql
extension=openssl
extension=zip

; Configuración
memory_limit = 256M
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300

; Timezone
date.timezone = America/Bogota
```

#### Agregar PHP al PATH

```powershell
# Como Administrador:
[Environment]::SetEnvironmentVariable(
    "Path",
    "$env:Path;C:\php",
    [EnvironmentVariableTarget]::Machine
)
```

#### Verificar

```powershell
php -v
# PHP 8.3.5.x (cli) ...
```

### 2. Instalar Composer

```powershell
# Descargar desde: https://getcomposer.org/download/

# O via PowerShell:
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php --install-dir=C:\php --filename=composer
php -r "unlink('composer-setup.php');"

# Verificar
composer --version
```

### 3. Instalar Node.js y Yarn

```powershell
# Descargar Node.js LTS
# Instalar con el wizard

# Verificar Node.js
node -v
npm -v

# Instalar Yarn globalmente
npm install -g yarn

# Verificar Yarn
yarn --version
```

### 4. Instalar Nginx

```powershell
# Descargar
# Versión Stable para Windows

# Extraer a:
C:\nginx\

# Estructura:
# C:\nginx\
#   ├── conf\
#   ├── html\
#   ├── logs\
#   └── nginx.exe
```

---

## 📦 Configuración del Proyecto

### 1. Clonar/Subir el Proyecto

```powershell
# Ubicación del proyecto
cd C:\nginx\html\

# Si usas Git:
git clone https://github.com/tu-usuario/sigeruta.git

# O sube los archivos manualmente a:
C:\nginx\html\sigeruta\
```

### 2. Instalar Dependencias PHP

```powershell
cd C:\nginx\html\sigeruta

# Instalar dependencias de producción
composer install --optimize-autoloader --no-dev

# Generar APP_KEY
php artisan key:generate
```

### 3. Instalar Dependencias Node.js

```powershell
# En el directorio del proyecto
yarn install

# O con npm:
npm install
```

### 4. Configurar Base de Datos

CREAR BASE DE DATOS
BD= proyecto_iudigital

#### Crear Base de Datos MySQL

```sql
CREATE DATABASE sigeruta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'root'@'localhost' IDENTIFIED BY 'PASSWORD';
GRANT ALL PRIVILEGES ON proyecto_iudigital.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

#### Ejecutar Migraciones

```powershell
php artisan migrate
php artisan db:seed
```

---

## ⚙️ Configuración de PHP-FPM

### 1. Instalar PHP-FPM para Windows

```powershell

# O usar el php-cgi.exe incluido en PHP
# Ya está en: C:\php\php-cgi.exe
```

### 2. Crear Script de Inicio PHP-FPM

**Archivo:** `C:\php\start-php-fcgi.bat`

```batch
@ECHO OFF
SET PHP_FCGI_CHILDREN=4
SET PHP_FCGI_MAX_REQUESTS=1000

cd C:\php
php-cgi.exe -b 127.0.0.1:9000
php-cgi.exe -b 127.0.0.1:9001
php-cgi.exe -b 127.0.0.1:9002
php-cgi.exe -b 127.0.0.1:9003
```

### 3. Ejecutar como Servicio (Opcional)

```powershell
# Instalar NSSM (Non-Sucking Service Manager)
choco install nssm

# Crear servicio
nssm install PHP-FastCGI "C:\php\php-cgi.exe" "-b 127.0.0.1:9000"
nssm install PHP-FastCGI "C:\php\php-cgi.exe" "-b 127.0.0.1:9001"
nssm install PHP-FastCGI "C:\php\php-cgi.exe" "-b 127.0.0.1:9002"
nssm install PHP-FastCGI "C:\php\php-cgi.exe" "-b 127.0.0.1:9003"

# Configurar servicio
nssm set PHP-FastCGI AppEnvironmentExtra PHP_FCGI_MAX_REQUESTS=1000
nssm set PHP-FastCGI AppEnvironmentExtra PHP_FCGI_CHILDREN=4

# Iniciar
nssm start PHP-FastCGI
```

---

## 🌐 Configuración de Nginx

### 1. Configuración Principal

**Archivo:** `C:\nginx\conf\nginx.conf`

```nginx
server {
    # Nueva sintaxis para http2 (Nginx 1.25+)
    listen 9440 ssl;
    http2 on;

    server_name www.sigeruta.tech sigeruta.tech;

    root C:/nginx/html/sigeruta/public;
    index index.php index.html;

    # Logs
    error_log C:/nginx/logs/sigeruta_error.log warn;
    access_log C:/nginx/logs/sigeruta_access.log;

    # Tamaño máximo de carga
    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP handling
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php_upstream;
        fastcgi_index index.php;

        # Parámetros FastCGI básicos
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param QUERY_STRING $query_string;
        fastcgi_param REQUEST_METHOD $request_method;
        fastcgi_param CONTENT_TYPE $content_type;
        fastcgi_param CONTENT_LENGTH $content_length;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_param REQUEST_URI $request_uri;
        fastcgi_param DOCUMENT_URI $document_uri;
        fastcgi_param DOCUMENT_ROOT $document_root;
        fastcgi_param SERVER_PROTOCOL $server_protocol;
        fastcgi_param REQUEST_SCHEME $scheme;
        fastcgi_param GATEWAY_INTERFACE CGI/1.1;
        fastcgi_param SERVER_SOFTWARE nginx/$nginx_version;
        fastcgi_param REMOTE_ADDR $remote_addr;
        fastcgi_param REMOTE_PORT $remote_port;
        fastcgi_param SERVER_ADDR $server_addr;
        fastcgi_param PATH_INFO $fastcgi_path_info;

        # Headers de proxy para Laravel (CRÍTICO)
        fastcgi_param HTTPS on;
        fastcgi_param HTTP_X_FORWARDED_PROTO https;
        fastcgi_param HTTP_X_FORWARDED_HOST $http_host;
        fastcgi_param HTTP_X_FORWARDED_PORT 443;
        fastcgi_param HTTP_X_FORWARDED_FOR $remote_addr;
        fastcgi_param SERVER_NAME www.sigeruta.tech;
        fastcgi_param SERVER_PORT 443;
        fastcgi_param HTTP_HOST $http_host;

        # Buffers
        fastcgi_buffer_size 128k;
        fastcgi_buffers 8 128k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
        fastcgi_read_timeout 300;
    }

    # Denegar acceso a archivos ocultos
    location ~ /\. {
        deny all;
        access_log off;
        log_not_found off;
    }

    # Archivos estáticos
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
        access_log off;
        try_files $uri =404;
    }

    # SSL
    ssl_certificate C:/etc/ssl/sigeruta.crt;
    ssl_certificate_key C:/etc/ssl/sigeruta.key;
    ssl_session_timeout 1d;
    ssl_session_cache shared:SSL:50m;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;

    charset utf-8;
}
```

### 3. Crear Carpetas Necesarias

```powershell
# Crear carpeta sites-enabled si no existe
New-Item -ItemType Directory -Force -Path "C:\nginx\conf\sites-enabled"
```

### 4. Iniciar Nginx

```powershell
# Verificar configuración
cd C:\nginx
nginx -t

# Si todo está OK, iniciar
nginx

# O reiniciar si ya está corriendo
nginx -s reload
```

### 5. Crear Servicio de Nginx (Opcional)

```powershell
# Con NSSM
nssm install Nginx "C:\nginx\nginx.exe"
nssm set Nginx AppDirectory "C:\nginx"
nssm start Nginx
```

---

## 🔄 Configuración de IIS (Proxy Reverso)

### 1. Instalar Módulos Necesarios

#### Instalar URL Rewrite

1. Descargar desde: https://www.iis.net/downloads/microsoft/url-rewrite
2. Ejecutar instalador `rewrite_amd64.msi`

#### Instalar Application Request Routing (ARR)

1. Descargar desde: https://www.iis.net/downloads/microsoft/application-request-routing
2. Ejecutar instalador `ARR_x64.msi`
3. Reiniciar IIS: `iisreset`

### 2. Habilitar Proxy en ARR

```powershell
# Via PowerShell (como Administrador)
Import-Module WebAdministration
Set-WebConfigurationProperty -pspath 'MACHINE/WEBROOT/APPHOST' `
    -filter "system.webServer/proxy" `
    -name "enabled" `
    -value "True"

iisreset
```

O manualmente:

1. Abrir **IIS Manager**
2. Clic en el **servidor** (nivel superior)
3. Doble clic en **"Application Request Routing Cache"**
4. En el panel derecho: **"Server Proxy Settings..."**
5. Marcar: **"Enable proxy"**
6. Aplicar

### 3. Configurar Server Variables Permitidas

```powershell
# Via PowerShell (Recomendado)
$variables = @(
    'HTTP_X_FORWARDED_HOST',
    'HTTP_X_FORWARDED_PROTO',
    'HTTP_X_FORWARDED_PORT',
    'HTTP_X_FORWARDED_FOR'
)

foreach ($var in $variables) {
    try {
        Add-WebConfigurationProperty -pspath 'MACHINE/WEBROOT/APPHOST' `
            -filter "system.webServer/rewrite/allowedServerVariables" `
            -name "." `
            -value @{name=$var}
        Write-Host "✓ $var agregada" -ForegroundColor Green
    }
    catch {
        Write-Host "⚠ $var ya existe" -ForegroundColor Yellow
    }
}

iisreset
```

### 4. Configurar Regla de Proxy

**Archivo:** `C:\inetpub\wwwroot\web.config` (o en tu sitio web)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
            <rules>
                <!-- Proxy SIGERUTA a Nginx interno -->
                <rule name="Proxy_app_sigeruta_to_9440" stopProcessing="true">
                    <match url="(.*)" />
                    <conditions>
                        <add input="{HTTP_HOST}" pattern="^(www\.)?sigeruta\.tech$" />
                    </conditions>
                    <action type="Rewrite" url="https://10.0.0.5:9440/{R:1}" />
                    <serverVariables>
                        <set name="HTTP_X_FORWARDED_HOST" value="{HTTP_HOST}" />
                        <set name="HTTP_X_FORWARDED_PROTO" value="https" />
                        <set name="HTTP_X_FORWARDED_PORT" value="443" />
                        <set name="HTTP_X_FORWARDED_FOR" value="{REMOTE_ADDR}" />
                    </serverVariables>
                </rule>
            </rules>
        </rewrite>
    </system.webServer>
</configuration>
```

### 5. Reiniciar IIS

```powershell
iisreset
```

---

## 🚀 Configuración de Laravel

### 1. Configurar .env

**Archivo:** `.env`

```env
APP_NAME=SIGERUTA
APP_ENV=production
APP_KEY=base64:tu_key_generada_aqui
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://www.sigeruta.tech
ASSET_URL=https://www.sigeruta.tech

# Base de datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sigeruta
DB_USERNAME=sigeruta_user
DB_PASSWORD=tu_password_seguro

# Sesiones seguras
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.sigeruta.tech

# Cache
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Logs
LOG_CHANNEL=daily
LOG_LEVEL=error
```

### 2. Configurar Trusted Proxies

**Archivo:** `bootstrap/app.php`

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;  // ⚠️ IMPORTANTE: Agregar este import

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Configurar Trusted Proxies (IIS + Nginx)
        $middleware->trustProxies(
            at: '*',  // Confía en todos los proxies
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO |
                     Request::HEADER_X_FORWARDED_AWS_ELB
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
```

### 3. Configurar AppServiceProvider

**Archivo:** `app/Providers/AppServiceProvider.php`

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forzar HTTPS en producción
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }
    }
}
```

---

## 🎨 Compilación de Assets

### 1. Configurar Vite

**Archivo:** `vite.config.js`

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import html from '@rollup/plugin-html';
import { glob } from 'glob';

function GetFilesArray(query) {
  return glob.sync(query);
}

// Archivos JS
const pageJsFiles = GetFilesArray('resources/assets/js/*.js');
const vendorJsFiles = GetFilesArray('resources/assets/vendor/js/*.js');
const LibsJsFiles = GetFilesArray('resources/assets/vendor/libs/**/*.js');

// Archivos CSS/SCSS
const AssetsCssFiles = GetFilesArray('resources/assets/css/**/*.css');
const CoreScssFiles = GetFilesArray('resources/assets/vendor/scss/**/!(_)*.scss');
const LibsScssFiles = GetFilesArray('resources/assets/vendor/libs/**/!(_)*.scss');
const LibsCssFiles = GetFilesArray('resources/assets/vendor/libs/**/*.css');
const FontsScssFiles = GetFilesArray('resources/assets/vendor/fonts/**/!(_)*.scss');

export default defineConfig({
  plugins: [
    laravel({
      input: [
        'resources/css/app.css',
        'resources/js/app.js',
        ...AssetsCssFiles,
        ...pageJsFiles,
        ...vendorJsFiles,
        ...LibsJsFiles,
        ...CoreScssFiles,
        ...LibsScssFiles,
        ...LibsCssFiles,
        ...FontsScssFiles
      ],
      refresh: true
    }),
    html()
  ],
  build: {
    manifest: true,
    outDir: 'public/build',
    emptyOutDir: true
  }
});
```

### 2. Compilar Assets para Producción

```powershell
cd C:\nginx\html\sigeruta

# Limpiar build anterior
Remove-Item -Path "public\build\*" -Recurse -Force -ErrorAction SilentlyContinue

# Compilar con Yarn
yarn run build

# O con NPM
npm run build
```

### 3. Verificar Compilación

```powershell
# Verificar que exista el manifest
Test-Path "public\build\manifest.json"

# Ver contenido del manifest
Get-Content "public\build\manifest.json" | ConvertFrom-Json | ConvertTo-Json -Depth 10

# Verificar archivos compilados
Get-ChildItem -Path "public\build\assets" | Select-Object Name, Length
```

---

## 🔐 Permisos y Seguridad

### 1. Permisos de Carpetas

```powershell
# Storage (escritura para Laravel)
icacls "C:\nginx\html\sigeruta\storage" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\nginx\html\sigeruta\storage" /grant "IUSR:(OI)(CI)F" /T

# Bootstrap cache
icacls "C:\nginx\html\sigeruta\bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)F" /T
icacls "C:\nginx\html\sigeruta\bootstrap\cache" /grant "IUSR:(OI)(CI)F" /T

# Logs
New-Item -ItemType Directory -Force -Path "C:\nginx\html\sigeruta\storage\logs"
icacls "C:\nginx\html\sigeruta\storage\logs" /grant "Everyone:(OI)(CI)F" /T
```

### 2. Cachear Configuraciones

```powershell
cd C:\nginx\html\sigeruta

# Limpiar cachés existentes
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cachear para producción (mejora performance)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 3. Proteger Archivos Sensibles

```powershell
# Denegar acceso web a .env
# Ya está protegido por la configuración de Nginx (location ~ /\.)

# Verificar que .env no sea accesible:
# https://www.sigeruta.tech/.env (debe dar 403 o 404)
```

---

## ✅ Verificación Final

### 1. Checklist de Servicios

```powershell
# Verificar PHP-FPM
Test-NetConnection -ComputerName 127.0.0.1 -Port 9000

# Verificar Nginx
Test-NetConnection -ComputerName 10.0.0.5 -Port 9440

# Verificar MySQL
Test-NetConnection -ComputerName 127.0.0.1 -Port 3306

# Ver procesos corriendo
Get-Process nginx, php-cgi, mysqld -ErrorAction SilentlyContinue
```

### 2. Probar Endpoints

#### Ruta de Prueba (Temporal)

**Archivo:** `routes/web.php` (agregar temporalmente)

```php
Route::get('/test-deployment', function() {
    return response()->json([
        'status' => 'OK',
        'APP_URL' => config('app.url'),
        'ASSET_URL' => config('app.asset_url'),
        'request_url' => request()->url(),
        'request_host' => request()->getHost(),
        'request_scheme' => request()->getScheme(),
        'is_secure' => request()->secure(),
        'php_version' => PHP_VERSION,
        'laravel_version' => app()->version(),
        'headers' => [
            'X-Forwarded-Proto' => request()->header('X-Forwarded-Proto'),
            'X-Forwarded-Host' => request()->header('X-Forwarded-Host'),
            'Host' => request()->header('Host'),
        ],
        'db_connection' => DB::connection()->getPdo() ? 'OK' : 'FAIL',
        'storage_writable' => is_writable(storage_path()),
    ], JSON_PRETTY_PRINT);
});
```

Visitar: `https://www.sigeruta.tech/test-deployment`

**Resultado Esperado:**

```json
{
  "status": "OK",
  "APP_URL": "https://www.sigeruta.tech",
  "request_host": "www.sigeruta.tech",
  "request_scheme": "https",
  "is_secure": true,
  "db_connection": "OK",
  "storage_writable": true
}
```

### 3. Probar Login

Visitar: `https://www.sigeruta.tech/auth/login-basic`

Verificar:

- ✅ La página carga sin errores
- ✅ Los estilos CSS se aplican correctamente
- ✅ Los iconos (FontAwesome) se muestran
- ✅ Los assets cargan desde `www.sigeruta.tech` (no desde IP interna)

---

## 🚨 Troubleshooting

### Problema: Error 500 en IIS

**Síntomas:** IIS muestra "Internal Server Error"

**Solución:**

```powershell
# Verificar que ARR esté instalado
Get-WebConfigurationProperty -pspath 'MACHINE/WEBROOT/APPHOST' `
    -filter "system.webServer/proxy" -name "enabled"

# Si retorna error, instalar ARR
# https://www.iis.net/downloads/microsoft/application-request-routing

# Habilitar proxy
Set-WebConfigurationProperty -pspath 'MACHINE/WEBROOT/APPHOST' `
    -filter "system.webServer/proxy" -name "enabled" -value "True"

iisreset
```

### Problema: Assets con IP Local (10.0.0.5:9440)

**Síntomas:** Los CSS/JS cargan desde IP interna en lugar del dominio

**Solución:**

1. Verificar `.env`:

```env
ASSET_URL=https://www.sigeruta.tech
```

2. Verificar `bootstrap/app.php` tenga `use Illuminate\Http\Request;`

3. Limpiar cachés:

```powershell
php artisan config:clear
php artisan config:cache
```

### Problema: Error "Class Request not found"

**Síntomas:** Fatal error en `bootstrap/app.php:25`

**Solución:**
Agregar import en `bootstrap/app.php`:

```php
use Illuminate\Http\Request;  // ⚠️ Agregar esta línea
```

### Problema: Vite Error - "Unable to locate file in manifest"

**Síntomas:** Error de fontaw
