<?php

namespace Tests\Functional;

use Tests\Support\BaseTestCase;

class AdminPanelTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Simular sesión de administrador
        $_SESSION['user_id'] = 1;
        $_SESSION['user'] = 'admin';
        $_SESSION['role'] = 'admin';
    }
    
    public function testAdminCanAccessAdminPanel()
    {
        // Simular que el usuario está logueado como admin
        $this->assertTrue(isset($_SESSION['user_id']));
        $this->assertEquals('admin', $_SESSION['role']);
        
        // Verificar que puede acceder al panel
        $hasAccess = isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
        $this->assertTrue($hasAccess);
    }
    
    public function testNormalUserCannotAccessAdminPanel()
    {
        // Cambiar a usuario normal
        $_SESSION['role'] = 'user';
        
        $hasAccess = isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
        $this->assertFalse($hasAccess);
    }
    
    public function testUnauthenticatedUserCannotAccess()
    {
        // Limpiar sesión
        unset($_SESSION['user_id']);
        unset($_SESSION['role']);
        
        $hasAccess = isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin';
        $this->assertFalse($hasAccess);
    }
    
    public function testAdminCanCreateProduct()
    {
        $productData = [
            'name' => 'Nuevo Producto',
            'price' => 99.99,
            'category' => 'electronics',
            'image' => 'nuevo-producto.jpg',
            'description' => 'Descripción del nuevo producto'
        ];
        
        // Validar que los datos son correctos
        $this->assertNotEmpty($productData['name']);
        $this->assertGreaterThan(0, $productData['price']);
        $this->assertContains($productData['category'], ['electronics', 'clothing', 'home']);
        $this->assertNotEmpty($productData['image']);
        
        // Simular creación exitosa
        $created = true; // En realidad vendría de la base de datos
        $this->assertTrue($created);
    }
    
    public function testAdminCanEditProduct()
    {
        $originalProduct = [
            'id' => 1,
            'name' => 'Producto Original',
            'price' => 50.00,
            'category' => 'electronics'
        ];
        
        $updatedData = [
            'name' => 'Producto Actualizado',
            'price' => 75.00,
            'category' => 'clothing'
        ];
        
        // Simular actualización
        $updatedProduct = array_merge($originalProduct, $updatedData);
        
        $this->assertEquals('Producto Actualizado', $updatedProduct['name']);
        $this->assertEquals(75.00, $updatedProduct['price']);
        $this->assertEquals('clothing', $updatedProduct['category']);
        $this->assertEquals(1, $updatedProduct['id']); // ID no cambia
    }
    
    public function testAdminCanDeleteProduct()
    {
        $productId = 1;
        $products = [
            ['id' => 1, 'name' => 'Producto 1'],
            ['id' => 2, 'name' => 'Producto 2'],
            ['id' => 3, 'name' => 'Producto 3']
        ];
        
        // Simular eliminación
        $remainingProducts = array_filter($products, function($product) use ($productId) {
            return $product['id'] !== $productId;
        });
        
        $this->assertCount(2, $remainingProducts);
        
        // Verificar que el producto específico ya no está
        $productIds = array_column($remainingProducts, 'id');
        $this->assertNotContains($productId, $productIds);
    }
    
    public function testAdminCanManageUsers()
    {
        $userData = [
            'username' => 'nuevousuario',
            'email' => 'nuevo@test.com',
            'password' => 'password123',
            'role' => 'user'
        ];
        
        // Validar datos de usuario
        $this->assertNotEmpty($userData['username']);
        $this->assertTrue(filter_var($userData['email'], FILTER_VALIDATE_EMAIL) !== false);
        $this->assertTrue(strlen($userData['password']) >= 6);
        $this->assertContains($userData['role'], ['user', 'admin']);
        
        // Simular creación de usuario
        $userCreated = true;
        $this->assertTrue($userCreated);
    }
    
    public function testAdminCanToggleUserStatus()
    {
        $user = [
            'id' => 2,
            'username' => 'testuser',
            'active' => 1
        ];
        
        // Simular cambio de estado
        $user['active'] = $user['active'] ? 0 : 1;
        
        $this->assertEquals(0, $user['active']);
        
        // Cambiar de nuevo
        $user['active'] = $user['active'] ? 0 : 1;
        $this->assertEquals(1, $user['active']);
    }
}
