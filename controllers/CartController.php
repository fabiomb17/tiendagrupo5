<?php
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../controllers/AuthController.php';

class CartController {
    private $cartModel;

    public function __construct() {
        $this->cartModel = new Cart();
    }

    // Obtener items del carrito
    public function getItems() {
        $userId = AuthController::requireAuth();
        
        try {
            $items = $this->cartModel->getCartItems($userId);
            $total = $this->cartModel->getCartTotal($userId);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => [
                    'items' => $items,
                    'total' => $total
                ]
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Agregar producto al carrito
    public function addItem() {
        $userId = AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $data = $this->getPostData();
        $productId = $data['product_id'] ?? 0;
        $quantity = $data['quantity'] ?? 1;

        if (!$productId) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de producto requerido'], 400);
        }

        if ($quantity < 1) {
            return $this->jsonResponse(['success' => false, 'message' => 'Cantidad debe ser mayor a 0'], 400);
        }

        try {
            $this->cartModel->addItem($userId, $productId, $quantity);
            $total = $this->cartModel->getCartTotal($userId);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Producto agregado al carrito',
                'cart_total' => $total
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Actualizar cantidad de un item
    public function updateQuantity() {
        $userId = AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $data = $this->getPostData();
        $productId = $data['product_id'] ?? 0;
        $quantity = $data['quantity'] ?? 0;

        if (!$productId) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de producto requerido'], 400);
        }

        try {
            $this->cartModel->updateQuantity($userId, $productId, $quantity);
            $total = $this->cartModel->getCartTotal($userId);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Cantidad actualizada',
                'cart_total' => $total
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Eliminar item del carrito
    public function removeItem() {
        $userId = AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        $data = $this->getPostData();
        $productId = $data['product_id'] ?? 0;

        if (!$productId) {
            return $this->jsonResponse(['success' => false, 'message' => 'ID de producto requerido'], 400);
        }

        try {
            $this->cartModel->removeItem($userId, $productId);
            $total = $this->cartModel->getCartTotal($userId);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Producto eliminado del carrito',
                'cart_total' => $total
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Vaciar carrito
    public function clearCart() {
        $userId = AuthController::requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->jsonResponse(['success' => false, 'message' => 'Método no permitido'], 405);
        }

        try {
            $this->cartModel->clearCart($userId);
            
            return $this->jsonResponse([
                'success' => true,
                'message' => 'Carrito vaciado exitosamente'
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Obtener resumen del carrito
    public function getSummary() {
        $userId = AuthController::requireAuth();
        
        try {
            $total = $this->cartModel->getCartTotal($userId);
            
            return $this->jsonResponse([
                'success' => true,
                'data' => $total
            ]);
        } catch (Exception $e) {
            return $this->jsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Métodos auxiliares
    private function getPostData() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Primero verificar si hay datos POST normales
            if (!empty($_POST)) {
                return $_POST;
            }
        }
        
        // Para APIs REST, obtener datos JSON
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    private function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
?>
