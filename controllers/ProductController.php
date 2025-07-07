<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    // Obtener todos los productos
    public function index() {
        try {
            $products = $this->productModel->getAll();
            return $this->jsonResponse(['success' => true, 'data' => $products]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Obtener productos por categoría
    public function getByCategory() {
        $category = $_GET['category'] ?? '';
        
        if (empty($category)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Categoría requerida'], 400);
        }

        try {
            if ($category === 'all') {
                $products = $this->productModel->getAll();
            } else {
                $products = $this->productModel->getByCategory($category);
            }
            
            return $this->jsonResponse(['success' => true, 'data' => $products]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Buscar productos
    public function search() {
        $searchTerm = $_GET['q'] ?? '';
        
        if (empty($searchTerm)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Término de búsqueda requerido'], 400);
        }

        try {
            $products = $this->productModel->search($searchTerm);
            return $this->jsonResponse(['success' => true, 'data' => $products]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Obtener producto por ID
    public function show() {
        $id = $_GET['id'] ?? 0;
        
        if (!$id) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de producto requerido'], 400);
        }

        try {
            $product = $this->productModel->getById($id);
            
            if (!$product) {
                return $this->jsonResponse(['success' => false, 'message' => 'Producto no encontrado'], 404);
            }
            
            return $this->jsonResponse(['success' => true, 'data' => $product]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Crear producto (solo admin)
    public function create() {
        if (!$this->isAdmin()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $data = $this->getPostData();
        
        // Validar datos requeridos
        $required = ['name', 'price', 'category', 'image'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return $this->jsonResponse(['success' => false, 'message' => "Campo {$field} requerido"], 400);
            }
        }

        try {
            $id = $this->productModel->create(
                $data['name'],
                $data['price'],
                $data['category'],
                $data['image'],
                $data['description'] ?? ''
            );
            
            return $this->jsonResponse(['success' => true, 'message' => 'Producto creado exitosamente', 'id' => $id]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Actualizar producto (solo admin)
    public function update() {
        if (!$this->isAdmin()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $data = $this->getPostData();
        $id = $data['id'] ?? 0;
        
        if (!$id) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de producto requerido'], 400);
        }

        // Validar que el producto existe
        if (!$this->productModel->exists($id)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Producto no encontrado'], 404);
        }

        try {
            $this->productModel->update(
                $id,
                $data['name'],
                $data['price'],
                $data['category'],
                $data['image'],
                $data['description'] ?? ''
            );
            
            return $this->jsonResponse(['success' => true, 'message' => 'Producto actualizado exitosamente']);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Eliminar producto (solo admin)
    public function delete() {
        if (!$this->isAdmin()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Acceso denegado'], 403);
        }

        $data = $this->getPostData();
        $id = $data['id'] ?? 0;
        
        if (!$id) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de producto requerido'], 400);
        }

        try {
            $this->productModel->delete($id);
            return $this->jsonResponse(['success' => true, 'message' => 'Producto eliminado exitosamente']);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Obtener categorías disponibles
    public function getCategories() {
        try {
            $categories = $this->productModel->getCategories();
            return $this->jsonResponse(['success' => true, 'data' => $categories]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Métodos auxiliares
    private function getPostData() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $_POST;
        }
        
        // Para APIs REST, obtener datos JSON
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    private function isAdmin() {
        session_start();
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }

    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
