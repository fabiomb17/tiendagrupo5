<?php
// Configurar headers CORS y JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Incluir controladores
require_once __DIR__ . '/controllers/ProductController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/CartController.php';
require_once __DIR__ . '/controllers/UserController.php';

// Obtener la ruta solicitada
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);
$path = str_replace('/tiendagrupo5/api', '', $path);

// Remover parámetros de consulta para el enrutamiento
$route = strtok($path, '?');

try {
    // Enrutamiento
    switch ($route) {
        // ===== RUTAS DE AUTENTICACIÓN =====
        case '/auth/login':
            $controller = new AuthController();
            $controller->login();
            break;
            
        case '/auth/logout':
            $controller = new AuthController();
            $controller->logout();
            break;
            
        case '/auth/register':
            $controller = new AuthController();
            $controller->register();
            break;
            
        case '/auth/session':
            $controller = new AuthController();
            $controller->checkSession();
            break;
            
        case '/auth/change-password':
            $controller = new AuthController();
            $controller->changePassword();
            break;
            
        case '/auth/update-profile':
            $controller = new AuthController();
            $controller->updateProfile();
            break;

        // ===== RUTAS DE PRODUCTOS =====
        case '/products':
            $controller = new ProductController();
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                if (isset($_GET['category'])) {
                    $controller->getByCategory();
                } elseif (isset($_GET['q'])) {
                    $controller->search();
                } else {
                    $controller->index();
                }
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->create();
            }
            break;
            
        case '/products/show':
            $controller = new ProductController();
            $controller->show();
            break;
            
        case '/products/update':
            $controller = new ProductController();
            $controller->update();
            break;
            
        case '/products/delete':
            $controller = new ProductController();
            $controller->delete();
            break;
            
        case '/products/categories':
            $controller = new ProductController();
            $controller->getCategories();
            break;

        // ===== RUTAS DEL CARRITO =====
        case '/cart':
            $controller = new CartController();
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->getItems();
            }
            break;
            
        case '/cart/add':
            $controller = new CartController();
            $controller->addItem();
            break;
            
        case '/cart/update':
            $controller = new CartController();
            $controller->updateQuantity();
            break;
            
        case '/cart/remove':
            $controller = new CartController();
            $controller->removeItem();
            break;
            
        case '/cart/clear':
            $controller = new CartController();
            $controller->clearCart();
            break;
            
        case '/cart/summary':
            $controller = new CartController();
            $controller->getSummary();
            break;

        // ===== RUTAS DE USUARIOS =====
        case '/users':
            $controller = new UserController();
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $controller->index();
            } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->create();
            }
            break;
            
        case '/users/show':
            $controller = new UserController();
            $controller->show();
            break;
            
        case '/users/update':
            $controller = new UserController();
            $controller->update();
            break;
            
        case '/users/delete':
            $controller = new UserController();
            $controller->delete();
            break;
            
        case '/users/toggle-status':
            $controller = new UserController();
            $controller->toggleStatus();
            break;
            
        case '/users/stats':
            $controller = new UserController();
            $controller->getStats();
            break;

        // ===== RUTA POR DEFECTO =====
        default:
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Endpoint no encontrado',
                'available_endpoints' => [
                    'auth' => [
                        'POST /auth/login',
                        'POST /auth/logout',
                        'POST /auth/register',
                        'GET /auth/session',
                        'POST /auth/change-password',
                        'POST /auth/update-profile'
                    ],
                    'products' => [
                        'GET /products',
                        'GET /products?category={category}',
                        'GET /products?q={search_term}',
                        'GET /products/show?id={id}',
                        'POST /products',
                        'POST /products/update',
                        'POST /products/delete',
                        'GET /products/categories'
                    ],
                    'cart' => [
                        'GET /cart',
                        'POST /cart/add',
                        'POST /cart/update',
                        'POST /cart/remove',
                        'POST /cart/clear',
                        'GET /cart/summary'
                    ],
                    'users' => [
                        'GET /users',
                        'GET /users/show?id={id}',
                        'POST /users',
                        'POST /users/update',
                        'POST /users/delete',
                        'POST /users/toggle-status',
                        'GET /users/stats'
                    ],
                    'users' => [
                        'GET /users',
                        'POST /users',
                        'GET /users/show?id={id}',
                        'POST /users/update',
                        'POST /users/delete',
                        'POST /users/toggle-status',
                        'GET /users/stats'
                    ]
                ]
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?>
