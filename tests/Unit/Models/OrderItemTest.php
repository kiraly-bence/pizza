<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_item_belongs_to_order(): void
    {
        $order = Order::factory()->create();
        $item  = OrderItem::factory()->create(['order_id' => $order->id]);

        $this->assertInstanceOf(Order::class, $item->order);
        $this->assertEquals($order->id, $item->order->id);
    }

    public function test_order_item_belongs_to_product(): void
    {
        $product = Product::factory()->create();
        $item    = OrderItem::factory()->create(['product_id' => $product->id]);

        $this->assertInstanceOf(Product::class, $item->product);
        $this->assertEquals($product->id, $item->product->id);
    }

    public function test_order_item_price_is_cast_to_integer(): void
    {
        $item = OrderItem::factory()->create(['price' => 2490]);

        $this->assertIsInt($item->price);
        $this->assertEquals(2490, $item->price);
    }

    public function test_order_item_quantity_is_cast_to_integer(): void
    {
        $item = OrderItem::factory()->create(['quantity' => 3]);

        $this->assertIsInt($item->quantity);
        $this->assertEquals(3, $item->quantity);
    }
}
