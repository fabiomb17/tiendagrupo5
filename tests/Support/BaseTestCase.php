<?php

namespace Tests\Support;

use PHPUnit\Framework\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Configurar variables de entorno para pruebas
        $_ENV['DB_HOST'] = 'localhost';
        $_ENV['DB_NAME'] = 'tiendagrupo5_test';
        $_ENV['DB_USER'] = 'root';
        $_ENV['DB_PASS'] = 'root';
        
        // Limpiar sesiones
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        $_SESSION = [];
    }
    
    protected function tearDown(): void
    {
        // Limpiar después de cada prueba
        $_SESSION = [];
        parent::tearDown();
    }
    
    /**
     * Helper para crear usuario admin de prueba
     */
    protected function createAdminUser(): array
    {
        return [
            'id' => 1,
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'role' => 'admin',
            'active' => 1
        ];
    }
    
    /**
     * Helper para crear usuario normal de prueba
     */
    protected function createNormalUser(): array
    {
        return [
            'id' => 2,
            'username' => 'user_test',
            'email' => 'user@test.com',
            'role' => 'user',
            'active' => 1
        ];
    }
    
    /**
     * Helper para crear producto de prueba
     */
    protected function createTestProduct(): array
    {
        return [
            'id' => 1,
            'name' => 'Producto Test',
            'price' => 99.99,
            'category' => 'electronics',
            'image' => 'test.jpg',
            'description' => 'Producto de prueba',
            'active' => 1
        ];
    }
}
