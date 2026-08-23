<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Supplier;
use App\Services\Ai\InventoryIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

class AiInventoryIntelligenceSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Category $category;
    protected Product $productFast;
    protected Product $productDead;
    protected Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name'      => 'Electronics',
            'slug'      => 'electronics',
            'is_active' => true,
        ]);

        $this->supplier = Supplier::create([
            'name'  => 'Apex Global Supplies',
            'email' => 'sales@apexsupplies.com',
        ]);

        $this->productFast = Product::create([
            'name'        => 'Fast Moving Wireless Mouse',
            'slug'        => 'fast-moving-wireless-mouse',
            'sku'         => 'MS-FAST-01',
            'price'       => 25.00,
            'qty'         => 20,
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);

        $this->productDead = Product::create([
            'name'        => 'Legacy VGA Cable',
            'slug'        => 'legacy-vga-cable',
            'sku'         => 'CAB-VGA-01',
            'price'       => 10.00,
            'qty'         => 100,
            'category_id' => $this->category->id,
            'is_active'   => true,
        ]);
    }

    public function test_demand_forecasting_multi_horizon()
    {
        $invService = app(InventoryIntelligenceService::class);

        // Create recent paid order with 60 units of productFast
        $order = Order::create([
            'order_number'   => 'ORD-VEL-01',
            'total_amount'   => 1500.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->productFast->id,
            'product_name' => $this->productFast->name,
            'price'        => 25.00,
            'qty'          => 60,
            'total'        => 1500.00,
        ]);

        $forecast = $invService->calculateDemandForecast($this->productFast);

        $this->assertEquals(1.0, $forecast['daily_velocity']); // 60 units / 60 days = 1 unit/day
        $this->assertEquals(7, $forecast['forecast_7d']);
        $this->assertEquals(30, $forecast['forecast_30d']);
        $this->assertEquals(90, $forecast['forecast_90d']);
    }

    public function test_stockout_prediction_and_reorder_point()
    {
        $invService = app(InventoryIntelligenceService::class);

        // 60 units sold in 60 days -> 1 unit/day
        $order = Order::create([
            'order_number'   => 'ORD-VEL-02',
            'total_amount'   => 1500.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->productFast->id,
            'product_name' => $this->productFast->name,
            'price'        => 25.00,
            'qty'          => 60,
            'total'        => 1500.00,
        ]);

        $stockout = $invService->predictStockoutRunway($this->productFast, 7);

        // current_stock is 20, velocity is 1.0 -> runway 20 days
        $this->assertEquals(20, $stockout['current_stock']);
        $this->assertEquals(20, $stockout['runway_days']);
        // reorder point = (1.0 * 7) + 3 = 10
        $this->assertEquals(10, $stockout['reorder_point']);
        $this->assertFalse($stockout['needs_reorder']); // 20 > 10
    }

    public function test_purchase_order_draft_generation()
    {
        $invService = app(InventoryIntelligenceService::class);

        $poDraft = $invService->generatePurchaseOrderDraft($this->productFast, 50);

        $this->assertEquals('draft_pending_manager_approval', $poDraft['status']);
        $this->assertEquals(50, $poDraft['order_quantity']);
        $this->assertEquals('Apex Global Supplies', $poDraft['supplier_name']);
        $this->assertEquals('$750.00', $poDraft['estimated_cost']); // 50 * ($25.00 * 0.6)
    }

    public function test_dead_stock_and_overstock_detection()
    {
        $invService = app(InventoryIntelligenceService::class);

        // productDead has 0 sales in 90 days and qty = 100
        $res = $invService->detectDeadAndOverstock($this->productDead);

        $this->assertEquals('Dead Stock', $res['classification']);
        $this->assertStringContainsString('Zero sales recorded', $res['explanation']);
    }

    public function test_branch_transfer_and_cycle_count_candidates()
    {
        $invService = app(InventoryIntelligenceService::class);

        // 1. Transfer recommendation
        $trans = $invService->recommendBranchStockTransfer($this->productFast, 1, 2, 10);
        $this->assertEquals('draft_pending_manager_approval', $trans['status']);
        $this->assertEquals(10, $trans['recommended_qty']);

        // 2. Cycle count candidates
        $candidates = $invService->prioritizeCycleCountCandidates(5);
        $this->assertGreaterThanOrEqual(1, $candidates->count());
        $this->assertTrue($candidates->contains('id', $this->productDead->id));
    }
}
