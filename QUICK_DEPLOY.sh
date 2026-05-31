#!/bin/bash

# Script rápido de despliegue para CloudLab Latam
# Uso: ./QUICK_DEPLOY.sh

set -e  # Detener en caso de error

echo "🚀 Iniciando despliegue de CloudLab Latam..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Función para imprimir mensajes
print_success() {
    echo -e "${GREEN}✓${NC} $1"
}

print_error() {
    echo -e "${RED}✗${NC} $1"
}

print_info() {
    echo -e "${YELLOW}ℹ${NC} $1"
}

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    print_error "No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz de Laravel."
    exit 1
fi

print_success "Directorio correcto detectado"

# 1. Instalar dependencias de Composer
print_info "Instalando dependencias de Composer..."
composer install --no-dev --optimize-autoloader
print_success "Dependencias de Composer instaladas"

# 2. Instalar dependencias de NPM
print_info "Instalando dependencias de NPM..."
npm install
print_success "Dependencias de NPM instaladas"

# 3. Compilar assets
print_info "Compilando assets para producción..."
npm run build
print_success "Assets compilados"

# 4. Verificar archivo .env
if [ ! -f ".env" ]; then
    print_error "Archivo .env no encontrado. Por favor créalo antes de continuar."
    exit 1
fi
print_success "Archivo .env encontrado"

# 5. Generar APP_KEY si no existe
if ! grep -q "APP_KEY=base64:" .env; then
    print_info "Generando APP_KEY..."
    php artisan key:generate
    print_success "APP_KEY generado"
else
    print_success "APP_KEY ya existe"
fi

# 6. Crear directorios necesarios
print_info "Creando directorios necesarios..."
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
mkdir -p bootstrap/cache
print_success "Directorios creados"

# 7. Configurar permisos
print_info "Configurando permisos..."
chmod -R 775 storage bootstrap/cache
print_success "Permisos configurados"

# 8. Ejecutar migraciones
read -p "¿Ejecutar migraciones de base de datos? (s/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Ss]$ ]]; then
    print_info "Ejecutando migraciones..."
    php artisan migrate --force
    print_success "Migraciones ejecutadas"
else
    print_info "Migraciones omitidas"
fi

# 9. Limpiar cachés
print_info "Limpiando cachés..."
php artisan optimize:clear
print_success "Cachés limpiadas"

# 10. Optimizar para producción
print_info "Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
print_success "Optimización completada"

# 11. Optimizar autoloader
print_info "Optimizando autoloader de Composer..."
composer dump-autoload --optimize --classmap-authoritative
print_success "Autoloader optimizado"

# 12. Verificar archivos importantes
print_info "Verificando archivos importantes..."
if [ -f "public/build/manifest.json" ]; then
    print_success "Assets compilados encontrados"
else
    print_error "Assets compilados no encontrados. Ejecuta 'npm run build'"
fi

if [ -d "storage/logs" ]; then
    print_success "Directorio de logs existe"
else
    print_error "Directorio de logs no existe"
fi

# Resumen
echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${GREEN}✓ Despliegue completado exitosamente${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "Próximos pasos:"
echo "1. Verifica que las imágenes estén en public/images/"
echo "2. Verifica la configuración de .env (especialmente DB y MAIL)"
echo "3. Prueba la aplicación en el navegador"
echo "4. Revisa los logs si hay problemas: tail -f storage/logs/laravel.log"
echo ""

