@echo off
REM Script para ejecutar pruebas del proyecto TiendaGrupo5 en Windows

echo ===================================
echo   PRUEBAS TIENDAGRUPO5
echo ===================================

REM Verificar que PHPUnit esté instalado
if not exist "vendor\bin\phpunit.bat" (
    echo ❌ PHPUnit no está instalado. Ejecuta: composer install
    exit /b 1
)

echo ✅ PHPUnit encontrado

REM Crear base de datos de prueba (opcional)
echo 🔧 Configurando base de datos de prueba...
mysql -u root -proot -e "CREATE DATABASE IF NOT EXISTS tiendagrupo5_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul || echo ⚠️  No se pudo crear la base de datos de prueba (continuando sin ella)

echo.
echo 🧪 Ejecutando todas las pruebas...
echo -----------------------------------

REM Ejecutar todas las pruebas
vendor\bin\phpunit --testdox

echo.
echo 📊 Ejecutando pruebas con cobertura...
echo --------------------------------------

REM Ejecutar pruebas con cobertura (si está disponible Xdebug)
vendor\bin\phpunit --coverage-text --coverage-html coverage\ 2>nul || echo ⚠️  Cobertura no disponible (requiere Xdebug)

echo.
echo 🎯 Ejecutando solo pruebas unitarias...
echo ---------------------------------------

vendor\bin\phpunit tests\Unit --testdox

echo.
echo 🔗 Ejecutando solo pruebas de integración...
echo ---------------------------------------------

vendor\bin\phpunit tests\Integration --testdox

echo.
echo 🌐 Ejecutando solo pruebas funcionales...
echo -----------------------------------------

vendor\bin\phpunit tests\Functional --testdox

echo.
echo ===================================
echo   PRUEBAS COMPLETADAS
echo ===================================

pause
