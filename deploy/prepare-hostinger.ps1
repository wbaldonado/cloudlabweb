# Prepare CloudLab Latam for Hostinger upload (run locally before FTP/SFTP)
# Usage: powershell -ExecutionPolicy Bypass -File deploy\prepare-hostinger.ps1

$ErrorActionPreference = "Continue"
$Root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
Set-Location $Root

Write-Host "=== CloudLab Latam - Hostinger prepare ===" -ForegroundColor Cyan
Write-Host "Project: $Root"
Write-Host ""

if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: php not in PATH. Add XAMPP php to PATH." -ForegroundColor Red
    exit 1
}
Write-Host "PHP: $(php -r 'echo PHP_VERSION;')"

if (Get-Command composer -ErrorAction SilentlyContinue) {
    Write-Host ""
    Write-Host "[1/4] composer install --no-dev --optimize-autoloader ..."
    composer install --no-dev --optimize-autoloader --no-interaction
} else {
    Write-Host "WARN: composer not found. Run composer on Hostinger." -ForegroundColor Yellow
}

if (Get-Command npm -ErrorAction SilentlyContinue) {
    Write-Host ""
    Write-Host "[2/4] npm install and npm run build ..."
    if (Test-Path "package-lock.json") { npm ci } else { npm install }
    npm run build
    if (-not (Test-Path "public\build\manifest.json")) {
        Write-Host "WARN: public/build/manifest.json missing." -ForegroundColor Yellow
    } else {
        Write-Host "OK: public/build/manifest.json exists."
    }
} else {
    Write-Host "WARN: npm not found. Upload existing public/build/ if present." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "[3/4] php artisan optimize:clear ..."
php artisan optimize:clear

Write-Host ""
Write-Host "[4/4] Writing deploy/HOSTINGER_UPLOAD_CHECKLIST.txt ..."
$lines = @(
    "CloudLab Latam - Hostinger upload checklist",
    "Generated: $(Get-Date -Format 'yyyy-MM-dd HH:mm')",
    "",
    "BEFORE UPLOAD (local)",
    "[ ] Ran deploy/prepare-hostinger.ps1",
    "[ ] .env NOT uploaded (use .env.hostinger.example on server)",
    "[ ] vendor/ uploaded OR composer install on server",
    "[ ] public/build/ present",
    "",
    "ON SERVER (hPanel)",
    "[ ] PHP 8.1+",
    "[ ] Document root = .../public",
    "[ ] .env configured",
    "[ ] php artisan key:generate",
    "[ ] chmod 775 storage bootstrap/cache",
    "[ ] php artisan storage:link",
    "[ ] php artisan migrate --force",
    "[ ] php artisan config:cache route:cache view:cache",
    "",
    "Full guide: docs/DEPLOY_HOSTINGER.md"
)
$lines | Out-File -FilePath "deploy\HOSTINGER_UPLOAD_CHECKLIST.txt" -Encoding utf8

Write-Host ""
Write-Host "Done. See docs/DEPLOY_HOSTINGER.md" -ForegroundColor Green
