# Despliegue en Hostinger — CloudLab Latam

Guía rápida para subir esta app Laravel 9 a **Hostinger** (hPanel + SFTP).

---

## Requisitos en Hostinger

| Requisito | Valor |
|-----------|--------|
| PHP | **8.1** o superior (hPanel → Avanzado → Configuración de PHP) |
| Extensiones | `mbstring`, `openssl`, `pdo_mysql`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `curl` |
| Base de datos | MySQL (crear en hPanel → Bases de datos) |
| Document root | Carpeta **`public`** del proyecto (obligatorio) |

---

## Paso 1 — Preparar en tu PC (XAMPP)

En la raíz del proyecto:

```powershell
powershell -ExecutionPolicy Bypass -File deploy\prepare-hostinger.ps1
```

Eso ejecuta (si tienes las herramientas):

- `composer install --no-dev --optimize-autoloader`
- `npm run build` → genera `public/build/`
- `php artisan optimize:clear`

Revisa `deploy/HOSTINGER_UPLOAD_CHECKLIST.txt` después de ejecutarlo.

---

## Paso 2 — Subir archivos (SFTP)

**Cliente recomendado:** FileZilla o el Administrador de archivos de Hostinger (SFTP puerto **22**).

### Carpeta en el servidor

Ejemplo típico:

```text
/home/u123456789/domains/cloudlablatam.tech/
├── app/
├── bootstrap/
├── config/
├── public/          ← document root debe apuntar AQUÍ
├── storage/
├── vendor/          (o instalar con Composer en el servidor)
├── .env             (crear en el servidor, no subir el de local)
└── artisan
```

### Subir

- Todas las carpetas del proyecto **excepto** `node_modules`, `.git`, `.env`
- Si no subes `vendor/`, instala en el servidor con SSH:  
  `composer install --no-dev --optimize-autoloader`
- **Sí subir** `public/build/` si compilaste assets en local

### No subir nunca

- `.env` de tu XAMPP (secretos locales)
- `node_modules/`

---

## Paso 3 — Document root = `public`

En **hPanel → Sitios web → Administrar → Dominios** (o Configuración del sitio):

1. **Directorio raíz / Document root** → ruta que termina en **`/public`**  
   Ejemplo: `/home/u123456789/domains/cloudlablatam.tech/public`

2. Guardar y esperar 1–2 minutos.

Si el document root apunta a la raíz del proyecto (sin `public`), se expone `.env` y la app no arranca bien.

---

## Paso 4 — Archivo `.env` en producción

1. En el servidor, copia `.env.hostinger.example` → `.env`
2. Edita con datos reales:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://cloudlablatam.tech

DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

SESSION_SECURE_COOKIE=true
TRUSTED_PROXIES=*

MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

3. Genera `APP_KEY` (elige una opción):

**SSH / Terminal Hostinger:**

```bash
cd /home/u123456789/domains/cloudlablatam.tech
php artisan key:generate --force
```

**Sin SSH:** ver sección “Artisan sin SSH” más abajo.

---

## Paso 5 — Permisos y storage

En hPanel → Archivos → Permisos (o por SSH):

```bash
chmod -R 775 storage bootstrap/cache
```

Enlace de storage (subidas públicas):

```bash
php artisan storage:link
```

Si el enlace simbólico no está permitido, copia el contenido de `storage/app/public` a `public/storage` manualmente.

---

## Paso 6 — Base de datos y cachés

### Opción A — Importar SQL en phpMyAdmin (estructura vacía)

1. hPanel → **Bases de datos** → crear base MySQL y usuario
2. Abrir **phpMyAdmin** → seleccionar tu base de datos
3. Pestaña **Importar** → archivo **`database/sql/hostinger_schema.sql`**
4. Esto crea todas las tablas + registro en `migrations` (no hace falta `php artisan migrate` para la estructura)
5. Crear usuario admin (SSH): `php artisan db:seed --class=AdminUserSeeder`  
   O importar un dump de datos desde tu XAMPP local si ya tienes contenido

