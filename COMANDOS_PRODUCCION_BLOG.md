# Comandos para Producción - Botón del Blog

Después de copiar los archivos a producción, ejecuta estos comandos en orden:

## 1. Limpiar Cachés (IMPORTANTE)
```bash
php artisan optimize:clear
```
Este comando limpia todos los cachés (config, route, view, cache).

## 2. Cachear Rutas (CRÍTICO para que funcione el botón)
```bash
php artisan route:cache
```
Este comando es **esencial** porque cachea las rutas, incluyendo `blogs.show` que usa el botón.

## 3. Cachear Vistas
```bash
php artisan view:cache
```
Cachea las vistas Blade compiladas.

## 4. Cachear Configuración
```bash
php artisan config:cache
```
Cachea la configuración de la aplicación.

## 5. Verificar Storage Link
```bash
php artisan storage:link
```
Asegúrate de que el enlace simbólico de storage esté creado para que las imágenes se muestren correctamente.

## 6. Verificar Permisos (si es necesario)
```bash
chmod -R 775 storage bootstrap/cache
```

## Comandos en una sola línea (rápido)
```bash
php artisan optimize:clear && php artisan route:cache && php artisan view:cache && php artisan config:cache
```

## Si el botón aún no funciona después de estos comandos:

### Verificar que la ruta existe:
```bash
php artisan route:list | grep blogs
```
Deberías ver algo como:
```
GET|HEAD  blogs/{blog} ................ blogs.show › BlogController@show
```

### Verificar logs de errores:
```bash
tail -f storage/logs/laravel.log
```

### Limpiar y recachear todo de nuevo:
```bash
php artisan optimize:clear
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan cache:clear
php artisan route:cache
php artisan view:cache
php artisan config:cache
```

## Notas Importantes:

1. **route:cache es CRÍTICO**: Sin este comando, las rutas pueden no funcionar correctamente en producción.

2. **Siempre limpia antes de cachear**: Ejecuta `optimize:clear` antes de cachear para evitar problemas con cachés antiguos.

3. **Si cambias rutas**: Siempre ejecuta `php artisan route:clear` y luego `php artisan route:cache` después de modificar rutas.

4. **Permisos**: Asegúrate de que el usuario del servidor web tenga permisos de lectura/escritura en `storage` y `bootstrap/cache`.

## Orden Recomendado Completo:

```bash
# 1. Limpiar todo
php artisan optimize:clear

# 2. Cachear para producción
php artisan route:cache
php artisan view:cache
php artisan config:cache

# 3. Verificar storage link
php artisan storage:link

# 4. Optimizar autoloader (opcional pero recomendado)
composer dump-autoload --optimize --classmap-authoritative
```

## Verificación Final:

Después de ejecutar los comandos, verifica:
1. Que la ruta `/blogs` funcione
2. Que al hacer clic en "Read more" navegue correctamente
3. Que las imágenes de los blogs se muestren
4. Revisa los logs si hay algún error
