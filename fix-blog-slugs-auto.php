<?php

/**
 * Script para corregir slugs de blogs con HTML tags (modo automático)
 * 
 * Uso: php fix-blog-slugs-auto.php
 * 
 * Este script corrige automáticamente sin preguntar confirmación
 * 
 * IMPORTANTE: Ejecuta este archivo desde el directorio raíz del proyecto Laravel
 * (donde está el archivo artisan)
 */

// Verificar que estamos en el directorio correcto
if (!file_exists(__DIR__.'/vendor/autoload.php')) {
    die("ERROR: No se encontró vendor/autoload.php\n");
}

if (!file_exists(__DIR__.'/bootstrap/app.php')) {
    die("ERROR: No se encontró bootstrap/app.php\n");
}

// Cargar Laravel
require __DIR__.'/vendor/autoload.php';

try {
    $app = require_once __DIR__.'/bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
} catch (Exception $e) {
    die("ERROR al cargar Laravel: " . $e->getMessage() . "\n");
}

use App\Models\Blog;
use Illuminate\Support\Str;

echo "========================================\n";
echo "  CORRECCIÓN DE SLUGS DE BLOGS\n";
echo "========================================\n\n";

/**
 * Función para limpiar slug de HTML tags
 */
function cleanSlug($slug) {
    if (empty($slug)) {
        return $slug;
    }
    
    // Remover HTML tags
    $slug = strip_tags($slug);
    
    // Decodificar entidades HTML
    $slug = html_entity_decode($slug, ENT_QUOTES, 'UTF-8');
    
    // Convertir a slug válido
    return Str::slug($slug);
}

// Obtener todos los blogs
echo "Buscando blogs...\n";
$blogs = Blog::all();
$totalBlogs = $blogs->count();
echo "Encontrados {$totalBlogs} blog(s)\n\n";

$fixed = 0;
$skipped = 0;
$errors = [];

echo "Analizando slugs...\n\n";

foreach ($blogs as $blog) {
    $originalSlug = $blog->slug;
    $cleanSlug = cleanSlug($originalSlug);
    
    // Verificar si necesita corrección
    $hasHtml = ($originalSlug !== strip_tags($originalSlug));
    $needsFix = ($originalSlug !== $cleanSlug) || $hasHtml;
    
    if ($needsFix) {
        // Generar nuevo slug limpio
        $baseSlug = $cleanSlug ?: cleanSlug($blog->title);
        
        // Si el slug está vacío después de limpiar, usar el título
        if (empty($baseSlug)) {
            $baseSlug = cleanSlug($blog->title);
        }
        
        // Si aún está vacío, usar un slug por defecto
        if (empty($baseSlug)) {
            $baseSlug = 'blog-' . $blog->id;
        }
        
        $newSlug = $baseSlug;
        $counter = 1;
        
        // Asegurar slug único
        while (Blog::where('slug', $newSlug)->where('id', '!=', $blog->id)->exists()) {
            $newSlug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        try {
            echo "ID: {$blog->id} - {$blog->title}\n";
            echo "  Old: " . ($originalSlug ?: '(vacío)') . "\n";
            echo "  New: {$newSlug}\n";
            
            $blog->slug = $newSlug;
            $blog->save();
            
            echo "  ✓ Corregido!\n\n";
            $fixed++;
        } catch (Exception $e) {
            $errors[] = [
                'id' => $blog->id,
                'title' => $blog->title,
                'error' => $e->getMessage()
            ];
            echo "  ✗ Error: " . $e->getMessage() . "\n\n";
        }
    } else {
        $skipped++;
    }
}

// Resumen
echo "========================================\n";
echo "  RESUMEN\n";
echo "========================================\n";
echo "Total de blogs: {$totalBlogs}\n";
echo "Corregidos: {$fixed}\n";
echo "Sin cambios: {$skipped}\n";

if (!empty($errors)) {
    echo "Errores: " . count($errors) . "\n";
    foreach ($errors as $error) {
        echo "  - Blog ID {$error['id']}: {$error['error']}\n";
    }
}

if ($fixed === 0 && empty($errors)) {
    echo "\n✓ ¡Todos los slugs están limpios!\n";
} elseif ($fixed > 0) {
    echo "\n✓ ¡Se corrigieron {$fixed} slug(s) exitosamente!\n";
}

echo "\n========================================\n";
echo "Proceso completado.\n";
