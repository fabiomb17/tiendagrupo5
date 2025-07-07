<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Cart.php';
require_once __DIR__ . '/Product.php';

class Order {
    private $db;
    private $table = 'orders';
    private $itemsTable = 'order_items';
    private $cartModel;
    private $productModel;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->cartModel = new Cart();
        $this->productModel = new Product();
    }

    // Crear orden desde el carrito
    public function createFromCart($userId, $shippingInfo = []) {
        try {
            // Iniciar transacción
            $this->db->getConnection()->beginTransaction();

            // Obtener items del carrito
            $cartItems = $this->cartModel->getCheckoutItems($userId);
            
            if (empty($cartItems)) {
                throw new Exception("El carrito está vacío");
            }

            // Calcular total
            $total = array_sum(array_column($cartItems, 'subtotal'));

            // Crear orden
            $orderNumber = $this->generateOrderNumber();
            $sql = "INSERT INTO {$this->table} 
                    (user_id, order_number, total_amount, status, shipping_info, created_at) 
                    VALUES (?, ?, ?, 'pending', ?, NOW())";
            
            $this->db->execute($sql, [
                $userId, 
                $orderNumber, 
                $total, 
                json_encode($shippingInfo)
            ]);

            $orderId = $this->db->lastInsertId();

            // Agregar items de la orden
            foreach ($cartItems as $item) {
                $this->addOrderItem($orderId, $item);
            }

            // Vaciar carrito
            $this->cartModel->clearCart($userId);

            // Confirmar transacción
            $this->db->getConnection()->commit();

            return $orderId;

        } catch (Exception $e) {
            // Revertir transacción en caso de error
            $this->db->getConnection()->rollBack();
            throw new Exception("Error al crear orden: " . $e->getMessage());
        }
    }

    // Agregar item a la orden
    private function addOrderItem($orderId, $item) {
        $sql = "INSERT INTO {$this->itemsTable} 
                (order_id, product_id, quantity, price, subtotal) 
                VALUES (?, ?, ?, ?, ?)";
        
        return $this->db->execute($sql, [
            $orderId,
            $item['product_id'],
            $item['quantity'],
            $item['price'],
            $item['subtotal']
        ]);
    }

    // Generar número de orden único
    private function generateOrderNumber() {
        $prefix = 'ORD';
        $timestamp = date('YmdHis');
        $random = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }

    // Obtener orden por ID
    public function getById($id) {
        $sql = "SELECT o.*, u.username, u.email 
                FROM {$this->table} o
                INNER JOIN users u ON o.user_id = u.id
                WHERE o.id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }

    // Obtener órdenes de un usuario
    public function getUserOrders($userId, $limit = 10, $offset = 0) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        return $this->db->fetchAll($sql, [$userId, $limit, $offset]);
    }

    // Obtener todas las órdenes (para admin)
    public function getAll($limit = 50, $offset = 0, $status = null) {
        $whereClause = '';
        $params = [];
        
        if ($status) {
            $whereClause = 'WHERE o.status = ?';
            $params[] = $status;
        }
        
        $sql = "SELECT o.*, u.username, u.email 
                FROM {$this->table} o
                INNER JOIN users u ON o.user_id = u.id
                {$whereClause}
                ORDER BY o.created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }

    // Obtener items de una orden
    public function getOrderItems($orderId) {
        $sql = "SELECT oi.*, p.name, p.image, p.category
                FROM {$this->itemsTable} oi
                INNER JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
                ORDER BY oi.id";
        
        return $this->db->fetchAll($sql, [$orderId]);
    }

    // Actualizar estado de orden
    public function updateStatus($orderId, $status) {
        $allowedStatuses = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'];
        
        if (!in_array($status, $allowedStatuses)) {
            throw new Exception("Estado de orden no válido");
        }

        $sql = "UPDATE {$this->table} SET status = ?, updated_at = NOW() WHERE id = ?";
        
        try {
            return $this->db->execute($sql, [$status, $orderId]);
        } catch (Exception $e) {
            throw new Exception("Error al actualizar estado de orden: " . $e->getMessage());
        }
    }

    // Cancelar orden
    public function cancel($orderId, $userId = null) {
        $whereClause = "id = ?";
        $params = [$orderId];
        
        // Si se proporciona userId, verificar que la orden pertenezca al usuario
        if ($userId) {
            $whereClause .= " AND user_id = ?";
            $params[] = $userId;
        }

        $sql = "UPDATE {$this->table} 
                SET status = 'cancelled', updated_at = NOW() 
                WHERE {$whereClause} AND status IN ('pending', 'confirmed')";
        
        try {
            $affected = $this->db->execute($sql, $params);
            if ($affected === 0) {
                throw new Exception("No se puede cancelar esta orden");
            }
            return true;
        } catch (Exception $e) {
            throw new Exception("Error al cancelar orden: " . $e->getMessage());
        }
    }

    // Obtener estadísticas de órdenes
    public function getStats($startDate = null, $endDate = null) {
        $whereClause = '1=1';
        $params = [];
        
        if ($startDate) {
            $whereClause .= ' AND created_at >= ?';
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $whereClause .= ' AND created_at <= ?';
            $params[] = $endDate;
        }

        $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(total_amount) as total_revenue,
                    AVG(total_amount) as average_order_value,
                    COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_orders,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders
                FROM {$this->table} 
                WHERE {$whereClause}";
        
        return $this->db->fetch($sql, $params);
    }

    // Verificar si una orden pertenece a un usuario
    public function belongsToUser($orderId, $userId) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE id = ? AND user_id = ?";
        $result = $this->db->fetch($sql, [$orderId, $userId]);
        return $result['count'] > 0;
    }
}
?>
