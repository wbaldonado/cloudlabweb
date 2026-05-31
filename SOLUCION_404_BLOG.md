# Solución Error 404 en Botón del Blog

## Problema
Después de copiar archivos a producción, el botón "Read more" genera un error 404.

## Cambios Realizados

### 1. Controlador Actualizado
**Archivo**: `app/Http/Controllers/BlogController.php`
- Cambiado el método `show()` para aceptar el slug directamente
- Agregada búsqueda explícita por slug (case-insensitive)
- Mejorado el logging para debugging
- Agregado fallback para buscar por ID si el slug es numérico

### 2. Vistas Actualizadas
**Archivos actualizados**:
- `resources/views/home.blade.php` - Usa `$blog->slug` explícitamente
- `resources/views/blogs/index.blade.php` - Usa `$blog->slug` explícitamente  
- `resources/views/blogs/show.blade.php` - Usa `$recentBlog->slug` explícitamente

## Comandos para Ejecutar en Producción

### Paso 1: Subir los archivos actualizados
Asegúrate de subir estos archivos a producción:
- `app/Http/Controllers/BlogController.php`
- `resources/views/home.blade.php`
- `resources/views/blogs/index.blade.php`
- `resources/views/blogs/show.blade.php`

### Paso 2: Limpiar TODOS los cachés
```bash
php artisan optimize:clear
```

### Paso 3: Limpiar cachés individuales (por si acaso)
```bash
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### Paso 4: Recachear para producción
```bash
php artisan route:cache
php artisan view:cache
php artisan config:cache
```

### Paso 5: Verificar que las rutas funcionan
```bash
php artisan route:list | grep blogs
```

Deberías ver:
```
GET|HEAD  blogs/{blog} ................ blogs.show › BlogController@show
```

### Paso 6: Verificar que los blogs tienen slug
```bash
php artisan tinker
```

Luego ejecuta:
```php
\App\Models\Blog::where('is_published', true)->get(['id', 'title', 'slug'])->each(function($b) {
    echo $b->id . ": " . $b->title . " -> " . $b->slug . "\n";
});
```

Asegúrate de que todos los blogs publicados tengan un slug válido.

## Comandos en una sola línea (rápido)

```bash
php artisan optimize:clear && php artisan route:cache && php artisan view:cache && php artisan config:cache
```

## Verificación

1. **Verifica la URL generada**: 
   - Abre la página de inicio en producción
   - Inspecciona el botón "Read more" 
   - La URL debería ser: `/blogs/nombre-del-slug-del-blog`
   - NO debería ser: `/blogs/1` o `/blogs/{blog}`

2. **Prueba directamente**:
   - Obtén el slug de un blog desde la base de datos
   - Accede directamente a: `https://tudominio.com/blogs/nombre-del-slug`
   - Debería funcionar sin error 404

3. **Revisa los logs si sigue fallando**:
   ```bash
   tail -f storage/logs/laravel.log
   ```
   Luego intenta hacer clic en el botón y revisa qué error aparece en los logs.

## Solución de Problemas

### Si sigue dando 404:

1. **Verifica que el slug existe en la base de datos**:
   ```bash
   php artisan tinker
   ```
   ```php
   $blog = \App\Models\Blog::where('is_published', true)->first();
   echo "Slug: " . $blog->slug . "\n";
   echo "URL esperada: /blogs/" . $blog->slug . "\n";
   ```

2. **Verifica que la ruta está cacheada correctamente**:
   ```bash
   php artisan route:clear
   php artisan route:cache
   ```

3. **Verifica permisos de archivos**:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

4. **Revisa el archivo .htaccess** (si usas Apache):
   Asegúrate de que las reglas de rewrite estén correctas para Laravel.

5. **Verifica la configuración de APP_URL en .env**:
   ```env
   APP_URL=https://tudominio.com
   ```

## Notas Importantes

- **El cambio principal**: Ahora usamos `$blog->slug` explícitamente en lugar de pasar el objeto `$blog` completo. Esto evita problemas con route model binding en producción.

- **Case-insensitive**: El controlador ahora busca el slug sin importar mayúsculas/minúsculas, lo que hace más robusto el sistema.

- **Logging mejorado**: Si hay un error, los logs ahora incluyen el slug solicitado y los slugs disponibles, lo que facilita el debugging.

## Archivos Modificados (Resumen)

1. ✅ `app/Http/Controllers/BlogController.php` - Método `show()` actualizado
2. ✅ `resources/views/home.blade.php` - Usa `$blog->slug`
3. ✅ `resources/views/blogs/index.blade.php` - Usa `$blog->slug`
4. ✅ `resources/views/blogs/show.blade.php` - Usa `$recentBlog->slug`

Después de subir estos archivos y ejecutar los comandos, el botón debería funcionar correctamente.
