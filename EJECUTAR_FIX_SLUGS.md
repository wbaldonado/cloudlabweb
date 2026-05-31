# Formas de Ejecutar la Corrección de Slugs

## Opción 1: Script PHP Simple (RECOMENDADO) ⭐

### Modo Interactivo (pregunta antes de corregir):
```bash
php fix-blog-slugs.php
```

### Modo Automático (corrige sin preguntar):
```bash
php fix-blog-slugs-auto.php
```

**Ventajas:**
- ✅ No requiere Tinker
- ✅ No requiere comandos artisan
- ✅ Funciona directamente con PHP
- ✅ Fácil de ejecutar en PuTTY

**Pasos:**
1. Sube el archivo `fix-blog-slugs.php` o `fix-blog-slugs-auto.php` al servidor
2. Navega al directorio del proyecto: `cd /ruta/a/tu/proyecto`
3. Ejecuta: `php fix-blog-slugs.php`

---

## Opción 2: Comando Artisan (si funciona)

Primero regenera el autoloader:
```bash
composer dump-autoload
php artisan optimize:clear
```

Luego ejecuta:
```bash
php artisan blog:fix-slugs --dry-run
php artisan blog:fix-slugs
```

---

## Opción 3: Tinker (interactivo)

```bash
php artisan tinker
```

Luego pega el código (clic derecho en PuTTY para pegar).

---

## Opción 4: SQL Directo (solo si conoces SQL)

**⚠️ ADVERTENCIA: Haz backup de la base de datos primero**

```sql
UPDATE blogs 
SET slug = LOWER(REGEXP_REPLACE(REGEXP_REPLACE(slug, '<[^>]+>', ''), '[^a-z0-9-]', '-'))
WHERE slug REGEXP '<[^>]+>';
```

**Nota:** Esta opción es más avanzada y puede variar según tu base de datos (MySQL/MariaDB/PostgreSQL).

---

## Opción 5: Ruta Web Temporal (solo para desarrollo)

Puedes crear una ruta temporal en `routes/web.php`:

```php
Route::get('/fix-slugs-temp', function() {
    // ... código de corrección ...
    return 'Slugs corregidos';
});
```

Luego visita: `https://tudominio.com/fix-slugs-temp`

**⚠️ IMPORTANTE:** Elimina esta ruta después de usarla por seguridad.

---

## Recomendación

**Usa la Opción 1** (`fix-blog-slugs-auto.php`) porque:
- ✅ Es la más simple
- ✅ No requiere configuración adicional
- ✅ Funciona en cualquier servidor con PHP
- ✅ Fácil de ejecutar en PuTTY

### Pasos en PuTTY:

```bash
# 1. Navegar al proyecto
cd /ruta/a/tu/proyecto

# 2. Verificar que el archivo existe
ls fix-blog-slugs-auto.php

# 3. Ejecutar
php fix-blog-slugs-auto.php
```

¡Listo! El script corregirá todos los slugs automáticamente.
