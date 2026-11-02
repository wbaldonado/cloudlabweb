# Guía de despliegue: CloudLab Latam (Laravel en Hostinger)

Procedimiento recomendado para publicar cambios en cloudlablatam.tech con Git por SSH, buenas prácticas y seguridad. Está pensado para el equipo que mantiene el sitio.

> **Antes de empezar:** el acceso SSH en Hostinger depende del plan (en general, Business o superior). Verifica en hPanel que tu plan lo incluye. Si no, ve a la sección "Alternativa sin SSH".

## 1. Estructura en el servidor

```
~/domains/cloudlablatam.tech/             ← aplicación Laravel completa
├── app/ bootstrap/ config/ database/ ...
├── vendor/  storage/  .env  .git/
└── public_html  →  enlace simbólico a public/
```

- Solo `public/` queda expuesta a internet.
- `.env`, `.git`, `vendor/` y los logs nunca deben quedar dentro de la carpeta web.
- Hostinger no permite cambiar la carpeta web desde `public_html`, por eso se usa el enlace simbólico.
- La función `symlink()` de PHP está desactivada en Hostinger, así que `php artisan storage:link` falla. El enlace de `storage` se crea con `ln -s` desde SSH.

## 2. Preparación inicial (solo la primera vez)

1. En hPanel, activa SSH y agrega tu llave pública (SSH Keys).
2. En GitHub, crea una **llave de despliegue de solo lectura** para el repositorio. No uses tu token personal.
3. Conéctate: `ssh -p 65002 USUARIO@HOST` (los datos exactos están en hPanel).
4. Elimina el contenido por defecto de `~/domains/cloudlablatam.tech/` y clona el repositorio ahí.
5. Crea el enlace de la carpeta web:
   ```bash
   cd ~/domains/cloudlablatam.tech
   ln -s public public_html
   ln -s ../storage/app/public public/storage
   ```
6. Crea el `.env` de producción directamente en el servidor (nunca lo subas desde tu equipo):
   ```bash
   cp .env.example .env
   nano .env
   php artisan key:generate
   chmod 600 .env
   ```
7. En hPanel: activa SSL, fuerza HTTPS y selecciona PHP 8.2 o superior.
8. Configura un cron para el scheduler de Laravel desde hPanel, si lo usas:
   ```
   * * * * * cd ~/domains/cloudlablatam.tech && php artisan schedule:run >> /dev/null 2>&1
   ```

### Variables de producción mínimas

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cloudlablatam.tech
SESSION_SECURE_COOKIE=true
LOG_LEVEL=warning
```

Usa credenciales propias de producción para la base de datos y el correo, con un usuario de base de datos de permisos limitados.

## 3. Antes de subir: lista de verificación en Cursor

Ejecuta estos pasos en la terminal de Cursor, en orden. Si alguno falla, no despliegues.

### 3.1 Limpieza y secretos
- [ ] `.gitignore` excluye `.env`, `vendor/`, `node_modules/` y `storage/logs`.
- [ ] No hay credenciales en el historial: `git log -p | grep -iE "password|secret|api_key"` (o usa `gitleaks`).
- [ ] No quedan paquetes ni rutas de depuración (Debugbar, Telescope, rutas de prueba).

### 3.2 Dependencias y seguridad
```bash
composer validate
composer audit
npm audit
```

### 3.3 Calidad y pruebas
```bash
./vendor/bin/pint
php artisan test
```

### 3.4 Probar en modo producción
```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan optimize
php artisan optimize:clear
```
Si `optimize` falla, suele haber rutas con funciones anónimas (closures) que deben pasar a controladores.

### 3.5 Revisión de la aplicación
- [ ] `.env.example` actualizado con las variables nuevas, sin valores reales.
- [ ] Límites de intentos (`throttle`) en login, `/admin`, formulario de contacto y campañas.
- [ ] Validación de subidas de archivos y del CSV de importación del CRM.
- [ ] CSRF activo. 2FA o restricción de acceso para `/admin`, si es posible.
- [ ] Pie de los correos de campaña con razón social, NIT, ciudad, contacto, política de datos y enlace de baja.

### 3.6 Migraciones
```bash
php artisan migrate --pretend
```
Revisa el SQL antes de ejecutarlo, sobre todo si toca tablas con datos reales.

### 3.7 SEO
- [ ] `sitemap.xml` y `robots.txt` correctos (`/admin` bloqueado, páginas públicas permitidas).
- [ ] Canonical, `hreflang` y metadatos de las páginas nuevas.

### 3.8 Versión
```bash
git status
git diff
git tag v1.1.0
git push origin main --tags
```

### Script opcional: `scripts/preflight.sh`

```bash
#!/usr/bin/env bash
set -euo pipefail

