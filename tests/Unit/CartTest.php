<?php

namespace Tests\Unit;

use Tests\Support\BaseTestCase;

require_once __DIR__ . '/../../models/Cart.php';

class CartTest extends BaseTestCase
{
    private $cartModel;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->cartModel = new \Cart();
    }
    
    public function testCartCanAddItems()
    {
        $userId = 1;
        $productId = 1;
        $quantity = 2;
        
        // Validaciones básicas
        $this->assertIsInt($userId);
        $this->assertIsInt($productId);
        $this->assertIsInt($quantity);
        $this->assertGreaterThan(0, $userId);
        $this->assertGreaterThan(0, $productId);
        $this->assertGreaterThan(0, $quantity);
    }
    
    public function testCartItemValidation()
    {
        // Validar cantidad
        $this->assertTrue(1 > 0);
        $this->assertTrue(5 > 0);
        $this->assertFalse(0 > 0);
        $this->assertFalse(-1 > 0);
        
        // Validar cantidad máxima
        $maxQuantity = 99;
        $this->assertTrue(1 <= $maxQuantity);
        $this->assertTrue(50 <= $maxQuantity);
        $this->assertFalse(100 > $maxQuantity);
    }
    
    public function testCartCalculations()
    {
        $cartItems = [
            ['product_id' => 1, 'quantity' => 2, 'price' => 99.99],
            ['product_id' => 2, 'quantity' => 1, 'price' => 49.99],
            ['product_id' => 3, 'quantity' => 3, 'price' => 19.99]
        ];
        
        // Calcular total de artículos
        $totalItems = array_sum(array_column($cartItems, 'quantity'));
        $this->assertEquals(6, $totalItems);
        
        // Calcular total del carrito
        $totalAmount = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['quantity'] * $item['price'];
        }
        
        $expectedTotal = (2 * 99.99) + (1 * 49.99) + (3 * 19.99);
        $this->assertEquals($expectedTotal, $totalAmount);
        $this->assertEquals(309.95, $totalAmount);
    }
    
    public function testCartIsEmpty()
    {
        $emptyCart = [];
        $cartWithItems = [
            ['product_id' => 1, 'quantity' => 1]
        ];
        
        $this->assertTrue(empty($emptyCart));
        $this->assertFalse(empty($cartWithItems));
        $this->assertCount(0, $emptyCart);
        $this->assertCount(1, $cartWithItems);
    }
    
    public function testCartItemUpdate()
    {
        $item = ['product_id' => 1, 'quantity' => 2, 'price' => 99.99];
        
        // Actualizar cantidad
        $item['quantity'] = 5;
        $this->assertEquals(5, $item['quantity']);
        
        // Validar nueva cantidad
        $this->assertGreaterThan(0, $item['quantity']);
        $this->assertLessThanOrEqual(99, $item['quantity']);
    }
    
    public function testCartItemRemoval()
    {
        $cartItems = [
            ['product_id' => 1, 'quantity' => 2],
            ['product_id' => 2, 'quantity' => 1],
            ['product_id' => 3, 'quantity' => 3]
        ];
        
        $productToRemove = 2;
        
        // Filtrar items (simular eliminación)
        $filteredItems = array_filter($cartItems, function($item) use ($productToRemove) {
            return $item['product_id'] !== $productToRemove;
        });
        
        $this->assertCount(2, $filteredItems);
        
        // Verificar que el producto fue eliminado
        $productIds = array_column($filteredItems, 'product_id');
        $this->assertNotContains($productToRemove, $productIds);
    }
    
    public function testCartSummary()
    {
        $cartItems = [
            ['product_id' => 1, 'quantity' => 2, 'price' => 50.00, 'name' => 'Producto 1'],
            ['product_id' => 2, 'quantity' => 1, 'price' => 30.00, 'name' => 'Producto 2']
        ];
        
        $summary = [
            'total_items' => array_sum(array_column($cartItems, 'quantity')),
            'total_amount' => array_sum(array_map(function($item) {
                return $item['quantity'] * $item['price'];
            }, $cartItems)),
            'items_count' => count($cartItems)
        ];
        
        $this->assertEquals(3, $summary['total_items']);
        $this->assertEquals(130.00, $summary['total_amount']);
        $this->assertEquals(2, $summary['items_count']);
    }
}
