<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $name = $_POST['name'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $category = $_POST['category'] ?? '';
    $image = $_POST['image'] ?? '';
    
    // Leer productos existentes
    $products = json_decode(file_get_contents('products.json'), true) ?? [];
    
    // Buscar y actualizar el producto
    foreach ($products as &$product) {
        if ($product['id'] == $id) {
            $product['name'] = $name;
            $product['price'] = $price;
            $product['category'] = $category;
            $product['image'] = $image;
            break;
        }
    }
    
    // Guardar los productos actualizados
    file_put_contents('products.json', json_encode($products, JSON_PRETTY_PRINT));
}

header('Location: index.php');
exit;
?>
