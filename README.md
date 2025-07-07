# MiTienda Grupo 5 UAPA - Sistema POO

Sistema de tienda en línea desarrollado con **Programación Orientada a Objetos (POO)** usando PHP, MySQL, JavaScript y Bootstrap.

## 🚀 Características

- **Arquitectura POO**: Modelos separados para Productos, Usuarios, Carrito y Órdenes
- **Base de datos MySQL**: Conexión separada del código usando patrón Singleton
- **API REST**: Endpoints para todas las operaciones
- **Autenticación**: Sistema de login/registro con roles (usuario/admin)
- **Carrito de compras**: Gestión completa del carrito con persistencia en BD
- **Panel de administración**: CRUD completo de productos
- **Responsive**: Interfaz adaptativa con Bootstrap 5

## 📋 Requisitos del Sistema

- **PHP 7.4** o superior
- **MySQL 5.7** o superior (o MariaDB)
- **Servidor web** (Apache/Nginx)
- **Extensiones PHP**:
  - PDO
  - PDO_MySQL
  - JSON

## 🛠️ Instalación

### 1. Clonar o descargar el proyecto
```bash
git clone [url-del-repositorio]
# o descargar y extraer el ZIP
```

### 2. Configurar la base de datos

#### Opción A: Usando phpMyAdmin o similar
1. Crear una base de datos llamada `tiendagrupo5`
2. Importar el archivo `database/setup.sql`

#### Opción B: Usando línea de comandos
```bash
mysql -u root -p
```
```sql
CREATE DATABASE tiendagrupo5;
USE tiendagrupo5;
SOURCE /ruta/al/proyecto/database/setup.sql;
```

### 3. Configurar conexión de base de datos
Editar el archivo `config/Database.php` y ajustar las credenciales:

```php
private $host = 'localhost';
private $username = 'root';        // Tu usuario de MySQL
private $password = '';            // Tu contraseña de MySQL
private $dbname = 'tiendagrupo5';
```

## 👤 Usuarios por Defecto

### Administrador
- **Email**: admin@tienda.com
- **Contraseña**: password
- **Panel admin**: `http://localhost/tiendagrupo5/admin`

## 📁 Estructura del Proyecto

```
tiendagrupo5/
├── config/
│   └── Database.php           # Configuración de BD (Singleton)
├── models/
│   ├── Product.php           # Modelo de Productos
│   ├── User.php              # Modelo de Usuarios
│   ├── Cart.php              # Modelo de Carrito
│   └── Order.php             # Modelo de Órdenes
├── controllers/
│   ├── ProductController.php # Controlador de Productos
│   ├── AuthController.php    # Controlador de Autenticación
│   └── CartController.php    # Controlador de Carrito
├── admin/
│   ├── index.php             # Panel de administración
│   ├── login.php             # Login de admin
│   ├── register_product.php  # Registrar producto
│   ├── edit_product.php      # Editar producto
│   └── delete_product.php    # Eliminar producto
├── database/
│   └── setup.sql             # Script de inicialización de BD
├── img/                      # Imágenes de productos
├── api.php                   # Rutas de la API REST
├── index.html                # Página principal
├── script.js                 # JavaScript con clases POO
├── styles.css                # Estilos CSS
└── README.md                 # Este archivo
```

## 🔧 API Endpoints

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión
- `POST /api/auth/register` - Registrar usuario
- `GET /api/auth/session` - Verificar sesión

### Productos
- `GET /api/products` - Obtener todos los productos
- `GET /api/products?category=electronics` - Filtrar por categoría
- `GET /api/products?q=smartphone` - Buscar productos
- `GET /api/products/show?id=1` - Obtener producto específico
- `POST /api/products` - Crear producto (admin)
- `POST /api/products/update` - Actualizar producto (admin)
- `POST /api/products/delete` - Eliminar producto (admin)

### Carrito
- `GET /api/cart` - Obtener carrito del usuario
- `POST /api/cart/add` - Agregar producto al carrito
- `POST /api/cart/update` - Actualizar cantidad
- `POST /api/cart/remove` - Eliminar producto del carrito
- `POST /api/cart/clear` - Vaciar carrito


## 📱 Funcionalidades

### Para Usuarios:
- Navegar catálogo de productos
- Filtrar por categorías
- Buscar productos
- Agregar al carrito
- Gestionar cantidades
- Completar compras

### Para Administradores:
- CRUD completo de productos
- Gestión de categorías
- Visualización de órdenes
- Control de usuarios

## 🐛 Troubleshooting

### Error de conexión a BD:
1. Verificar que MySQL esté ejecutándose
2. Comprobar credenciales en `config/Database.php`
3. Asegurar que la BD `tienda_grupo5` existe

### Problemas con sesiones:
1. Verificar permisos de escritura en directorio de sesiones
2. Comprobar configuración de PHP

### Error 404 en API:
1. Verificar que mod_rewrite esté habilitado (Apache)
2. Comprobar rutas en `api.php`

## 🤝 Contribuir

1. Fork del proyecto
2. Crear rama para nueva funcionalidad
3. Hacer commit de cambios
4. Push a la rama
5. Abrir Pull Request

## 📄 Licencia

Este proyecto es para fines educativos - Universidad Abierta Para Adultos (UAPA).

## 👥 Equipo - Grupo 5

- Fabio Muñoz - 100069637
- EDWIN MUÑOZ – 100068274 
- EDISON RAFAEL MERCEDES – 100069673 
- YERNISON NUÑEZ DE JESÚS – 100063221 
- GREGORY JOSUE NÚÑEZ PAYANO – 100063230 