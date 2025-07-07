<?php
require_once __DIR__ . '/../config/Database.php';

class Product {
    private $db;
    private $table = 'products';

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Crear producto
    public function create($name, $price, $category, $image, $description = '') {
        $sql = "INSERT INTO {$this->table} (name, price, category, image, description, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        
        try {
            $this->db->execute($sql, [$name, $price, $category, $image, $description]);
            return $this->db->lastInsertId();
        } catch (Exception $e) {
            throw new Exception("Error al crear producto: " . $e->getMessage());
        }
    }

    // Obtener todos los productos
    public function getAll() {
        $sql = "SELECT * FROM {$this->table} WHERE active = 1 ORDER BY created_at DESC";
        return $this->db->fetchAll($sql);
    }

    // Obtener producto por ID
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND active = 1";
        return $this->db->fetch($sql, [$id]);
    }

    // Obtener productos por categoría
    public function getByCategory($category) {
        $sql = "SELECT * FROM {$this->table} WHERE category = ? AND active = 1 ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, [$category]);
    }

    // Buscar productos
    public function search($searchTerm) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (name LIKE ? OR description LIKE ?) AND active = 1 
                ORDER BY created_at DESC";
        $searchParam = "%{$searchTerm}%";
        return $this->db->fetchAll($sql, [$searchParam, $searchParam]);
    }

    // Actualizar producto
    public function update($id, $name, $price, $category, $image, $description = '') {
        $sql = "UPDATE {$this->table} 
                SET name = ?, price = ?, category = ?, image = ?, description = ?, updated_at = NOW() 
                WHERE id = ?";
        
        try {
            return $this->db->execute($sql, [$name, $price, $category, $image, $description, $id]);
        } catch (Exception $e) {
            throw new Exception("Error al actualizar producto: " . $e->getMessage());
        }
    }

    // Eliminar producto (soft delete)
    public function delete($id) {
        $sql = "UPDATE {$this->table} SET active = 0, updated_at = NOW() WHERE id = ?";
        
        try {
            return $this->db->execute($sql, [$id]);
        } catch (Exception $e) {
            throw new Exception("Error al eliminar producto: " . $e->getMessage());
        }
    }

    // Obtener categorías disponibles
    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM {$this->table} WHERE active = 1";
        return $this->db->fetchAll($sql);
    }

    // Verificar si existe un producto
    public function exists($id) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE id = ? AND active = 1";
        $result = $this->db->fetch($sql, [$id]);
        return $result['count'] > 0;
    }

    // Obtener productos con paginación
    public function getPaginated($page = 1, $limit = 10, $category = null) {
        $offset = ($page - 1) * $limit;
        
        $whereClause = "WHERE active = 1";
        $params = [];
        
        if ($category) {
            $whereClause .= " AND category = ?";
            $params[] = $category;
        }
        
        $sql = "SELECT * FROM {$this->table} {$whereClause} 
                ORDER BY created_at DESC 
                LIMIT ? OFFSET ?";
        
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }

    // Contar total de productos
    public function getTotalCount($category = null) {
        $whereClause = "WHERE active = 1";
        $params = [];
        
        if ($category) {
            $whereClause .= " AND category = ?";
            $params[] = $category;
        }
        
        $sql = "SELECT COUNT(*) as total FROM {$this->table} {$whereClause}";
        $result = $this->db->fetch($sql, $params);
        return $result['total'];
    }
}
?>
