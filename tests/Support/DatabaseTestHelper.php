<?php

namespace Tests\Support;

class DatabaseTestHelper
{
    private static $connection = null;
    
    public static function getTestConnection()
    {
        if (self::$connection === null) {
            try {
                self::$connection = new \PDO(
                    "mysql:host=localhost;dbname=tiendagrupo5_test;charset=utf8mb4",
                    "root",
                    "root",
                    [
                        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                        \PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            } catch (\PDOException $e) {
                // Si no hay base de datos de prueba, usar mock
                return null;
            }
        }
        
        return self::$connection;
    }
    
    public static function createTestDatabase()
    {
        try {
            $pdo = new \PDO("mysql:host=localhost;charset=utf8mb4", "root", "root");
            $pdo->exec("CREATE DATABASE IF NOT EXISTS tiendagrupo5_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    public static function setupTestTables()
    {
        $connection = self::getTestConnection();
        if (!$connection) {
            return false;
        }
        
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            role ENUM('user', 'admin') DEFAULT 'user',
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(200) NOT NULL,
            description TEXT,
            price DECIMAL(10, 2) NOT NULL,
            category VARCHAR(50) NOT NULL,
            image VARCHAR(500) NOT NULL,
            active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS cart_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
            UNIQUE KEY unique_user_product (user_id, product_id)
        );
        ";
        
        try {
            $connection->exec($sql);
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    public static function clearTestData()
    {
        $connection = self::getTestConnection();
        if (!$connection) {
            return false;
        }
        
        try {
            $connection->exec("SET FOREIGN_KEY_CHECKS = 0");
            $connection->exec("TRUNCATE TABLE cart_items");
            $connection->exec("TRUNCATE TABLE products");
            $connection->exec("TRUNCATE TABLE users");
            $connection->exec("SET FOREIGN_KEY_CHECKS = 1");
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
    
    public static function seedTestData()
    {
        $connection = self::getTestConnection();
        if (!$connection) {
            return false;
        }
        
        try {
            // Insertar usuario admin de prueba
            $stmt = $connection->prepare("
                INSERT INTO users (username, email, password, role) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute(['admin_test', 'admin@test.com', password_hash('password123', PASSWORD_DEFAULT), 'admin']);
            
            // Insertar usuario normal de prueba
            $stmt->execute(['user_test', 'user@test.com', password_hash('password123', PASSWORD_DEFAULT), 'user']);
            
            // Insertar productos de prueba
            $stmt = $connection->prepare("
                INSERT INTO products (name, description, price, category, image) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute(['iPhone 13', 'Smartphone de Apple', 999.99, 'electronics', 'iphone13.jpg']);
            $stmt->execute(['Samsung Galaxy', 'Smartphone de Samsung', 899.99, 'electronics', 'galaxy.jpg']);
            $stmt->execute(['Camisa Azul', 'Camisa casual azul', 29.99, 'clothing', 'camisa-azul.jpg']);
            
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }
}
