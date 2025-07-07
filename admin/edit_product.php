<?php
session_start();
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';

// Verificar autenticación y permisos
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userModel = new User();
if (!$userModel->isAdmin($_SESSION['user_id'])) {
    header('Location: ../index.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id = intval($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category = $_POST['category'] ?? '';
        $image = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validaciones
        if (!$id) {
            throw new Exception("ID de producto no válido");
        }

        if (empty($name) || empty($category) || empty($image)) {
            throw new Exception("Todos los campos obligatorios deben ser completados");
        }

        if ($price <= 0) {
            throw new Exception("El precio debe ser mayor a 0");
        }

        $allowedCategories = ['electronics', 'clothing', 'home'];
        if (!in_array($category, $allowedCategories)) {
            throw new Exception("Categoría no válida");
        }

        // Actualizar producto
        $productModel = new Product();
        
        // Verificar que el producto existe
        if (!$productModel->exists($id)) {
            throw new Exception("El producto no existe");
        }

        $updated = $productModel->update($id, $name, $price, $category, $image, $description);
        
        if ($updated) {
            $message = "Producto '{$name}' actualizado exitosamente";
            $_SESSION['success'] = $message;
            
            // Si es una solicitud AJAX, devolver JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => $message]);
                exit;
            }
        } else {
            throw new Exception("No se pudo actualizar el producto");
        }
        
    } catch (Exception $e) {
        $error = $e->getMessage();
        $_SESSION['error'] = $error;
        
        // Si es una solicitud AJAX, devolver JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $error]);
            exit;
        }
    }
}

header('Location: index.php');
exit;
?>
