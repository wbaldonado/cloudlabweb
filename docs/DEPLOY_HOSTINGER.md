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

## Referencias

- Plantilla env: `.env.hostinger.example`
- Guía general: `PRODUCTION_DEPLOYMENT.md`
- Script local: `deploy/prepare-hostinger.ps1`
- Script servidor: `deploy/post-deploy-hostinger.sh`
