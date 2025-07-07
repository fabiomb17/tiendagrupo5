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
        $name = trim($_POST['name'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category = $_POST['category'] ?? '';
        $image = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // Validaciones
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

        // Crear producto
        $productModel = new Product();
        $productId = $productModel->create($name, $price, $category, $image, $description);

        $_SESSION['success'] = "Producto '{$name}' registrado exitosamente con ID: {$productId}";
        
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
}

header('Location: index.php');
exit;
?>
