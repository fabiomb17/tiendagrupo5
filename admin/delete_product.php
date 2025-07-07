<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    
    // Leer productos existentes
    $products = json_decode(file_get_contents('products.json'), true) ?? [];
    
    // Filtrar el producto a eliminar
    $products = array_filter($products, function($product) use ($id) {
        return $product['id'] != $id;
    });
    
    // Reindexar el array para mantener la estructura JSON limpia
    $products = array_values($products);
    
    // Guardar los productos actualizados
    file_put_contents('products.json', json_encode($products, JSON_PRETTY_PRINT));
}

header('Location: index.php');
exit;
?>
