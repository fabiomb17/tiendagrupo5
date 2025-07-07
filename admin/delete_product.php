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
        
        if (!$id) {
            throw new Exception("ID de producto no válido");
        }

        $productModel = new Product();
        
        // Verificar que el producto existe
        $product = $productModel->getById($id);
        if (!$product) {
            throw new Exception("El producto no existe");
        }

        // Eliminar producto (soft delete)
        $deleted = $productModel->delete($id);
        
        if ($deleted) {
            $_SESSION['success'] = "Producto '{$product['name']}' eliminado exitosamente";
        } else {
            throw new Exception("No se pudo eliminar el producto");
        }
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: index.php');
exit;
?>
