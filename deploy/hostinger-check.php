<?php
/**
 * Hostinger 500 error diagnostic (temporary — delete after fixing).
 *
 * 1. Copy to: laravel_aoo/public/hostinger-check.php
 * 2. Visit: https://your-domain.com/hostinger-check.php?token=CHANGE_ME
 * 3. Set the same token below, then DELETE this file when done.
 */
declare(strict_types=1);

const CHECK_TOKEN = 'CHANGE_ME';

if (($_GET['token'] ?? '') !== CHECK_TOKEN) {
    http_response_code(403);
    exit('Forbidden. Set CHECK_TOKEN in hostinger-check.php and pass ?token=...');
}

header('Content-Type: text/plain; charset=utf-8');

$root = dirname(__DIR__);
echo "CloudLab Latam — Hostinger diagnostic\n";
echo str_repeat('=', 50) . "\n\n";

echo "PHP version: " . PHP_VERSION . "\n";
echo "Project root: {$root}\n\n";

$checks = [
    'vendor/autoload.php' => $root . '/vendor/autoload.php',
    '.env' => $root . '/.env',
    'artisan' => $root . '/artisan',
    'config/app.php' => $root . '/config/app.php',
    'config/csp.php' => $root . '/config/csp.php',
    'public/index.php' => $root . '/public/index.php',
    'storage/logs' => $root . '/storage/logs',
    'bootstrap/cache' => $root . '/bootstrap/cache',
    'logo-cl.png' => $root . '/public/images/logo-cl.png',
];

echo "Files / folders:\n";
foreach ($checks as $label => $path) {
    $ok = file_exists($path);
    $writable = is_dir($path) ? is_writable($path) : (is_file($path) ? is_readable($path) : false);
    echo sprintf("  [%s] %s%s\n", $ok ? 'OK' : 'MISSING', $label, ($ok && is_dir($path) && ! $writable) ? ' (not writable!)' : '');
}

echo "\n.env:\n";
if (is_file($root . '/.env')) {
    $env = file_get_contents($root . '/.env');
    foreach (['APP_KEY', 'APP_ENV', 'APP_DEBUG', 'APP_URL', 'DB_DATABASE', 'DB_USERNAME', 'DB_HOST'] as $key) {
        if (preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $env, $m)) {
            $val = trim($m[1], " \t\"'");
            if ($key === 'APP_KEY') {
                $val = ($val !== '' && str_starts_with($val, 'base64:')) ? 'set (base64:...)' : ($val === '' ? 'EMPTY — run php artisan key:generate' : 'invalid?');
            }
            if (in_array($key, ['DB_USERNAME', 'DB_PASSWORD'], true) && $val !== '') {
                $val = '***';
            }
            echo "  {$key}={$val}\n";
        } else {
            echo "  {$key}=NOT SET\n";
        }
    }
} else {
    echo "  .env file not found\n";
}

echo "\nCached config (delete if 500 after deploy):\n";
foreach (glob($root . '/bootstrap/cache/*.php') ?: [] as $file) {
    echo '  ' . basename($file) . ' (' . date('Y-m-d H:i:s', filemtime($file)) . ")\n";
}
if (! glob($root . '/bootstrap/cache/*.php')) {
    echo "  (none)\n";
}

echo "\nLaravel boot test:\n";
try {
    require $root . '/vendor/autoload.php';
    $app = require $root . '/bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "  [OK] Application kernel loaded\n";
} catch (Throwable $e) {
    echo "  [FAIL] " . $e->getMessage() . "\n";
    echo "  at " . $e->getFile() . ':' . $e->getLine() . "\n";
}

echo "\nLast log lines (storage/logs/laravel.log):\n";
$logFile = $root . '/storage/logs/laravel.log';
if (is_readable($logFile)) {
    $lines = file($logFile, FILE_IGNORE_NEW_LINES);
    $tail = array_slice($lines ?: [], -25);
    foreach ($tail as $line) {
        echo '  ' . $line . "\n";
    }
} else {
    echo "  (log not readable or empty)\n";
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "Common fixes:\n";
echo "  1. Delete bootstrap/cache/config.php and route.php via File Manager\n";
echo "  2. APP_KEY empty → php artisan key:generate --force\n";
echo "  3. chmod 775 storage bootstrap/cache\n";
echo "  4. CSP issues → CSP_ENABLED=false in .env\n";
echo "  5. DB wrong → fix DB_* in .env\n";
echo "  DELETE public/hostinger-check.php when finished.\n";
