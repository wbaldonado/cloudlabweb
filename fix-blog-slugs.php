<?php

/**
 * Script para corregir slugs de blogs con HTML tags
 * 
 * Uso: php fix-blog-slugs.php
 * 
 * Ejecuta este archivo desde el directorio raíz del proyecto Laravel
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;
use Illuminate\Support\Str;

echo "=== Corrigiendo slugs de blogs ===\n\n";

function cleanSlug($slug) {
    if (empty($slug)) return $slug;
    $slug = strip_tags($slug);
    $slug = html_entity_decode($slug, ENT_QUOTES, 'UTF-8');
    return Str::slug($slug);
}

$blogs = Blog::all();
$fixed = 0;
$needsFix = [];

// Primero, identificar qué necesita corrección
foreach ($blogs as $blog) {
    $originalSlug = $blog->slug;
    $cleanSlug = cleanSlug($originalSlug);
    
    if ($originalSlug !== $cleanSlug || $originalSlug !== strip_tags($originalSlug)) {
        $needsFix[] = [
            'blog' => $blog,
            'old_slug' => $originalSlug,
            'new_slug' => $cleanSlug ?: cleanSlug($blog->title),
        ];
    }
}

if (empty($needsFix)) {
    echo "✓ Todos los slugs están limpios!\n";
    exit(0);
}

echo "Encontrados " . count($needsFix) . " blog(s) con slugs problemáticos:\n\n";

foreach ($needsFix as $item) {
    echo "ID: {$item['blog']->id} - {$item['blog']->title}\n";
    echo "  Old: {$item['old_slug']}\n";
    echo "  New: {$item['new_slug']}\n\n";
}

echo "¿Deseas corregir estos slugs? (s/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$confirm = trim(strtolower($line));
fclose($handle);

if ($confirm !== 's' && $confirm !== 'y' && $confirm !== 'si' && $confirm !== 'yes') {
    echo "Operación cancelada.\n";
    exit(0);
}

echo "\nCorrigiendo slugs...\n\n";

foreach ($needsFix as $item) {
    $blog = $item['blog'];
    $newSlug = $item['new_slug'];
    
    // Asegurar slug único
    $baseSlug = $newSlug;
    $counter = 1;
    while (Blog::where('slug', $newSlug)->where('id', '!=', $blog->id)->exists()) {
        $newSlug = $baseSlug . '-' . $counter;
        $counter++;
    }
    
    $oldSlug = $blog->slug;
    $blog->slug = $newSlug;
    $blog->save();
    
    echo "✓ Corregido blog ID {$blog->id}: '{$oldSlug}' → '{$newSlug}'\n";
    $fixed++;
}

echo "\n✓ ¡Se corrigieron {$fixed} slug(s) exitosamente!\n";
