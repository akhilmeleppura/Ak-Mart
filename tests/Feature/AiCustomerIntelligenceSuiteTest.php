<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Services\Ai\CustomerIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class AiCustomerIntelligenceSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $vipCustomer;
    protected User $atRiskCustomer;
    protected User $newCustomer;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name'      => 'Electronics',
            'slug'      => 'electronics',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name'        => 'Wireless ANC Earbuds',
            'slug'        => 'wireless-anc-earbuds',
            'sku'         => 'ANC-01',
            'price'       => 150.00,
            'qty'         => 20,
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        $this->vipCustomer = User::factory()->create([
            'email' => 'vip_shopper@example.com',
        ]);

        $this->atRiskCustomer = User::factory()->create([
            'email' => 'dormant_shopper@example.com',
        ]);

        $this->newCustomer = User::factory()->create([
            'email' => 'new_shopper@example.com',
        ]);
    }

    public function test_customer_lifecycle_segmentation()
    {
        $crmService = app(CustomerIntelligenceService::class);

        // 1. VIP Customer (Total Spend > $1,000)
        Order::create([
            'order_number'   => 'ORD-VIP-01',
            'user_id'        => $this->vipCustomer->id,
            'total_amount'   => 1200.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        $vipSeg = $crmService->calculateCustomerLifecycleSegment($this->vipCustomer);
        $this->assertEquals('VIP', $vipSeg['segment']);

        // 2. New Customer (1 Order)
        Order::create([
            'order_number'   => 'ORD-NEW-01',
            'user_id'        => $this->newCustomer->id,
            'total_amount'   => 150.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        $newSeg = $crmService->calculateCustomerLifecycleSegment($this->newCustomer);
        $this->assertEquals('New Customer', $newSeg['segment']);
    }

    public function test_customer_lifetime_value_calculation()
    {
        $crmService = app(CustomerIntelligenceService::class);

        // Create 2 completed orders for VIP Customer
        Order::create([
            'order_number'   => 'ORD-CLV-01',
            'user_id'        => $this->vipCustomer->id,
            'total_amount'   => 500.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);
        Order::create([
            'order_number'   => 'ORD-CLV-02',
            'user_id'        => $this->vipCustomer->id,
            'total_amount'   => 700.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        $clv = $crmService->calculateCustomerLifetimeValue($this->vipCustomer);
        $this->assertEquals(1200.00, $clv['historical_spend']);
        $this->assertGreaterThan(0, $clv['predicted_12m_value']);
        $this->assertContains($clv['confidence'], ['Medium', 'High']);
    }

    public function test_churn_risk_and_next_best_action()
    {
        $crmService = app(CustomerIntelligenceService::class);

        $oldDate = Carbon::now()->subDays(120);
        $o1 = Order::create([
            'order_number'   => 'ORD-DORM-01',
            'user_id'        => $this->atRiskCustomer->id,
            'total_amount'   => 200.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);
        $o2 = Order::create([
            'order_number'   => 'ORD-DORM-02',
            'user_id'        => $this->atRiskCustomer->id,
            'total_amount'   => 250.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        \Illuminate\Support\Facades\DB::table('orders')->whereIn('id', [$o1->id, $o2->id])->update([
            'created_at' => $oldDate->toDateTimeString(),
            'updated_at' => $oldDate->toDateTimeString(),
        ]);

        $churn = $crmService->calculateChurnRisk($this->atRiskCustomer);
        $this->assertEquals('High', $churn['risk_level']);

        $action = $crmService->getNextBestAction($this->atRiskCustomer);
        $this->assertEquals('Send Win-Back Campaign', $action['action']);
    }
}
