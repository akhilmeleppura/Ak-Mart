<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use App\Models\StockCount;
use App\Models\StockCountItem;
use App\Models\B2bCompany;
use App\Models\B2bQuote;
use App\Models\Order;
use App\Models\FulfillmentOrder;
use App\Models\GiftCard;
use App\Models\StoreCredit;
use App\Models\PosRegisterSession;
use App\Models\AbandonedCart;
use App\Models\WebhookSubscription;
use App\Models\Backup;
use App\Services\InventoryService;
use App\Services\FinanceService;
use App\Services\ProductFeedService;
use App\Services\SystemHealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NextGenCommerceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branch;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'is_supreme_admin' => 1,
            'is_super_admin'   => 1,
            'user_type'        => 'super_admin'
        ]);

        $this->branch = Branch::create(['name' => 'Main Branch', 'code' => 'MB-01', 'is_active' => true]);
        $this->category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Pro Laptop 15',
            'slug'        => 'pro-laptop-15',
            'sku'         => 'LAP-15',
            'price'       => 1200.00,
            'qty'         => 20,
            'is_active'   => true,
            'branch_id'   => $this->branch->id,
        ]);
    }

    public function test_multi_warehouse_allocation_and_stock_reservation(): void
    {
        $warehouse = Warehouse::create([
            'code'      => 'WH-TEST-01',
            'name'      => 'Test Regional Hub',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post("/inventory/warehouses/{$warehouse->id}/stock", [
            'product_id'   => $this->product->id,
            'qty'          => 15,
            'bin_location' => 'AISLE-A1',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('warehouse_stocks', [
            'warehouse_id' => $warehouse->id,
            'product_id'   => $this->product->id,
            'qty'          => 15,
        ]);

        $invService = app(InventoryService::class);
        $reservation = $invService->reserveStock($this->product->id, 3, null, 'sess_123', $warehouse->id);

        $this->assertNotNull($reservation);
        $this->assertEquals(3, $reservation->qty);
        $this->assertEquals('active', $reservation->status);
    }

    public function test_cycle_counting_and_reconciliation(): void
    {
        $warehouse = Warehouse::create([
            'code'      => 'WH-COUNT-01',
            'name'      => 'Audit Warehouse',
            'is_active' => true,
        ]);

        // 1. Initialize count
        $response = $this->actingAs($this->admin)->post('/inventory/stock-counts', [
            'type'         => 'cycle',
            'warehouse_id' => $warehouse->id,
            'notes'        => 'Quarterly Audit',
        ]);

        $response->assertRedirect();
        $count = StockCount::latest()->first();
        $this->assertNotNull($count);

        $item = $count->items()->first();
        $this->assertNotNull($item);

        // 2. Update item count
        $itemRes = $this->actingAs($this->admin)->postJson("/inventory/stock-counts/{$count->id}/item/{$item->id}", [
            'counted_qty' => 18, // 2 less than 20
            'remarks'     => '2 damaged units discarded',
        ]);

        $itemRes->assertStatus(200);
        $itemRes->assertJson(['success' => true, 'difference' => -2]);

        // 3. Reconcile
        $recRes = $this->actingAs($this->admin)->post("/inventory/stock-counts/{$count->id}/reconcile");
        $recRes->assertRedirect();

        $this->assertEquals('reconciled', $count->fresh()->status);
        $this->assertEquals(18, $this->product->fresh()->qty);
    }

    public function test_abc_inventory_analysis(): void
    {
        $response = $this->actingAs($this->admin)->get('/inventory/abc-analysis');
        $response->assertStatus(200);
    }

    public function test_b2b_companies_and_tier_pricing(): void
    {
        $response = $this->actingAs($this->admin)->post('/b2b/companies', [
            'name'          => 'Global Tech Supplies',
            'contact_email' => 'tech@global.com',
            'credit_limit'  => 20000.00,
            'payment_terms' => 'net_30',
        ]);

        $response->assertRedirect();
        $company = B2bCompany::where('name', 'Global Tech Supplies')->first();
        $this->assertNotNull($company);

        // Add Wholesale Tier Price
        $tierRes = $this->actingAs($this->admin)->post("/b2b/companies/{$company->id}/tier-price", [
            'product_id' => $this->product->id,
            'min_qty'    => 10,
            'unit_price' => 950.00,
        ]);

        $tierRes->assertRedirect();
        $this->assertDatabaseHas('b2b_tier_prices', [
            'b2b_company_id' => $company->id,
            'product_id'     => $this->product->id,
            'unit_price'     => 950.00,
        ]);
    }

    public function test_b2b_quotes_workflow(): void
    {
        $company = B2bCompany::create([
            'name'          => 'Direct Wholesale Ltd',
            'company_code'  => 'B2B-DW-01',
            'contact_email' => 'wholesale@direct.com',
            'payment_terms' => 'net_30',
        ]);

        $response = $this->actingAs($this->admin)->post('/b2b/quotes', [
            'b2b_company_id'     => $company->id,
            'items'              => [
                ['product_id' => $this->product->id, 'qty' => 5]
            ],
            'discount'           => 10,
            'notes'              => 'Discount applied for cash upfront',
        ]);

        $response->assertRedirect();
        $quote = B2bQuote::latest()->first();
        $this->assertNotNull($quote);
        $this->assertEquals(5400.00, (float)$quote->total); // (5 * 1200) - 10% = 6000 - 600 = 5400

        // Approve quote
        $statusRes = $this->actingAs($this->admin)->post("/b2b/quotes/{$quote->id}/status", [
            'status' => 'approved',
        ]);
        $statusRes->assertRedirect();
        $this->assertEquals('approved', $quote->fresh()->status);
    }

    public function test_advanced_fulfillment_order_and_status(): void
    {
        $order = Order::create([
            'order_number'   => 'ORD-FUL-01',
            'user_id'        => $this->admin->id,
            'total_amount'   => 1200.00,
            'payment_status' => 'paid',
            'order_status'   => 'processing',
        ]);

        $orderItem = $order->items()->create([
            'product_id'   => $this->product->id,
            'product_name' => $this->product->name,
            'qty'          => 1,
            'price'        => 1200.00,
            'unit_price'   => 1200.00,
            'total'        => 1200.00,
            'total_price'  => 1200.00,
        ]);

        $response = $this->actingAs($this->admin)->post('/fulfillment', [
            'order_id' => $order->id,
            'items'    => [
                ['order_item_id' => $orderItem->id, 'qty' => 1]
            ],
        ]);

        $response->assertRedirect();
        $fulfillment = FulfillmentOrder::latest()->first();
        $this->assertNotNull($fulfillment);

        // Update shipping tracking
        $statusRes = $this->actingAs($this->admin)->post("/fulfillment/{$fulfillment->id}/status", [
            'status'           => 'shipped',
            'shipping_carrier' => 'DHL Express',
            'tracking_number'  => 'DHL-992211',
        ]);

        $statusRes->assertRedirect();
        $this->assertEquals('shipped', $fulfillment->fresh()->status);
        $this->assertEquals('DHL-992211', $fulfillment->fresh()->tracking_number);
    }

    public function test_customer_portal_wishlist_and_saved_cart(): void
    {
        $response = $this->actingAs($this->admin)->get('/customer/portal');
        $response->assertStatus(200);

        // Toggle Wishlist
        $wishRes = $this->actingAs($this->admin)->postJson('/customer/wishlist/toggle', [
            'product_id' => $this->product->id,
        ]);
        $wishRes->assertStatus(200);
        $wishRes->assertJson(['success' => true, 'action' => 'added']);

        // Save Cart
        $cartRes = $this->actingAs($this->admin)->postJson('/customer/saved-cart', [
            'name'      => 'My Workstation Cart',
            'cart_data' => [['id' => $this->product->id, 'qty' => 1]],
        ]);
        $cartRes->assertStatus(200);
        $cartRes->assertJson(['success' => true]);
    }

    public function test_gift_cards_and_store_credits(): void
    {
        // 1. Issue Gift Card
        $response = $this->actingAs($this->admin)->post('/gift-cards', [
            'amount'          => 75.00,
            'recipient_email' => 'gift@akmart.com',
            'expiry_days'     => 180,
        ]);

        $response->assertRedirect();
        $gc = GiftCard::latest()->first();
        $this->assertNotNull($gc);
        $this->assertEquals(75.00, (float)$gc->current_balance);

        // 2. Lookup Gift Card API
        $lookupRes = $this->postJson('/gift-cards/lookup', ['code' => $gc->code]);
        $lookupRes->assertStatus(200);
        $lookupRes->assertJson(['valid' => true, 'code' => $gc->code]);

        // 3. Store Credit
        $storeCredit = StoreCredit::create(['user_id' => $this->admin->id, 'balance' => 0]);
        $storeCredit->credit(50.00, 'refund', null, 'Goodwill credit');
        $this->assertEquals(50.00, $storeCredit->fresh()->balance);

        $storeCredit->debit(20.00, 'purchase', null, 'Applied to order');
        $this->assertEquals(30.00, $storeCredit->fresh()->balance);
    }

    public function test_pos_shift_register_open_and_close(): void
    {
        // 1. Open shift
        $response = $this->actingAs($this->admin)->post('/finance/pos-register/open', [
            'opening_amount' => 150.00,
        ]);

        $response->assertRedirect();
        $session = PosRegisterSession::where('user_id', $this->admin->id)->where('status', 'open')->first();
        $this->assertNotNull($session);

        // 2. Close shift
        $closeRes = $this->actingAs($this->admin)->post('/finance/pos-register/close', [
            'session_id'     => $session->id,
            'closing_amount' => 150.00,
            'notes'          => 'Exact cash matched',
        ]);

        $closeRes->assertRedirect();
        $this->assertEquals('closed', $session->fresh()->status);
        $this->assertEquals(0, $session->fresh()->difference);
    }

    public function test_omnichannel_product_feeds(): void
    {
        $feedService = app(ProductFeedService::class);

        $xml = $feedService->generateGoogleShoppingXml();
        $this->assertStringContainsString('<rss', $xml);
        $this->assertStringContainsString('Pro Laptop 15', $xml);

        $csv = $feedService->generateMetaCatalogCsv();
        $this->assertStringContainsString('Pro Laptop 15', $csv);

        $json = $feedService->generateTikTokCatalogJson();
        $this->assertStringContainsString('Pro Laptop 15', $json);
    }

    public function test_developer_webhooks_and_system_health(): void
    {
        // 1. Webhook Subscription
        $response = $this->actingAs($this->admin)->post('/developer/webhooks', [
            'name'       => 'Test Webhook Endpoint',
            'target_url' => 'https://example.com/webhook',
            'events'     => ['order.created', 'order.paid'],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('webhook_subscriptions', ['name' => 'Test Webhook Endpoint']);

        // 2. System Health Diagnostics
        $healthService = app(SystemHealthService::class);
        $diag = $healthService->runDiagnostics();
        $this->assertArrayHasKey('score', $diag);
        $this->assertArrayHasKey('checks', $diag);
        $this->assertEquals('healthy', $diag['checks']['database']['status']);

        // 3. Backup Snapshot
        $backupRes = $this->actingAs($this->admin)->post('/system/backups/create', [
            'type' => 'database',
        ]);
        $backupRes->assertRedirect();
        $this->assertDatabaseHas('backups', ['type' => 'database']);
    }
}
