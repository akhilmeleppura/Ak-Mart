<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Ai\PredictiveIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PredictiveIntelligenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_predictive_intelligence_brief_and_stockout_forecasting()
    {
        $category = Category::create(['name' => 'Intelligence Test Cat', 'slug' => 'intel-test']);
        
        $p1 = Product::create([
            'name' => 'Fast Moving Milk',
            'slug' => 'fast-moving-milk',
            'sku' => 'MILK-01',
            'price' => 3.50,
            'qty' => 4,
            'min_stock' => 5,
            'max_stock' => 50,
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $user = User::factory()->create(['email' => 'shopper@example.com']);

        $order = Order::create([
            'order_number'     => 'ORD-INTEL-01',
            'user_id'          => $user->id,
            'shipping_address' => '123 Market Street, Suite 400',
            'total_amount'     => 350.00,
            'order_status'     => 'confirmed',
            'payment_method'   => 'cod',
            'created_at'       => \Illuminate\Support\Carbon::yesterday()->midDay(),
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $p1->id,
            'product_name' => $p1->name,
            'qty'          => 10,
            'price'        => 3.50,
            'total'        => 35.00,
            'created_at'   => \Illuminate\Support\Carbon::yesterday()->midDay(),
        ]);

        $service = app(PredictiveIntelligenceService::class);

        // 1. Test Daily Business Brief
        $brief = $service->getDailyBusinessBrief();
        $this->assertIsArray($brief);
        $this->assertEquals(350.00, $brief['yesterday_sales']);
        $this->assertEquals(1, $brief['yesterday_orders']);
        $this->assertNotEmpty($brief['ai_summary_text']);

        // 2. Test Stockout Velocity Forecasting
        $risks = $service->predictStockoutRisks(null, 7);
        $this->assertNotEmpty($risks);
        $this->assertEquals($p1->id, $risks[0]['product_id']);
        $this->assertGreaterThan(0, $risks[0]['recommended_reorder']);

        // 3. Test Order Fraud Scoring
        $fraudReport = $service->calculateOrderFraudRisk($order);
        $this->assertArrayHasKey('risk_score', $fraudReport);
        $this->assertArrayHasKey('risk_level', $fraudReport);
        $this->assertContains('High-value Cash on Delivery (COD) order.', $fraudReport['reasons']);
    }
}
