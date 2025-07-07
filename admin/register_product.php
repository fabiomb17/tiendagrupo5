<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? 0;
    $category = $_POST['category'] ?? '';
    $image = $_POST['image'] ?? '';
    $products = json_decode(file_get_contents('products.json'), true) ?? [];
    $id = count($products) > 0 ? end($products)['id'] + 1 : 1;
    $products[] = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'category' => $category,
        'image' => $image
    ];
    file_put_contents('products.json', json_encode($products, JSON_PRETTY_PRINT));
}
header('Location: index.php');
exit;
