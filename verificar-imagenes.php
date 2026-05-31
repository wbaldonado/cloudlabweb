<?php
/**
 * Script de verificación de imágenes
 * Coloca este archivo en la raíz de tu proyecto en producción y accede vía navegador
 * Ejemplo: https://tudominio.com/verificar-imagenes.php
 */

// Verificar que las imágenes existan
$images = [
    'es' => 'public/images/digital-transformation-es.png',
    'en' => 'public/images/digital-transformation-en.png',
];

$basePath = __DIR__;
$appUrl = env('APP_URL', 'http://localhost');

echo "<!DOCTYPE html>
<html>
<head>
    <title>Verificación de Imágenes</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; }
        .success { color: green; }
        .error { color: red; }
        .info { background: #e3f2fd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        img { max-width: 300px; border: 2px solid #ddd; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>Verificación de Imágenes - CloudLab Latam</h1>
        <div class='info'>
            <strong>Base Path:</strong> {$basePath}<br>
            <strong>APP_URL:</strong> {$appUrl}<br>
            <strong>Fecha:</strong> " . date('Y-m-d H:i:s') . "
        </div>
";

foreach ($images as $lang => $imagePath) {
    $fullPath = $basePath . '/' . $imagePath;
    $exists = file_exists($fullPath);
    $readable = $exists && is_readable($fullPath);
    $size = $exists ? filesize($fullPath) : 0;
    $url = $appUrl . '/' . $imagePath;
    
    echo "<h2>Imagen {$lang} ({$imagePath})</h2>";
    
    if ($exists) {
        echo "<p class='success'>✓ Archivo existe</p>";
        echo "<p><strong>Ruta completa:</strong> {$fullPath}</p>";
        echo "<p><strong>Tamaño:</strong> " . number_format($size / 1024, 2) . " KB</p>";
        echo "<p><strong>Permisos:</strong> " . substr(sprintf('%o', fileperms($fullPath)), -4) . "</p>";
        echo "<p><strong>Legible:</strong> " . ($readable ? "<span class='success'>✓ Sí</span>" : "<span class='error'>✗ No</span>") . "</p>";
        echo "<p><strong>URL esperada:</strong> <a href='{$url}' target='_blank'>{$url}</a></p>";
        
        if ($readable) {
            echo "<p><strong>Vista previa:</strong></p>";
            echo "<img src='/{$imagePath}' alt='Imagen {$lang}' onerror=\"this.style.border='3px solid red'; this.alt='ERROR: No se pudo cargar la imagen';\">";
        }
    } else {
        echo "<p class='error'>✗ Archivo NO existe</p>";
        echo "<p><strong>Ruta buscada:</strong> {$fullPath}</p>";
    }
    
    echo "<hr>";
}

// Verificar configuración de Laravel
echo "<h2>Configuración de Laravel</h2>";
echo "<pre>";
echo "APP_URL: " . env('APP_URL', 'No configurado') . "\n";
echo "APP_ENV: " . env('APP_ENV', 'No configurado') . "\n";
echo "ASSET_URL: " . env('ASSET_URL', 'No configurado (usará APP_URL)') . "\n";
echo "</pre>";

// Probar función asset()
if (function_exists('asset')) {
    echo "<h2>Prueba de función asset()</h2>";
    echo "<pre>";
    echo "asset('images/digital-transformation-es.png'): " . asset('images/digital-transformation-es.png') . "\n";
    echo "asset('images/digital-transformation-en.png'): " . asset('images/digital-transformation-en.png') . "\n";
    echo "</pre>";
} else {
    echo "<p class='error'>La función asset() no está disponible. Este script debe ejecutarse en el contexto de Laravel.</p>";
}

echo "<div class='info'>
    <h3>Instrucciones:</h3>
    <ol>
        <li>Verifica que los archivos existan en las rutas mostradas arriba</li>
        <li>Si no existen, súbelos a: <code>public/images/</code></li>
        <li>Verifica los permisos (deben ser 644 o 755)</li>
        <li>Verifica que APP_URL en .env sea correcto</li>
        <li>Ejecuta: <code>php artisan config:clear</code> y <code>php artisan cache:clear</code></li>
        <li>Elimina este archivo después de la verificación por seguridad</li>
    </ol>
</div>";

echo "</div></body></html>";
?>

