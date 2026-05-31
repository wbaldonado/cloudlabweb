# Solución: Slugs con Tags HTML

## Problema
Los slugs en la base de datos contienen tags HTML (ej: `<p>Test</p>`), lo que causa errores 404 y URLs inválidas.

## Solución Implementada

### 1. Modelo Blog Actualizado
**Archivo**: `app/Models/Blog.php`

**Cambios**:
- ✅ Limpia automáticamente el título de HTML antes de generar el slug
- ✅ Limpia el slug existente si contiene HTML tags
- ✅ Nuevo método estático `cleanSlug()` que elimina HTML y convierte a slug válido

### 2. Controlador Actualizado
**Archivo**: `app/Http/Controllers/BlogController.php`

**Cambios**:
- ✅ Limpia el slug recibido antes de buscar el blog
- ✅ Previene errores 404 por slugs con HTML

### 3. Comando Artisan para Corregir Slugs Existentes
**Archivo**: `app/Console/Commands/FixBlogSlugs.php`

## Comandos para Ejecutar en Producción

### Paso 1: Subir los archivos actualizados
Asegúrate de subir estos archivos:
- `app/Models/Blog.php`
- `app/Http/Controllers/BlogController.php`
- `app/Console/Commands/FixBlogSlugs.php`

### Paso 2: Verificar qué slugs necesitan corrección (modo dry-run)
```bash
php artisan blog:fix-slugs --dry-run
```

Esto mostrará qué blogs tienen slugs con HTML sin hacer cambios.

### Paso 3: Corregir los slugs existentes
```bash
php artisan blog:fix-slugs
```

Este comando:
- Encuentra todos los blogs con slugs que contienen HTML
- Los limpia automáticamente
- Asegura que los slugs sean únicos
- Muestra un resumen de los cambios

### Paso 4: Limpiar cachés
```bash
php artisan optimize:clear
php artisan route:cache
php artisan view:cache
php artisan config:cache
```

## Ejemplo de Uso del Comando

### Ver qué se corregiría (sin hacer cambios):
```bash
php artisan blog:fix-slugs --dry-run
```

**Salida esperada**:
```
Scanning blogs for slugs with HTML tags...

Found 2 blog(s) with problematic slugs:

ID: 1 - Test Blog
  Old: <p>Test</p>
  New: test

ID: 5 - Another Blog
  Old: <h1>Title</h1>
  New: title

Dry run mode - no changes made. Remove --dry-run to apply fixes.
```

### Aplicar las correcciones:
```bash
php artisan blog:fix-slugs
```

**Salida esperada**:
```
Scanning blogs for slugs with HTML tags...

Found 2 blog(s) with problematic slugs:

ID: 1 - Test Blog
  Old: <p>Test</p>
  New: test

ID: 5 - Another Blog
  Old: <h1>Title</h1>
  New: title

Do you want to fix these slugs? (yes/no) [yes]:
> yes

Fixing slugs...
✓ Fixed blog ID 1: '<p>Test</p>' → 'test'
✓ Fixed blog ID 5: '<h1>Title</h1>' → 'title'

✓ Successfully fixed 2 blog slug(s)!
```

## Prevención Futura

Los cambios en el modelo `Blog` aseguran que:

1. **Al crear un blog**: El slug se genera automáticamente limpiando el título de HTML
2. **Al actualizar un blog**: Si el slug se modifica, se limpia automáticamente
3. **Al guardar**: El slug siempre se valida y limpia antes de guardar

## Verificación

Después de ejecutar el comando:

1. **Verifica los slugs en la base de datos**:
   ```bash
   php artisan tinker
   ```
   ```php
   \App\Models\Blog::all(['id', 'title', 'slug'])->each(function($b) {
       echo $b->id . ": " . $b->title . " -> " . $b->slug . "\n";
   });
   ```

2. **Prueba las URLs**:
   - Los slugs deberían ser solo texto, números y guiones
   - No deberían contener `<`, `>`, o caracteres especiales
   - Las URLs deberían funcionar correctamente

## Notas Importantes

- ✅ El comando **no elimina** blogs, solo corrige los slugs
- ✅ Los slugs se mantienen únicos automáticamente
- ✅ Si un slug limpio ya existe, se agrega un número (ej: `test-1`, `test-2`)
- ✅ El comando es **seguro** y muestra un preview antes de hacer cambios

## Solución de Problemas

### Si el comando no encuentra el comando:
```bash
php artisan list | grep blog
```

Si no aparece, asegúrate de que el archivo `app/Console/Commands/FixBlogSlugs.php` existe.

### Si hay errores al ejecutar:
1. Verifica que Laravel puede acceder a la base de datos
2. Revisa los logs: `tail -f storage/logs/laravel.log`
3. Asegúrate de tener permisos de escritura en la base de datos

### Si algunos slugs siguen con HTML después de corregir:
1. Verifica que el modelo `Blog` tiene el método `cleanSlug()` estático
2. Ejecuta el comando nuevamente
3. Si persiste, verifica manualmente en la base de datos
