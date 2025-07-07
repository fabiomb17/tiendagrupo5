# Pruebas TiendaGrupo5

Este directorio contiene todas las pruebas automatizadas para el proyecto.

## Estructura de Pruebas

```
tests/
├── Unit/                    # Pruebas unitarias (modelos individuales)
│   ├── UserTest.php        # Pruebas del modelo User
│   ├── ProductTest.php     # Pruebas del modelo Product
│   └── CartTest.php        # Pruebas del modelo Cart
├── Integration/            # Pruebas de integración (controladores)
│   ├── ProductControllerTest.php
│   └── AuthControllerTest.php
├── Functional/             # Pruebas funcionales (flujos completos)
│   ├── AdminPanelTest.php
│   ├── ShoppingCartTest.php
│   └── ApiEndpointsTest.php
└── Support/                # Clases de apoyo
    ├── BaseTestCase.php
    └── DatabaseTestHelper.php
```

## Tipos de Pruebas

### 🔬 **Pruebas Unitarias** (`tests/Unit/`)
Prueban componentes individuales de forma aislada:
- ✅ Validación de datos
- ✅ Lógica de negocio
- ✅ Métodos de modelos
- ✅ Cálculos y transformaciones

### 🔗 **Pruebas de Integración** (`tests/Integration/`)
Prueban la interacción entre componentes:
- ✅ Controladores y modelos
- ✅ API endpoints
- ✅ Respuestas JSON
- ✅ Manejo de errores

### 🌐 **Pruebas Funcionales** (`tests/Functional/`)
Prueban flujos completos del usuario:
- ✅ Panel de administración
- ✅ Carrito de compras
- ✅ Autenticación
- ✅ Gestión de productos y usuarios

## Instalación

1. **Instalar dependencias:**
   ```bash
   composer install
   ```

2. **Configurar base de datos de prueba (opcional):**
   ```sql
   CREATE DATABASE tiendagrupo5_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

## Ejecución de Pruebas

### 🚀 **Ejecutar todas las pruebas:**
```bash
# Linux/Mac
./run-tests.sh

# Windows
run-tests.bat

# O directamente con PHPUnit
vendor/bin/phpunit
```

### 🎯 **Ejecutar pruebas específicas:**

```bash
# Solo pruebas unitarias
vendor/bin/phpunit tests/Unit

# Solo pruebas de integración
vendor/bin/phpunit tests/Integration

# Solo pruebas funcionales
vendor/bin/phpunit tests/Functional

# Una prueba específica
vendor/bin/phpunit tests/Unit/UserTest.php
```

### 📊 **Generar reporte de cobertura:**
```bash
vendor/bin/phpunit --coverage-html coverage/
```

## Configuración

### Variables de Entorno para Pruebas
El archivo `phpunit.xml` configura automáticamente:
- `DB_HOST=localhost`
- `DB_NAME=tiendagrupo5_test`
- `DB_USER=root`
- `DB_PASS=root`

### Base de Datos de Prueba
- Las pruebas pueden ejecutarse sin base de datos (usando mocks)
- Para pruebas completas, crea una base de datos separada: `tiendagrupo5_test`
- Los datos de prueba se limpian automáticamente

## Resultados de Ejemplo

```
PHPUnit 11.5.0 by Sebastian Bergmann and contributors.

Unit Tests
 ✔ User can be created
 ✔ User validation
 ✔ Is admin method
 ✔ Product can be created
 ✔ Product validation
 ✔ Cart can add items

Integration Tests
 ✔ Index returns products
 ✔ Login validation
 ✔ Json response format

Functional Tests
 ✔ Admin can access admin panel
 ✔ User can add product to cart
 ✔ Api returns json response

Time: 00:00.123, Memory: 10.00 MB

OK (15 tests, 45 assertions)
```

## Buenas Prácticas

### ✅ **Qué probar:**
- Validaciones de entrada
- Lógica de negocio crítica
- Cálculos importantes
- Flujos de usuario principales
- Manejo de errores

### ❌ **Qué NO probar:**
- Getters/setters simples
- Configuración de frameworks
- Librerías externas
- Código trivial

### 📝 **Convenciones:**
- Nombres descriptivos: `testUserCanLoginWithValidCredentials()`
- Un concepto por prueba
- Arrange-Act-Assert pattern
- Cleanup automático

## Herramientas Utilizadas

- **PHPUnit 11.5**: Framework principal de pruebas
- **Faker**: Generación de datos de prueba (opcional)
- **Mocks**: Simulación de dependencias
- **Database Helper**: Gestión de datos de prueba

## Integración Continua

Las pruebas están configuradas para ejecutarse automáticamente en:
- ✅ Commits locales (opcional)
- ✅ Pull requests
- ✅ Despliegues

## Troubleshooting

### ❌ **Error: "Class not found"**
```bash
composer dump-autoload
```

### ❌ **Error: "Database connection failed"**
- Verifica las credenciales en `phpunit.xml`
- Las pruebas funcionan sin BD usando mocks

### ❌ **Error: "Permission denied"**
```bash
chmod +x run-tests.sh
```

## Métricas de Calidad

Objetivos de cobertura:
- **Modelos**: >90%
- **Controladores**: >80%
- **General**: >75%