### Opción B — Migraciones Laravel (SSH)

Con SSH en la raíz del proyecto:

```bash
bash deploy/post-deploy-hostinger.sh
```

O manualmente:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Logo not visible on Hostinger

### Step 1 — Confirm the image file exists

Upload **`logo-cl.png`** to:

```text
laravel_aoo/public/images/logo-cl.png
```

Also upload **`logo.png`** as fallback in the same folder.

### Step 2 — Test the URL in your browser

Open:

```text
https://TU-DOMINIO.com/images/logo-cl.png
```

| Result | Action |
|--------|--------|
| **Image shows** | File is OK. Upload updated code (see Step 4) and clear cache. |
| **404 Not Found** | File missing or document root is wrong (see Step 3). |

### Step 3 — Document root must be `public`

hPanel → domain → **Document root** must end with **`/public`**, e.g.:

```text
.../laravel_aoo/public
```

Not `.../laravel_aoo` alone.

If you cannot change document root, add to `.env`:

```env
ASSET_PREFIX=laravel_aoo/public
```

(or whatever path appears before `/images/` in your site URL)

### Step 4 — Upload these files to Hostinger

| File |
|------|
| `app/Support/Branding.php` |
| `resources/views/components/navigation.blade.php` |
| `views/components/navigation.blade.php` |
| `views/layouts/app.blade.php` |
| `public/images/logo-cl.png` |
| `public/css/cloudlab.css` |

Then run (SSH):

```bash
php artisan view:clear
php artisan config:clear
```

### Step 5 — Optional `.env` override

If `/images/logo-cl.png` works in the browser but not in the page:

```env
LOGO_URL=https://TU-DOMINIO.com/images/logo-cl.png
```

---

## Paso 7 — Comprobar

- [ ] `https://tu-dominio.com/` carga el home
- [ ] `/blogs` y un post “Leer más” funcionan
- [ ] CSS `cloudlab.css` e imágenes cargan
- [ ] `/admin/login` accesible
- [ ] Formulario de contacto (si usas correo SMTP)

Errores **500** → revisar `storage/logs/laravel.log` en el administrador de archivos.

---

## Artisan sin SSH

1. Añade en `.env`: `DEPLOY_RUN_TOKEN=una-cadena-larga-aleatoria`
2. Copia **temporalmente** `deploy/run-artisan.php` a `public/run-artisan-once.php`
3. Visita: `https://tu-dominio.com/run-artisan-once.php?token=TU_TOKEN`
4. **Borra** el archivo de `public/` inmediatamente después

---

## Actualizar después de cambios

Sube solo archivos modificados, luego en el servidor:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Si hay migraciones nuevas: `php artisan migrate --force`

---

## Error 500 after uploading files

### Fix 1 — Clear cached config (try first)

Delete all `.php` files in `laravel_aoo/bootstrap/cache/` via File Manager.

### Fix 2 — APP_KEY

```bash
php artisan key:generate --force
php artisan config:clear
```

### Fix 3 — See real error (temporary)

```env
APP_DEBUG=true
```

Set back to `false` after fixing.

### Fix 4 — Disable CSP temporarily

```env
CSP_ENABLED=false
```

### Fix 5 — Diagnostic script

Upload `deploy/hostinger-check.php` → `public/hostinger-check.php`, set `CHECK_TOKEN`, visit with `?token=...`, then delete the file.

### Fix 6 — Re-upload these files (logo fix, no Branding class needed)

- `config/app.php`
- `resources/views/components/navigation.blade.php`
- `views/components/navigation.blade.php`
- `views/layouts/app.blade.php`

---

## Referencias

- Plantilla env: `.env.hostinger.example`
- Guía general: `PRODUCTION_DEPLOYMENT.md`
- Script local: `deploy/prepare-hostinger.ps1`
- Script servidor: `deploy/post-deploy-hostinger.sh`
