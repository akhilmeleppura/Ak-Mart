<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use App\Models\ShippingMethod;
use App\Models\StockNotification;
use App\Models\PriceAlert;
use App\Services\ShippingService;
use App\Services\PricingEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NonAiCommerceSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name'      => 'Non-AI Test Category',
            'slug'      => 'non-ai-test-cat',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'email' => 'supreme_admin@akmart.com',
        ]);
    }

    public function test_bulk_stock_and_pricing_updates_with_immutable_ledger()
    {
        $this->actingAs($this->admin);

        $p1 = Product::create([
            'name'        => 'Bulk Item 1',
            'slug'        => 'bulk-item-1',
            'sku'         => 'BULK-01',
            'price'       => 20.00,
            'qty'         => 10,
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        $p2 = Product::create([
            'name'        => 'Bulk Item 2',
            'slug'        => 'bulk-item-2',
            'sku'         => 'BULK-02',
            'price'       => 30.00,
            'qty'         => 15,
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        // 1. Test Bulk Stock Addition (+5 to each)
        $resStock = $this->postJson(route('app-ecommerce-product-bulk-stock'), [
            'ids'        => [$p1->id, $p2->id],
            'adjustment' => 5,
            'type'       => 'add',
        ]);

        $resStock->assertStatus(200);
        $resStock->assertJson(['success' => true]);
        $this->assertEquals(15, $p1->fresh()->qty);
        $this->assertEquals(20, $p2->fresh()->qty);

        // Verify Immutable Ledger logged movements
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $p1->id,
            'quantity'   => 5,
            'reason'     => 'Bulk Inventory Stock Adjustment',
        ]);

        // 2. Test Bulk Pricing Update (10% increase)
        $resPrice = $this->postJson(route('app-ecommerce-product-bulk-pricing'), [
            'ids'        => [$p1->id, $p2->id],
            'adjustment' => 10,
            'type'       => 'percent',
        ]);

        $resPrice->assertStatus(200);
        $this->assertEquals(22.00, $p1->fresh()->price);
        $this->assertEquals(33.00, $p2->fresh()->price);
    }

    public function test_shipping_service_courier_abstraction_and_pincode_check()
    {
        $shippingService = app(ShippingService::class);

        // 1. Pincode Serviceability Check
        $pincodeResult = $shippingService->checkServiceability('560001', 'delhivery');
        $this->assertTrue($pincodeResult['serviceable']);
        $this->assertEquals('560001', $pincodeResult['pincode']);

        // 2. Invalid Pincode
        $invalidResult = $shippingService->checkServiceability('123');
        $this->assertFalse($invalidResult['serviceable']);

        // 3. Courier Method Shipment Dispatch
        $order = Order::create([
            'order_number'     => 'ORD-SHIP-01',
            'user_id'          => $this->admin->id,
            'total_amount'     => 100.00,
            'order_status'     => 'confirmed',
            'shipping_address' => '456 Delivery Lane, Metro',
        ]);

        $method = ShippingMethod::create([
            'name'         => 'Delhivery Express',
            'carrier_code' => 'delhivery',
            'cost'         => 15.00,
            'is_active'    => true,
        ]);

        $shipment = $shippingService->createShipment($order, $method);
        $this->assertNotFalse($shipment);
        $this->assertStringStartsWith('DLV-', $shipment->tracking_id);
    }

    public function test_back_in_stock_and_price_alert_subscriptions()
    {
        $product = Product::create([
            'name'        => 'Watch Me Product',
            'slug'        => 'watch-me-product',
            'sku'         => 'WATCH-01',
            'price'       => 50.00,
            'qty'         => 0,
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        // 1. Back-in-Stock Subscription
        $stockAlert = StockNotification::create([
            'product_id'  => $product->id,
            'user_id'     => $this->admin->id,
            'email'       => $this->admin->email,
            'is_notified' => false,
        ]);
        $this->assertDatabaseHas('stock_notifications', ['id' => $stockAlert->id, 'is_notified' => false]);

        // 2. Price-Drop Alert Subscription
        $priceAlert = PriceAlert::create([
            'product_id'   => $product->id,
            'user_id'      => $this->admin->id,
            'email'        => $this->admin->email,
            'target_price' => 40.00,
            'is_triggered' => false,
        ]);
        $this->assertDatabaseHas('price_alerts', ['id' => $priceAlert->id, 'target_price' => 40.00]);
    }

    public function test_ai_copilot_conversational_assistant_offline_boundary()
    {
        $this->actingAs($this->admin);

        $res = $this->postJson(route('app-ai-copilot-chat'), [
            'messages' => [
                ['role' => 'user', 'content' => 'What is our total sales and inventory status?']
            ]
        ]);

        $res->assertStatus(200);
        $res->assertJson(['success' => true]);
        $this->assertNotEmpty($res->json('reply'));
    }
}
