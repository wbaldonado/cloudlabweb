# Solución: Error "no command blog namespace"

## Problema
Al ejecutar `php artisan blog:fix-slugs` aparece el error:
```
There are no commands defined in the "blog" namespace.
```

## Solución

### Paso 1: Regenerar el autoloader de Composer
```bash
composer dump-autoload
```

Este comando regenera el autoloader de Composer para que detecte el nuevo comando.

### Paso 2: Limpiar el cache de comandos de Laravel
```bash
php artisan clear-compiled
php artisan optimize:clear
```

### Paso 3: Verificar que el comando existe
```bash
php artisan list | grep blog
```

O ver todos los comandos disponibles:
```bash
php artisan list
```

### Paso 4: Si aún no aparece, verificar el archivo
Asegúrate de que el archivo existe en:
```
app/Console/Commands/FixBlogSlugs.php
```

### Paso 5: Ejecutar el comando
Una vez que aparezca en la lista, ejecuta:
```bash
php artisan blog:fix-slugs --dry-run
php artisan blog:fix-slugs
```

## Solución Alternativa: Usar Tinker

Si el comando sigue sin funcionar, puedes corregir los slugs manualmente usando Tinker:

```bash
php artisan tinker
```

Luego ejecuta este código:

```php
use App\Models\Blog;
use Illuminate\Support\Str;

// Función para limpiar slug
function cleanSlug($slug) {
    if (empty($slug)) return $slug;
    $slug = strip_tags($slug);
    $slug = html_entity_decode($slug, ENT_QUOTES, 'UTF-8');
    return Str::slug($slug);
}

// Encontrar blogs con slugs problemáticos
$blogs = Blog::all();
$fixed = 0;

foreach ($blogs as $blog) {
    $originalSlug = $blog->slug;
    $cleanSlug = cleanSlug($originalSlug);
    
    // Verificar si necesita corrección
    if ($originalSlug !== $cleanSlug || $originalSlug !== strip_tags($originalSlug)) {
        echo "ID: {$blog->id} - {$blog->title}\n";
        echo "  Old: {$originalSlug}\n";
        echo "  New: {$cleanSlug}\n";
        
        // Generar slug único si es necesario
        $baseSlug = $cleanSlug ?: cleanSlug($blog->title);
        $newSlug = $baseSlug;
        $counter = 1;
        
        while (Blog::where('slug', $newSlug)->where('id', '!=', $blog->id)->exists()) {
            $newSlug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        // Actualizar el slug
        $blog->slug = $newSlug;
        $blog->save();
        
        echo "  ✓ Fixed: '{$originalSlug}' → '{$newSlug}'\n\n";
        $fixed++;
    }
}

echo "✓ Successfully fixed {$fixed} blog slug(s)!\n";
exit
```

## Comandos Completos (en orden)

```bash
# 1. Regenerar autoloader
composer dump-autoload

# 2. Limpiar caches
php artisan clear-compiled
php artisan optimize:clear

# 3. Verificar comando
php artisan list | grep blog

# 4. Ejecutar comando (si aparece)
php artisan blog:fix-slugs --dry-run
php artisan blog:fix-slugs

# 5. Limpiar y recachear para producción
php artisan route:cache
php artisan view:cache
php artisan config:cache
```

## Verificación

Después de ejecutar los comandos, verifica:

1. **Que el comando aparece en la lista**:
   ```bash
   php artisan list | grep blog
   ```
   Deberías ver: `blog:fix-slugs`

2. **Que los slugs están limpios**:
   ```bash
   php artisan tinker
   ```
   ```php
   \App\Models\Blog::all(['id', 'title', 'slug'])->each(function($b) {
       echo $b->id . ": " . $b->title . " -> " . $b->slug . "\n";
   });
   ```

## Notas

- El comando debería detectarse automáticamente si está en `app/Console/Commands/`
- Si no se detecta, `composer dump-autoload` suele solucionarlo
- En producción, asegúrate de ejecutar `composer dump-autoload --optimize`
