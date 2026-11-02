# Prepare CloudLab Latam for Hostinger upload
# Usage: powershell -ExecutionPolicy Bypass -File deploy\prepare-hostinger.ps1

$ErrorActionPreference = "Continue"
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

Write-Host "=== CloudLab Latam - Hostinger rebuild ===" -ForegroundColor Cyan
Write-Host "Project: $Root`n"

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: php not in PATH." -ForegroundColor Red
    exit 1
}
Write-Host "PHP: $(php -r 'echo PHP_VERSION;')"

# Remove local caches — do NOT upload these to Hostinger
Write-Host "`n[1/6] Clearing local caches (bootstrap/cache, storage views) ..."
Get-ChildItem "$Root\bootstrap\cache\*.php" -ErrorAction SilentlyContinue | Remove-Item -Force
Get-ChildItem "$Root\storage\framework\views\*.php" -ErrorAction SilentlyContinue | Remove-Item -Force
php artisan optimize:clear 2>$null

if (Get-Command composer -ErrorAction SilentlyContinue) {
    Write-Host "`n[2/6] composer install --no-dev --optimize-autoloader ..."
    composer install --no-dev --optimize-autoloader --no-interaction
} else {
    Write-Host "WARN: composer not found." -ForegroundColor Yellow
}

if (Get-Command npm -ErrorAction SilentlyContinue) {
    Write-Host "`n[3/6] npm ci && npm run build ..."
    if (Test-Path "package-lock.json") { npm ci } else { npm install }
    npm run build
} else {
    Write-Host "WARN: npm not found." -ForegroundColor Yellow
}

Write-Host "`n[4/6] Verifying required files ..."
$required = @(
    "artisan",
    "composer.json",
    "composer.lock",
    "vendor\autoload.php",
    "public\index.php",
    "public\css\cloudlab.css",
    "public\js\card-glow.js",
    "public\images\logo-cl.png",
    "public\build\manifest.json",
    "config\app.php",
    "config\view.php",
    "config\csp.php",
    "app\Support\Branding.php",
    "views\layouts\app.blade.php",
    "views\components\navigation.blade.php",
    "resources\views\components\navigation.blade.php",
    "resources\views\home.blade.php",
    "database\sql\hostinger_schema.sql",
    ".env.hostinger.example"
)
$missing = @()
foreach ($f in $required) {
    if (Test-Path (Join-Path $Root $f)) {
        Write-Host "  OK  $f"
    } else {
        Write-Host "  MISSING  $f" -ForegroundColor Red
        $missing += $f
    }
}

Write-Host "`n[5/6] Writing deploy/HOSTINGER_UPLOAD_FILES.txt ..."
$uploadDirs = @(
    "app", "bootstrap", "config", "database", "deploy", "docs", "lang",
    "public", "resources", "routes", "storage", "vendor", "views"
)
$uploadFiles = @(
    "artisan", "composer.json", "composer.lock",
    ".env.hostinger.example", ".htaccess"
)
$manifest = @(
    "CloudLab Latam - UPLOAD MANIFEST FOR HOSTINGER",
    "Generated: $(Get-Date -Format 'yyyy-MM-dd HH:mm')",
    "Target server path: .../domains/cloudlablatam.tech/laravel_app/",
    "Document root MUST be: .../laravel_app/public",
    "",
    "=== UPLOAD THESE FOLDERS (entire folder) ==="
)
foreach ($d in $uploadDirs) { $manifest += "  $d/" }
$manifest += ""
$manifest += "=== UPLOAD THESE ROOT FILES ==="
foreach ($f in $uploadFiles) { $manifest += "  $f" }
$manifest += ""
$manifest += "=== DO NOT UPLOAD ==="
$manifest += "  .env (create on server from .env.hostinger.example)"
$manifest += "  .git/"
$manifest += "  node_modules/"
$manifest += "  tests/ (optional)"
$manifest += "  bootstrap/cache/*.php (empty cache on server)"
$manifest += ""
$manifest += "=== AFTER UPLOAD (SSH or Terminal) ==="
$manifest += "  cd laravel_app"
$manifest += "  cp .env.hostinger.example .env   # then edit DB + APP_URL"
$manifest += "  php artisan key:generate --force"
$manifest += "  php artisan storage:link"
$manifest += "  php artisan migrate --force   # OR import database/sql/hostinger_schema.sql"
$manifest += "  php artisan config:cache"
$manifest += "  php artisan route:cache"
$manifest += "  php artisan view:cache"
$manifest += "  chmod -R 775 storage bootstrap/cache"
$manifest += ""
$manifest += "=== .env MINIMUM (production) ==="
$manifest += "  APP_ENV=production"
$manifest += "  APP_DEBUG=false"
$manifest += "  APP_URL=https://cloudlablatam.tech"
$manifest += "  DB_HOST=localhost"
$manifest += "  DB_DATABASE=..."
$manifest += "  DB_USERNAME=..."
$manifest += "  DB_PASSWORD=..."
$manifest += "  SESSION_SECURE_COOKIE=true"
$manifest += "  TRUSTED_PROXIES=*"
$manifest += ""
$manifest += "See docs/DEPLOY_HOSTINGER.md"
$manifest | Out-File -FilePath "$Root\deploy\HOSTINGER_UPLOAD_FILES.txt" -Encoding utf8

Write-Host "`n[6/6] Summary"
if ($missing.Count -gt 0) {
    Write-Host "WARNING: $($missing.Count) required file(s) missing." -ForegroundColor Yellow
} else {
    Write-Host "All required files present." -ForegroundColor Green
}
Write-Host "`nUpload guide: deploy\HOSTINGER_UPLOAD_FILES.txt"
Write-Host "Full docs:    docs\DEPLOY_HOSTINGER.md"
Write-Host "Done." -ForegroundColor Green
