# Qrbita

Qrbita es una aplicación Laravel 13 + React/TypeScript para crear una vCard profesional, publicarla mediante una URL estable, compartirla con un código QR y consultar métricas de interacción.

## Funcionalidades incluidas

- Registro, autenticación, verificación de correo y cuentas activas/inactivas.
- Una vCard por usuario, con formulario y vista previa en vivo.
- Fotografía, portada, logotipo, datos de contacto y hasta ocho canales sociales.
- Publicación y despublicación sin cambiar la URL ni el código QR.
- Descarga de QR en PNG y contacto compatible con vCard 3.0 (`.vcf`).
- Métricas de vistas, escaneos, clics, contactos guardados y dispositivos.
- Asistente opcional mediante OpenAI Responses API con salida estructurada.
- Panel de administración de usuarios.

## Requisitos del sistema

Antes de instalar Qrbita, verifica que el equipo tenga:

- PHP 8.3 o superior con las extensiones `ctype`, `curl`, `dom`, `fileinfo`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_sqlite` o el controlador de base de datos elegido, `tokenizer`, `xml` y `zip`.
- Composer 2.7 o superior.
- Node.js 20 LTS o superior y npm 10 o superior.
- SQLite 3.35 o superior para la configuración local predeterminada, o MySQL 8.0+ / PostgreSQL 15+ para otros entornos.
- Git, recomendado para clonar y actualizar el proyecto.
- Un navegador moderno con soporte para ES2022.

## Dependencias principales

El backend utiliza Laravel Framework 13, Laravel Fortify, Inertia Laravel, Laravel Wayfinder, PHPStan/Larastan y `endroid/qr-code` 6.1. El frontend utiliza React 19, TypeScript 5, Inertia React, Tailwind CSS 4, Vite 8, Lucide React y Sonner. Composer y npm instalan las versiones exactas compatibles a partir de `composer.lock` y `package-lock.json`.

## Instalación local

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run build
```

Configura en `.env` como mínimo `APP_URL`, `APP_TIMEZONE` y la conexión de base de datos. La configuración local usa SQLite; si el archivo no existe, créalo antes de migrar:

```bash
touch database/database.sqlite
```

Para desarrollo, inicia Laravel y Vite con:

```bash
composer dev
```

También puedes ejecutar los procesos por separado:

```bash
php artisan serve
npm run dev
```

Abre la URL mostrada por Laravel, normalmente `http://localhost:8000`.

## Configuración de producción

En producción instala dependencias sin paquetes de desarrollo, configura una base de datos persistente y compila los recursos:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate --force --seed
php artisan storage:link
php artisan optimize
```

El servidor web debe apuntar a la carpeta `public`. Configura HTTPS, un proceso de colas si se agregan trabajos asíncronos y permisos de escritura para `storage` y `bootstrap/cache`.

## Configuración de OpenAI

La clave se usa únicamente en el servidor. Nunca debe incluirse en React, publicarse en el repositorio ni escribirse directamente en `config/services.php`.

```dotenv
OPENAI_API_KEY=tu_clave_aqui
OPENAI_MODEL=gpt-4o-mini
OPENAI_BASE_URL=https://api.openai.com/v1
```

Si no existe una clave, el formulario manual permanece completamente disponible y la interfaz muestra que la asistencia de IA no está configurada.

Después de modificar variables de entorno, limpia la configuración almacenada:

```bash
php artisan config:clear
```

## Crear la primera cuenta administradora

Registra la cuenta normalmente y luego ejecuta:

```bash
php artisan qrbita:make-admin correo@ejemplo.com
```

## Verificación

```bash
vendor/bin/pint --test
vendor/bin/phpstan analyse
php artisan test
npm run check
npm run types:check
npm run build
```

Las imágenes de las vCards se almacenan en el disco `public`; en producción es necesario conservar el enlace creado por `php artisan storage:link`. Configura `APP_URL` y `APP_TIMEZONE` para el dominio y zona horaria definitivos antes de publicar.

## Solución rápida de problemas

- Si aparece un error de clave de aplicación, ejecuta `php artisan key:generate`.
- Si las imágenes no cargan, verifica que exista `public/storage` y vuelve a ejecutar `php artisan storage:link`.
- Si la IA no está disponible, confirma `OPENAI_API_KEY`, ejecuta `php artisan config:clear` y revisa la conectividad del servidor. El formulario manual seguirá funcionando.
- Si npm o Composer reportan versiones incompatibles, utiliza Node.js 20 LTS, PHP 8.3+ y vuelve a ejecutar la instalación desde los archivos de bloqueo.
