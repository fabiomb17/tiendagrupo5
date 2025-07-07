<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Product.php';

class Cart {
    private $db;
    private $table = 'cart_items';
    private $productModel;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->productModel = new Product();
    }

    // Agregar producto al carrito
    public function addItem($userId, $productId, $quantity = 1) {
        // Verificar que el producto existe
        if (!$this->productModel->exists($productId)) {
            throw new Exception("El producto no existe");
        }

        // Verificar si el item ya está en el carrito
        $existingItem = $this->getItem($userId, $productId);
        
        if ($existingItem) {
            // Actualizar cantidad
            return $this->updateQuantity($userId, $productId, $existingItem['quantity'] + $quantity);
        } else {
            // Agregar nuevo item
            $sql = "INSERT INTO {$this->table} (user_id, product_id, quantity, created_at) 
                    VALUES (?, ?, ?, NOW())";
            
            try {
                return $this->db->execute($sql, [$userId, $productId, $quantity]);
            } catch (Exception $e) {
                throw new Exception("Error al agregar al carrito: " . $e->getMessage());
            }
        }
    }

    // Obtener item específico del carrito
    private function getItem($userId, $productId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        return $this->db->fetch($sql, [$userId, $productId]);
    }

    // Obtener todos los items del carrito de un usuario
    public function getCartItems($userId) {
        $sql = "SELECT c.*, p.name, p.price, p.image, p.category,
                       (c.quantity * p.price) as subtotal
                FROM {$this->table} c
                INNER JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ? AND p.active = 1
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }

    // Actualizar cantidad de un item
    public function updateQuantity($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeItem($userId, $productId);
        }

        $sql = "UPDATE {$this->table} SET quantity = ?, updated_at = NOW() 
                WHERE user_id = ? AND product_id = ?";
        
        try {
            return $this->db->execute($sql, [$quantity, $userId, $productId]);
        } catch (Exception $e) {
            throw new Exception("Error al actualizar cantidad: " . $e->getMessage());
        }
    }

    // Eliminar item del carrito
    public function removeItem($userId, $productId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ? AND product_id = ?";
        
        try {
            return $this->db->execute($sql, [$userId, $productId]);
        } catch (Exception $e) {
            throw new Exception("Error al eliminar item del carrito: " . $e->getMessage());
        }
    }

    // Vaciar carrito
    public function clearCart($userId) {
        $sql = "DELETE FROM {$this->table} WHERE user_id = ?";
        
        try {
            return $this->db->execute($sql, [$userId]);
        } catch (Exception $e) {
            throw new Exception("Error al vaciar carrito: " . $e->getMessage());
        }
    }

    // Obtener total del carrito
    public function getCartTotal($userId) {
        $sql = "SELECT 
                    COUNT(*) as item_count,
                    SUM(c.quantity) as total_quantity,
                    SUM(c.quantity * p.price) as total_amount
                FROM {$this->table} c
                INNER JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ? AND p.active = 1";
        
        $result = $this->db->fetch($sql, [$userId]);
        
        return [
            'item_count' => (int)($result['item_count'] ?? 0),
            'total_quantity' => (int)($result['total_quantity'] ?? 0),
            'total_amount' => (float)($result['total_amount'] ?? 0)
        ];
    }

    // Verificar si el carrito tiene items
    public function hasItems($userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = ?";
        $result = $this->db->fetch($sql, [$userId]);
        return $result['count'] > 0;
    }

    // Transferir carrito de sesión a usuario autenticado
    public function transferSessionCart($sessionId, $userId) {
        // Esta función sería útil si manejas carritos de sesión para usuarios no autenticados
        // Por ahora, solo implementamos para usuarios autenticados
        return true;
    }

    // Obtener items del carrito con información detallada para checkout
    public function getCheckoutItems($userId) {
        $sql = "SELECT 
                    c.product_id,
                    c.quantity,
                    p.name,
                    p.price,
                    p.image,
                    p.category,
                    (c.quantity * p.price) as subtotal
                FROM {$this->table} c
                INNER JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ? AND p.active = 1
                ORDER BY c.created_at DESC";
        
        return $this->db->fetchAll($sql, [$userId]);
    }
}
?>
