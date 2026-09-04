<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Expense;
use App\Services\Ai\BusinessIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class AiBusinessIntelligenceSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Category $categoryAudio;
    protected Category $categoryComputing;
    protected Product $productHeadphones;
    protected Product $productLaptop;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categoryAudio = Category::create([
            'name'      => 'Audio & Sound',
            'slug'      => 'audio-sound',
            'is_active' => true,
        ]);

        $this->categoryComputing = Category::create([
            'name'      => 'Computing',
            'slug'      => 'computing',
            'is_active' => true,
        ]);

        $this->productHeadphones = Product::create([
            'name'        => 'Pro ANC Headphones',
            'slug'        => 'pro-anc-headphones',
            'sku'         => 'HDP-01',
            'price'       => 200.00,
            'qty'         => 15,
            'category_id' => $this->categoryAudio->id,
            'is_active'   => true,
        ]);

        $this->productLaptop = Product::create([
            'name'        => 'SlimBook 15 Laptop',
            'slug'        => 'slimbook-15-laptop',
            'sku'         => 'LAP-01',
            'price'       => 1000.00,
            'qty'         => 3, // Low stock <= 5
            'category_id' => $this->categoryComputing->id,
            'is_active'   => true,
        ]);

        $this->customer = User::factory()->create([
            'email' => 'executive_shopper@example.com',
        ]);
    }

    public function test_centralized_kpi_registry_definitions()
    {
        $biService = app(BusinessIntelligenceService::class);

        $kpis = $biService->getKpiRegistry();

        $this->assertArrayHasKey('gross_revenue', $kpis);
        $this->assertArrayHasKey('aov', $kpis);
        $this->assertArrayHasKey('net_profit', $kpis);
        $this->assertArrayHasKey('profit_margin', $kpis);
        $this->assertArrayHasKey('return_rate', $kpis);
        $this->assertEquals('orders', $kpis['gross_revenue']['data_source']);
    }

    public function test_executive_daily_business_brief_generation()
    {
        $biService = app(BusinessIntelligenceService::class);

        // Create a paid order today
        $order = Order::create([
            'order_number'   => 'ORD-BRIEF-01',
            'user_id'        => $this->customer->id,
            'total_amount'   => 1200.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        $brief = $biService->getExecutiveDailyBrief();

        $this->assertEquals(1200.00, $brief['sales']['revenue']);
        $this->assertEquals(1, $brief['sales']['order_count']);
        $this->assertEquals(1200.00, $brief['sales']['aov']);
        $this->assertGreaterThan(0, $brief['profit']['net_profit']);
        $this->assertGreaterThanOrEqual(1, $brief['inventory']['low_stock_skus']); // SlimBook 15 has qty 3
        $this->assertNotEmpty($brief['recommendations']);
    }

    public function test_period_over_period_comparison()
    {
        $biService = app(BusinessIntelligenceService::class);

        // Create paid order this month
        Order::create([
            'order_number'   => 'ORD-CURR-01',
            'user_id'        => $this->customer->id,
            'total_amount'   => 1000.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
            'created_at'     => Carbon::now()->startOfMonth()->addHours(2),
        ]);

        $comp = $biService->comparePeriods('month');

        $this->assertEquals('This Month vs Last Month', $comp['comparison_type']);
        $this->assertGreaterThanOrEqual(1000.00, $comp['current_period_rev']);
        $this->assertArrayHasKey('growth_percentage', $comp);
    }

    public function test_revenue_decomposition_by_category()
    {
        $biService = app(BusinessIntelligenceService::class);

        $order = Order::create([
            'order_number'   => 'ORD-DEC-01',
            'user_id'        => $this->customer->id,
            'total_amount'   => 1200.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->productHeadphones->id,
            'product_name' => $this->productHeadphones->name,
            'price'        => 200.00,
            'qty'          => 1,
            'total'        => 200.00,
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->productLaptop->id,
            'product_name' => $this->productLaptop->name,
            'price'        => 1000.00,
            'qty'          => 1,
            'total'        => 1000.00,
        ]);

        $decomp = $biService->decomposeRevenueByCategory();

        $this->assertEquals(1200.00, $decomp['total_revenue']);
        $this->assertCount(2, $decomp['categories']);
    }

    public function test_scenario_simulation_what_if()
    {
        $biService = app(BusinessIntelligenceService::class);

        // Baseline order of $1000
        Order::create([
            'order_number'   => 'ORD-SIM-01',
            'user_id'        => $this->customer->id,
            'total_amount'   => 1000.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        $sim = $biService->runScenarioSimulation('price_discount', [
            'discount_pct'          => 10,
            'volume_increase_pct'   => 20,
        ]);

        $this->assertEquals('SIMULATION_NOT_GUARANTEED', $sim['type']);
        $this->assertEquals(1000.00, $sim['baseline_revenue']);
        // Projected: 1000 * 0.90 * 1.20 = 1080.00
        $this->assertEquals(1080.00, $sim['projected_revenue']);
        $this->assertEquals(80.00, $sim['projected_delta']);
        $this->assertEquals('Favorable Scenario', $sim['feasibility_verdict']);
    }
}
