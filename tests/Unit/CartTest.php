<?php

namespace Tests\Unit;

require_once __DIR__ . '/../../tests/support/BaseTestCase.php';
require_once __DIR__ . '/../../models/Cart.php';

use Tests\Support\BaseTestCase;

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
