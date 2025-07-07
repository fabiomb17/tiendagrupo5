#!/bin/bash

# Script para ejecutar pruebas del proyecto TiendaGrupo5

echo "==================================="
echo "  PRUEBAS TIENDAGRUPO5"
echo "==================================="

# Verificar que PHPUnit esté instalado
if [ ! -f "vendor/bin/phpunit" ]; then
    echo "❌ PHPUnit no está instalado. Ejecuta: composer install"
    exit 1
fi

echo "✅ PHPUnit encontrado"

# Crear base de datos de prueba (opcional)
echo "🔧 Configurando base de datos de prueba..."
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS tiendagrupo5_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || echo "⚠️  No se pudo crear la base de datos de prueba (continuando sin ella)"

echo ""
echo "🧪 Ejecutando todas las pruebas..."
echo "-----------------------------------"

# Ejecutar todas las pruebas
vendor/bin/phpunit --testdox

echo ""
echo "📊 Ejecutando pruebas con cobertura..."
echo "--------------------------------------"

# Ejecutar pruebas con cobertura (si está disponible Xdebug)
vendor/bin/phpunit --coverage-text --coverage-html coverage/ 2>/dev/null || echo "⚠️  Cobertura no disponible (requiere Xdebug)"

echo ""
echo "🎯 Ejecutando solo pruebas unitarias..."
echo "---------------------------------------"

vendor/bin/phpunit tests/Unit --testdox

echo ""
echo "🔗 Ejecutando solo pruebas de integración..."
echo "---------------------------------------------"

vendor/bin/phpunit tests/Integration --testdox

echo ""
echo "🌐 Ejecutando solo pruebas funcionales..."
echo "-----------------------------------------"

vendor/bin/phpunit tests/Functional --testdox

echo ""
echo "==================================="
echo "  PRUEBAS COMPLETADAS"
echo "==================================="
