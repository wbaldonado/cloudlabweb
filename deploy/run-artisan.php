<?php
/**
 * One-time Laravel tasks on Hostinger when SSH is unavailable.
 *
 * 1. Set DEPLOY_RUN_TOKEN in .env (long random string).
 * 2. Upload this file to the project ROOT (same folder as artisan).
 * 3. Visit: https://your-domain.com/../deploy/run-artisan.php?token=YOUR_TOKEN
 *    (adjust URL if file is in /deploy/ — use https://domain.com/deploy/run-artisan.php?token=...)
 * 4. DELETE this file immediately after success.
 */

declare(strict_types=1);

$root = dirname(__DIR__);
if (! is_file($root . '/artisan')) {
    http_response_code(500);
    exit('artisan not found. Place this script in deploy/ under project root.');
}

$envFile = $root . '/.env';
if (! is_file($envFile)) {
    http_response_code(500);
    exit('.env missing');
}

$env = file_get_contents($envFile);
if (! preg_match('/^DEPLOY_RUN_TOKEN=(.+)$/m', $env, $m) || trim($m[1], " \t\"'") === '') {
    http_response_code(403);
    exit('Set DEPLOY_RUN_TOKEN=... in .env first.');
}

$expected = trim($m[1], " \t\"'");
$given = $_GET['token'] ?? '';
if (! is_string($given) || $given === '' || ! hash_equals($expected, $given)) {
    http_response_code(403);
    exit('Invalid or missing token.');
}

header('Content-Type: text/plain; charset=utf-8');

$php = PHP_BINARY ?: 'php';
$artisan = escapeshellarg($root . '/artisan');
$commands = [
    'key:generate --force' => 'APP_KEY',
    'storage:link' => 'storage link',
    'migrate --force' => 'migrations',
    'config:cache' => 'config cache',
    'route:cache' => 'route cache',
    'view:cache' => 'view cache',
];

foreach ($commands as $cmd => $label) {
    echo ">>> artisan $cmd\n";
    passthru("$php $artisan $cmd 2>&1", $code);
    echo $code === 0 ? "OK: $label\n\n" : "FAIL ($code): $label\n\n";
}

echo "Done. DELETE deploy/run-artisan.php now.\n";