composer validate --strict
composer audit
npm audit --audit-level=high
./vendor/bin/pint --test
php artisan test
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan optimize
php artisan optimize:clear
composer install   # restaura dependencias de desarrollo
echo "Preflight completo: listo para desplegar"
```

## 4. Despliegue en cada versión

### 4.1 Probar primero en staging
Crea un subdominio (por ejemplo `staging.cloudlablatam.tech`) con contraseña y `noindex`, y despliega ahí antes de producción.

### 4.2 Respaldo
- Exporta la base de datos: `mysqldump -u USUARIO -p BASE > respaldo-$(date +%F).sql`
- Copia `storage/app` y el `.env`.
- Verifica en hPanel la frecuencia de las copias automáticas.

### 4.3 Publicar por SSH

```bash
cd ~/domains/cloudlablatam.tech
php artisan down --retry=60
git fetch --tags
git checkout v1.1.0
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
php artisan queue:restart    # solo si usas colas
php artisan up
```

Los assets compilados (`public/build`) deben existir en el servidor. Si Node no está disponible allí, compílalos en tu equipo o en GitHub Actions y súbelos con `rsync`:

```bash
rsync -rzv -e "ssh -p 65002" public/build/ USUARIO@HOST:~/domains/cloudlablatam.tech/public/build/
```

### Script opcional: `scripts/deploy.sh` (en el servidor)

```bash
#!/usr/bin/env bash
set -euo pipefail
TAG="${1:?Uso: ./deploy.sh vX.Y.Z}"

cd ~/domains/cloudlablatam.tech
php artisan down --retry=60
trap 'php artisan up' EXIT

git fetch --tags
git checkout "$TAG"
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize
echo "Desplegada la versión $TAG"
```

### 4.4 Verificación posterior
- [ ] Inicio y páginas de servicio cargan por HTTPS.
- [ ] Login del admin, CRM y panel de contenido funcionan.
- [ ] Un correo de campaña de prueba llega con el pie completo.
- [ ] `storage/logs` sin errores nuevos.
- [ ] Imágenes y estilos cargan (revisa `public/build` y el enlace `public/storage`).
- [ ] PageSpeed Insights sin regresiones.

### 4.5 Volver atrás si algo falla
```bash
git checkout v1.0.0
composer install --no-dev --optimize-autoloader
php artisan optimize
```
Si la versión nueva cambió la base de datos, restaura el respaldo o usa `php artisan migrate:rollback` solo si las migraciones lo permiten.

## 5. Seguridad: resumen

| Área | Práctica |
|---|---|
| Acceso | Llaves SSH, sin contraseña. 2FA en hPanel y GitHub. Llave de despliegue de solo lectura. |
| Archivos | Solo `public/` expuesta. Sin `phpinfo.php`, Adminer ni respaldos dentro del sitio. |
| Permisos | Carpetas 755, archivos 644, `.env` en 600, `storage/` y `bootstrap/cache/` escribibles. |
| Configuración | `APP_DEBUG=false`, HTTPS forzado, cookies seguras, PHP actualizado. |
| Base de datos | Usuario con permisos limitados, respaldos antes de cada migración. |
| Aplicación | `composer audit` y `npm audit` en cada versión, límites de intentos, validación de entradas. |
| Secretos | Nunca en el repositorio. Rotar credenciales si se sospecha una filtración. |

## 6. Alternativa sin SSH (menos recomendada)

1. Completa toda la lista de la sección 3 en Cursor, incluido `composer install --no-dev --optimize-autoloader` y `npm run build`.
2. Comprime el proyecto **sin** `.env`, `.git`, `node_modules` ni `storage/logs`.
3. Sube el contenido con el administrador de archivos o FTP, manteniendo la estructura de la sección 1 (aplicación fuera de la carpeta web, solo `public/` como carpeta pública).
4. Crea el `.env` de producción en el servidor.
5. Ejecuta migraciones y cachés desde una tarea programada o una ruta temporal protegida, y elimínala al terminar.

Esta opción exige más cuidado y deja menos trazabilidad. Si es posible, pasa a un plan con SSH.

## 7. Mejora futura: despliegue automático

Un flujo de GitHub Actions puede compilar los assets, copiarlos con `rsync` y ejecutar los comandos del servidor por SSH al crear una etiqueta de versión. Los secretos (host, usuario, llave) se guardan como secretos del repositorio, no en el código.

## Notas

- Las rutas, el puerto SSH (65002) y los pasos de hPanel pueden variar según tu plan. Confirma los datos exactos en tu panel.
- Probar siempre en staging reduce casi todos los riesgos de este procedimiento.
