# Production Deployment Guide — CloudLab Latam

Step-by-step instructions to move the Laravel application to production (shared hosting, VPS, or similar).

> **Hostinger:** use the dedicated guide **[docs/DEPLOY_HOSTINGER.md](docs/DEPLOY_HOSTINGER.md)** and run `deploy/prepare-hostinger.ps1` before uploading.

---

## 1. Before You Deploy

### 1.1 Server requirements

- **PHP** 8.1 or higher (extensions: `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `bcmath` if using decimals)
- **Composer** (for dependency install)
- **Node.js** 18+ and **npm** (to build frontend assets), or build assets locally and upload `public/build/`
- **MySQL** or **MariaDB** (or DB supported by Laravel)
- **SSH** access (recommended) or FTP/SFTP

### 1.2 Prepare locally (optional but recommended)

Run from your project root (e.g. `C:\xampp\htdocs\cloudlablatam1`):

```bash
# Install production dependencies
composer install --no-dev --optimize-autoloader

# Build frontend assets
npm ci
npm run build

# Clear caches before packing
php artisan optimize:clear
```

Keep `node_modules` and `vendor` in `.gitignore`; you will install them on the server (or only upload `public/build/` if you built locally).

---

## 2. Upload Project to Server

### Option A: Git (recommended on VPS)

On the server:

```bash
cd /path/to/your/web/root
git clone YOUR_REPO_URL .
```

Then install dependencies and build on the server (see Section 3).

### Option B: FTP/SFTP (e.g. Hostinger, cPanel)

See **Section 2.1** below for detailed step-by-step FTP instructions.

---

## 2.1 Detailed instructions: Uploading with FTP

Use these steps when you deploy using an FTP client (FileZilla, WinSCP, Cyberduck, or your host’s file manager).

### Step 1: Get your FTP credentials

From your hosting panel (Hostinger, cPanel, etc.):

- **FTP host:** e.g. `ftp.yourdomain.com` or `yourdomain.com` (sometimes with a port like `21` for FTP or `22` for SFTP).
- **Username:** your FTP user (often the same as your hosting account or a sub-account).
- **Password:** the FTP password.
- **Port:** `21` (FTP) or `22` (SFTP). Prefer **SFTP** if available (encrypted).

Write these down; you’ll enter them in the FTP client.

### Step 2: Choose the remote folder (application root)

- **Shared hosting (e.g. Hostinger):**  
  Upload to the folder that will be the **Laravel application root**. Common names:
  - `domains/yourdomain.com/public_html/` — then you will set document root to a **subfolder** (see Step 6), or
  - `domains/yourdomain.com/private/` or `htdocs/` — document root will point to `.../private/public` or `.../htdocs/public`.

- **cPanel:**  
  Often `public_html/` is the document root. For Laravel you have two options:
  1. Upload the whole project into a subfolder, e.g. `public_html/cloudlablatam1/`, and set the **domain’s document root** to `public_html/cloudlablatam1/public`, or  
  2. Upload so that the contents of your local `public` folder are in `public_html/`, and the rest of the app (app, config, etc.) is **above** `public_html/` (some hosts allow a parent folder like `private/` or `laravel/`). Prefer option 1 if you’re unsure.

- **Important:** The remote folder you upload to must contain, after upload, the **same structure** as your project: `app/`, `config/`, `public/`, `storage/`, etc. So you upload the **contents** of your project into that folder (or the project folder itself as one folder). See next step.

### Step 3: What to upload and what NOT to upload

**Upload these (from your project root, e.g. `C:\xampp\htdocs\cloudlablatam1`):**

| Upload | Notes |
|--------|--------|
| `app/` | All files and subfolders |
| `bootstrap/` | All (including `cache/`; empty is fine) |
| `config/` | All |
| `database/` | All (migrations, seeders) |
| `lang/` or `resources/lang/` | All |
| `public/` | **Entire folder** including `index.php`, `images/`, and (if built) `build/` |
| `resources/` | All (views, etc.) |
| `routes/` | All |
| `storage/` | **Folder structure only** (see below) |
| `artisan` | File |
| `composer.json` | File |
| `composer.lock` | File |
| `package.json` | File (optional; only if you run npm on server) |
| `package-lock.json` | File (optional) |
| `.env.example` | File (you will create `.env` from it on the server) |
| `vite.config.js` | File (optional; for building on server) |
| `postcss.config.js` | File (optional) |

**Do NOT upload:**

| Do not upload | Reason |
|---------------|--------|
| `.env` | Contains local secrets; create a new `.env` on the server |
| `node_modules/` | Very large; rebuild with `npm ci` on server or build locally and upload only `public/build/` |
| `vendor/` | PHP dependencies; install on server with `composer install` or use host’s “Composer” tool |
| `.git/` | Not needed in production; saves space and avoids exposing repo |
| `tests/` | Optional; omit to save space |
| `storage/logs/*.log` | Log files from your PC |
| `storage/framework/cache/data/*` | Local cache |
| `storage/framework/sessions/*` | Local sessions |
| `storage/framework/views/*` | Compiled views (Laravel will recreate) |
| `.editorconfig`, `.gitattributes`, `.gitignore` | Optional; no effect on running the app |

**Storage folder:**  
Upload the **folders** inside `storage/` so the structure exists: `storage/app/`, `storage/framework/cache/data/`, `storage/framework/sessions/`, `storage/framework/views/`, `storage/logs/`. You can leave these empty; the server needs writable directories. If your client doesn’t create empty folders, create them on the server (e.g. `storage/framework/sessions`, `storage/framework/views`, `storage/framework/cache/data`, `storage/logs`) and upload a small file like `.gitignore` into each so the folder exists.

### Step 4: Build assets locally (recommended if you don’t have SSH)

If you **cannot** run `composer` or `npm` on the server:

1. On your PC, in the project root:
   ```bash
   npm ci
   npm run build
   ```
2. Upload the **entire** `public/build/` folder (and its contents) to the server so it appears at `public/build/` in the application root. This way CSS and JS work in production.

If you **can** run Composer on the server (e.g. Hostinger “Composer” in panel), you can skip uploading `vendor/` and run `composer install --no-dev --optimize-autoloader` there; you still need to either build and upload `public/build/` or run `npm run build` on the server.

### Step 5: Upload order and tips

1. **Create the remote application root folder** (e.g. `cloudlablatam1` or use the folder your host gave you).
2. **Upload folders first** (so structure exists), then files. In FileZilla: drag the **contents** of your project folder into the remote folder so you get `app/`, `config/`, `public/`, etc. directly inside the remote folder — not an extra nested `cloudlablatam1` inside it unless you want that.
3. **Exclude** `node_modules`, `vendor`, `.env`, `.git` (in FileZilla: Site Manager → Transfer settings → “Skip files” / “Filter” to hide them, or simply do not drag them).
4. **Double-check** that `public/index.php` and `public/build/` (if you built assets) exist on the server.
5. **Transfer mode:** use **Binary** for images and compiled files; **Auto** is usually fine. For PHP/Blade/config, ASCII or Auto is fine.

### Step 6: Create `.env` on the server

You must not use your local `.env` on production. On the server:

**Option A – File manager (no SSH):**  
1. In the host’s file manager, go to the application root.  
2. Copy `.env.example` to a new file named `.env`.  
3. Edit `.env`: set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://yourdomain.com`, and your production database credentials.  
4. If the host has “Generate Laravel key” or similar, use it; otherwise set `APP_KEY=` and run key generation later (e.g. one-time PHP script or SSH).

**Option B – FTP:**  
1. Download `.env.example` from the server (or upload it if you did not).  
2. Locally, copy it to a file named `env.txt` and edit: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, DB_*.  
3. Upload `env.txt` to the application root.  
4. In the host’s file manager, rename `env.txt` to `.env` (FTP often cannot create names starting with a dot).

### Step 7: Set the document root to `public`

The web server must use the **`public`** folder as the document root, not the project root.

- **Shared hosting / panel:** Find “Document root”, “Web root”, or “Domain root” and set it to the **`public`** directory of your app, e.g.  
  `.../public_html/cloudlablatam1/public`  
  or  
  `.../domains/yourdomain.com/private/public`.

- If you cannot change document root and **must** use a folder like `public_html/` as root: then put the **contents** of your local `public/` (e.g. `index.php`, `build/`, `images/`) into `public_html/`, and move `app/`, `config/`, etc. to a sibling folder (e.g. `private/` or `laravel/`) and adjust `public/index.php` so it points to the correct parent (bootstrap path). This is host-specific; the clean solution is to set document root to `public`.

### Step 8: After FTP upload

1. **Permissions (if your host allows):** Set `storage` and `bootstrap/cache` to writable (e.g. 755 or 775; host panels often have “Permissions” or “CHMOD”).
2. **Storage link:** If the host has “Create storage link” or you have SSH, run `php artisan storage:link`. Otherwise create a symbolic link from `public/storage` to `storage/app/public` via the panel if the option exists.
3. **Composer (if available):** Run `composer install --no-dev --optimize-autoloader` in the application root (SSH or host’s “Composer” / “Run script”).
4. **Laravel caches:** If you can run Artisan (SSH or host’s “Run PHP” / “Cron” with `php artisan config:cache` etc.), run:
   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
5. **Migrations:** Run `php artisan migrate --force` (SSH or host tool that runs PHP in the project root).

If you have **no SSH and no “Run PHP”**, you may need to run migrations and key generation via a one-time PHP script or the host’s installer; see your host’s Laravel documentation.

### Step 9: Verify

- Open `https://yourdomain.com` — you should see the app, not a directory listing or 500 error.
- Open `https://yourdomain.com/blogs` — blog listing loads.
- Click “Read more” on a post — single post page loads (no 404).
- Check that CSS/JS and images load (no broken layout or 404 for `build/` or `images/`).

If you see a 500 error, check the host’s error log or `storage/logs/laravel.log` (via FTP or file manager) and ensure `.env` has a valid `APP_KEY` (e.g. run `php artisan key:generate` if you have a way to run Artisan).

---

## 3. Environment File (`.env`)

1. On the server, copy the example env and edit:

   ```bash
   cp .env.example .env
   nano .env   # or use your host’s file manager
   ```

2. **Required production settings:**

   ```env
   APP_NAME="CloudLab Latam"
   APP_ENV=production
   APP_KEY=base64:XXXXX
   APP_DEBUG=false
   APP_URL=https://yourdomain.com

   LOG_CHANNEL=stack
   LOG_LEVEL=error

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

3. **Generate application key** (if not set):

   ```bash
   php artisan key:generate
   ```

4. **Security:** Never commit `.env` to Git. Keep `APP_DEBUG=false` and `APP_ENV=production` in production.

---

## 4. Install Dependencies & Build Assets (on server)

Run these in the **project root** (where `artisan` and `composer.json` are).

```bash
# Composer (production only)
composer install --no-dev --optimize-autoloader

# Node (if you have Node on the server)
npm ci
npm run build
```

If you **cannot** run Node on the server: build locally with `npm run build`, then upload the `public/build/` folder via FTP so production has the compiled CSS/JS.

---

## 5. Laravel Setup Commands

Run in order, from project root:

```bash
# 1. Storage link (for uploads and public storage)
php artisan storage:link

# 2. Create dirs if missing
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache

# 3. Permissions (Linux; adjust user/group if needed)
chmod -R 775 storage bootstrap/cache
# If you have a web user (e.g. www-data):
# chown -R www-data:www-data storage bootstrap/cache

# 4. Run migrations
php artisan migrate --force

# 5. Clear then rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**One-liner (clear + cache):**

```bash
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

---

## 6. Web Server Configuration

### Document root

- Must be the **`public`** directory of the project (e.g. `.../cloudlablatam1/public`).
- Do **not** point the document root to the project root; that would expose `.env` and code.

### Apache (e.g. XAMPP, Hostinger, cPanel)

Ensure `public/.htaccess` exists (Laravel includes it). If the app is in a subdirectory, document root should still be `.../public`.

Example `public/.htaccess` (Laravel default):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ index.php [L]
</IfModule>
```

Full Laravel default uses `RewriteCond` to skip existing files; your project may already have the full version.

### Nginx (example)

```nginx
root /path/to/cloudlablatam1/public;
index index.php;

location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

---

## 7. Post-Deployment Checklist

- [ ] **URL:** Open `https://yourdomain.com` — no 404/500.
- [ ] **Blog:** Open `/blogs`, click “Read more” — blog post opens (no 404).
- [ ] **Assets:** CSS/JS and images load (check `public/build/` and `public/images/`).
- [ ] **Storage:** If you use uploads, test one and confirm the file is reachable (e.g. `/storage/...`).
- [ ] **Admin:** Log in and confirm admin area works.
- [ ] **Logs:** If something fails, check `storage/logs/laravel.log`.

---

## 8. Optional: Use the Quick Deploy Script (Linux/Mac)

If you have SSH and bash:

```bash
chmod +x QUICK_DEPLOY.sh
./QUICK_DEPLOY.sh
```

Answer the migration prompt. Then run storage link and permissions if not already done:

```bash
php artisan storage:link
chmod -R 775 storage bootstrap/cache
```

---

## 9. Fixing 500 Errors (e.g. Hostinger)

1. **Permissions:**

   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

2. **Clear and recache:**

   ```bash
   php artisan optimize:clear
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

3. **Check logs:**

   ```bash
   tail -n 100 storage/logs/laravel.log
   ```

4. **APP_KEY:** Ensure `.env` has `APP_KEY=base64:...`. If missing, run `php artisan key:generate`.

You can adapt `QUICK_FIX_HOSTINGER.sh`: set the `cd` path to your app root, then run the script via SSH.

---

## 10. After Code or Config Changes

Whenever you change routes, config, or code:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

If you add migrations:

```bash
php artisan migrate --force
```

---

## 11. Summary Command Order (copy-paste)

Run from **project root** on the server:

```bash
# Dependencies (if using Git or full upload without vendor)
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# Laravel
php artisan storage:link
mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Where to run:** Always in the Laravel project root (the directory that contains `artisan`, `composer.json`, and the `app/`, `config/`, `public/` folders).

---

## 12. Directory Reference

| Purpose              | Path (from project root)     |
|----------------------|------------------------------|
| Project root         | `./` (where `artisan` lives) |
| Document root        | `./public`                   |
| Logs                 | `storage/logs/laravel.log`   |
| Uploads (public)     | `storage/app/public` → linked at `public/storage` |
| Static images        | `public/images/`             |
| Compiled assets      | `public/build/`              |

Using this guide, you can move the app to production and keep it running correctly after updates.
