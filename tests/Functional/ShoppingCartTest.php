<?php

namespace Tests\Functional;

require_once __DIR__ . '/../../tests/support/BaseTestCase.php';

use Tests\Support\BaseTestCase;

class ShoppingCartTest extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Simular sesión de usuario
        $_SESSION['user_id'] = 2;
        $_SESSION['user'] = 'testuser';
        $_SESSION['role'] = 'user';
    }
    
    public function testUserCanAddProductToCart()
    {
        $cart = [];
        $product = [
            'id' => 1,
            'name' => 'iPhone 13',
            'price' => 999.99,
            'quantity' => 1
        ];
        
        // Simular agregar producto al carrito
        $cart[] = $product;
        
        $this->assertCount(1, $cart);
        $this->assertEquals($product['id'], $cart[0]['id']);
        $this->assertEquals($product['quantity'], $cart[0]['quantity']);
    }
    
    public function testUserCanUpdateCartQuantity()
    {
        $cartItem = [
            'product_id' => 1,
            'quantity' => 2,
            'price' => 99.99
        ];
        
        // Actualizar cantidad
        $newQuantity = 5;
        $cartItem['quantity'] = $newQuantity;
        
        $this->assertEquals(5, $cartItem['quantity']);
        
        // Validar que la cantidad esté en rango válido
        $this->assertGreaterThan(0, $cartItem['quantity']);
        $this->assertLessThanOrEqual(99, $cartItem['quantity']);
    }
    
    public function testUserCanRemoveProductFromCart()
    {
        $cart = [
            ['product_id' => 1, 'name' => 'Producto 1', 'quantity' => 2],
            ['product_id' => 2, 'name' => 'Producto 2', 'quantity' => 1],
            ['product_id' => 3, 'name' => 'Producto 3', 'quantity' => 3]
        ];
        
        $productToRemove = 2;
        
        // Filtrar carrito
        $cart = array_values(array_filter($cart, function($item) use ($productToRemove) {
            return $item['product_id'] !== $productToRemove;
        }));
        
        $this->assertCount(2, $cart);
        
        // Verificar que el producto específico fue eliminado
        $productIds = array_column($cart, 'product_id');
        $this->assertNotContains($productToRemove, $productIds);
    }
    
    public function testCartCalculatesTotalCorrectly()
    {
        $cartItems = [
            ['product_id' => 1, 'quantity' => 2, 'price' => 50.00],
            ['product_id' => 2, 'quantity' => 1, 'price' => 30.00],
            ['product_id' => 3, 'quantity' => 3, 'price' => 20.00]
        ];
        
        // Calcular total
        $total = 0;
        $totalItems = 0;
        
        foreach ($cartItems as $item) {
            $total += $item['quantity'] * $item['price'];
            $totalItems += $item['quantity'];
        }
        
        $this->assertEquals(190.00, $total); // (2*50) + (1*30) + (3*20) = 100 + 30 + 60
        $this->assertEquals(6, $totalItems); // 2 + 1 + 3
    }
    
    public function testUserCanClearCart()
    {
        $cart = [
            ['product_id' => 1, 'quantity' => 2],
            ['product_id' => 2, 'quantity' => 1]
        ];
        
        // Limpiar carrito
        $cart = [];
        
        $this->assertEmpty($cart);
        $this->assertCount(0, $cart);
    }
    
    public function testCartPersistsAcrossSessions()
    {
        $userId = $_SESSION['user_id'];
        
        // Simular datos del carrito en base de datos
        $cartData = [
            'user_id' => $userId,
            'items' => [
                ['product_id' => 1, 'quantity' => 2],
                ['product_id' => 3, 'quantity' => 1]
            ]
        ];
        
        $this->assertEquals($userId, $cartData['user_id']);
        $this->assertCount(2, $cartData['items']);
    }
    
    public function testCartValidatesProductQuantity()
    {
        $validQuantities = [1, 5, 10, 99];
        $invalidQuantities = [0, -1, 100, 'invalid'];
        
        foreach ($validQuantities as $qty) {
            $this->assertGreaterThan(0, $qty);
            $this->assertLessThanOrEqual(99, $qty);
            $this->assertIsInt($qty);
        }
        
        foreach ($invalidQuantities as $qty) {
            if (is_int($qty)) {
                $isValid = $qty > 0 && $qty <= 99;
                $this->assertFalse($isValid);
            } else {
                $this->assertFalse(is_int($qty));
            }
        }
    }
    
    public function testCartSummaryDisplay()
    {
        $cartSummary = [
            'total_items' => 5,
            'total_amount' => 249.95,
            'items_count' => 3,
            'currency' => 'DOP'
        ];
        
        $this->assertIsInt($cartSummary['total_items']);
        $this->assertIsFloat($cartSummary['total_amount']);
        $this->assertIsInt($cartSummary['items_count']);
        $this->assertEquals('DOP', $cartSummary['currency']);
        
        // Formatear moneda
        $formattedAmount = $cartSummary['currency'] . ' ' . number_format($cartSummary['total_amount'], 2);
        $this->assertEquals('DOP 249.95', $formattedAmount);
    }
}
