<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\GiftCard;
use App\Models\StoreCredit;
use App\Models\B2bCompany;
use App\Models\B2bBuyer;
use App\Models\StockMovement;
use App\Services\OrderService;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommerceRegressionAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer1;
    protected User $customer2;
    protected Branch $branch1;
    protected Branch $branch2;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name'             => 'Supreme Admin',
            'email'            => 'supreme@ak-mart.com',
            'is_supreme_admin' => 1,
            'is_super_admin'   => 1,
            'user_type'        => 'super_admin'
        ]);

        $this->customer1 = User::factory()->create(['name' => 'Alice Customer', 'email' => 'alice@test.com', 'user_type' => 'customer']);
        $this->customer2 = User::factory()->create(['name' => 'Bob Customer', 'email' => 'bob@test.com', 'user_type' => 'customer']);

        $this->branch1 = Branch::create(['name' => 'London Hub', 'code' => 'LON-01', 'is_active' => true]);
        $this->branch2 = Branch::create(['name' => 'Manchester Hub', 'code' => 'MAN-02', 'is_active' => true]);

        $this->category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);

        $this->product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'High Performance Laptop',
            'slug'        => 'high-performance-laptop',
            'sku'         => 'HPL-2026',
            'price'       => 1000.00,
            'qty'         => 50,
            'is_active'   => true,
            'branch_id'   => $this->branch1->id,
        ]);
    }

    /**
     * Test 1: Product validation prevents invalid negative prices and enforces SKU uniqueness
     */
    public function test_product_validation_prevents_negative_price_and_duplicate_sku(): void
    {
        // Negative price attempt
        $resNegative = $this->actingAs($this->admin)->post('/products/create', [
            'productTitle' => 'Invalid Price Product',
            'category_id'  => $this->category->id,
            'productSku'   => 'INV-PRICE',
            'productPrice' => -50.00,
            'quantity'     => 10,
        ]);
        $resNegative->assertSessionHasErrors(['productPrice']);

        // Duplicate SKU attempt
        $resDuplicate = $this->actingAs($this->admin)->post('/products/create', [
            'productTitle' => 'Duplicate SKU Product',
            'category_id'  => $this->category->id,
            'productSku'   => 'HPL-2026', // Already exists
            'productPrice' => 500.00,
            'quantity'     => 10,
        ]);
        $resDuplicate->assertSessionHasErrors(['productSku']);
    }

    /**
     * Test 2: Customer IDOR protection on order view
     */
    public function test_customer_idor_order_protection(): void
    {
        $order = Order::create([
            'order_number'   => 'ORD-ALICE-100',
            'user_id'        => $this->customer1->id,
            'total_amount'   => 1000.00,
            'payment_status' => 'paid',
            'order_status'   => 'processing',
        ]);

        // Customer 1 can view own portal
        $resAlice = $this->actingAs($this->customer1)->get("/customer/portal");
        $resAlice->assertStatus(200);

        // Customer 2 attempting to view Customer 1's order via direct API
        $resBob = $this->actingAs($this->customer2)->getJson("/api/v1/orders/{$order->order_number}");
        $resBob->assertStatus(403);
    }

    /**
     * Test 3: Coupon validation endpoint
     */
    public function test_coupon_validation_and_calculation(): void
    {
        $coupon = Coupon::create([
            'code'        => 'SAVE20',
            'type'        => 'percentage',
            'value'       => 20.00,
            'min_spend'   => 100.00,
            'is_active'   => true,
            'start_date'  => now()->subDay(),
            'end_date'    => now()->addDays(10),
        ]);

        $response = $this->postJson('/api/v1/coupons/validate', [
            'code'      => 'SAVE20',
            'subtotal'  => 1000.00,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'valid'           => true,
            'discount_amount' => 200.00,
            'new_total'       => 800.00,
        ]);
    }

    /**
     * Test 4: Purchase order receiving incrementation
     */
    public function test_purchase_order_partial_and_full_receiving(): void
    {
        $supplier = Supplier::create(['name' => 'Global Components Inc', 'email' => 'sales@globalcomp.com']);
        $po = PurchaseOrder::create([
            'po_number'    => 'PO-TEST-88',
            'supplier_id'  => $supplier->id,
            'branch_id'    => $this->branch1->id,
            'status'       => 'ordered',
            'total_amount' => 5000.00,
        ]);

        $item = $po->items()->create([
            'product_id' => $this->product->id,
            'quantity'   => 10,
            'unit_cost'  => 500.00,
            'subtotal'   => 5000.00,
        ]);

        $initialStock = $this->product->qty; // 50

        // Receive PO
        $response = $this->actingAs($this->admin)->postJson("/purchases/{$po->id}/receive");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals($initialStock + 10, $this->product->fresh()->qty);
        $this->assertEquals('received', $po->fresh()->status);
    }

    /**
     * Test 5: Gift Card & Store Credit negative balance prevention
     */
    public function test_gift_card_and_store_credit_balance_constraints(): void
    {
        $gc = GiftCard::create([
            'code'            => 'GC-AUDIT-50',
            'initial_balance' => 50.00,
            'current_balance' => 50.00,
            'currency'        => 'USD',
            'is_active'       => true,
        ]);

        $this->assertTrue($gc->deduct(30.00));
        $this->assertEquals(20.00, (float)$gc->fresh()->current_balance);

        // Attempt to deduct more than remaining balance
        $this->assertFalse($gc->deduct(50.00));
        $this->assertEquals(20.00, (float)$gc->fresh()->current_balance);

        // Store credit debit constraint
        $storeCredit = StoreCredit::create(['user_id' => $this->customer1->id, 'balance' => 40.00]);
        $this->assertNotNull($storeCredit->debit(30.00, 'test', null, 'Order debit'));
        $this->assertEquals(10.00, (float)$storeCredit->fresh()->balance);

        $this->assertNull($storeCredit->debit(50.00, 'test', null, 'Excess debit'));
        $this->assertEquals(10.00, (float)$storeCredit->fresh()->balance);
    }

    /**
     * Test 6: Smart URL Product Importer handles invalid/offline URLs gracefully
     */
    public function test_smart_url_product_importer_offline_fallback(): void
    {
        $response = $this->actingAs($this->admin)->post('/catalog/importer/url', [
            'product_url' => 'https://invalid-nonexistent-domain-xyz987.com/product/123'
        ]);

        // Must handle failure gracefully via redirect with error message
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /**
     * Test 7: AI Copilot Product Content Generator offline fallback
     */
    public function test_ai_tools_offline_deterministic_fallback(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/ai/product/generate', [
            'name'     => 'Wireless Noise Cancelling Headphones',
            'category' => 'Audio',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertNotEmpty($response->json('data.description'));
    }

    /**
     * Test 8: Supreme Admin global bypass on policies and gates
     */
    public function test_supreme_admin_universal_gate_bypass(): void
    {
        $this->actingAs($this->admin);
        $this->assertTrue(\Illuminate\Support\Facades\Gate::allows('manage-system'));
        $this->assertTrue(\Illuminate\Support\Facades\Gate::allows('view-confidential-financials'));
        $this->assertTrue(\Illuminate\Support\Facades\Gate::allows('delete-any-branch'));
    }

    /**
     * Test 9: POS Terminal checkout atomic stock deduction
     */
    public function test_pos_checkout_atomic_execution(): void
    {
        $initialStock = $this->product->qty; // 50

        $response = $this->actingAs($this->admin)->postJson('/vendor/pos/checkout', [
            'customer_id'    => $this->customer1->id,
            'items'          => [
                ['id' => $this->product->id, 'qty' => 3, 'price' => 1000.00]
            ],
            'total'          => 3000.00,
            'payment_method' => 'cash',
            'amount_paid'    => 3000.00,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Stock decreased by exactly 3
        $this->assertEquals($initialStock - 3, $this->product->fresh()->qty);

        // StockMovement recorded
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type'       => 'sale',
            'quantity'   => -3,
        ]);
    }

    /**
     * Test 10: Workflow automation index screen renders without Str class error
     */
    public function test_workflow_automation_screen_renders_cleanly(): void
    {
        // Seed a workflow rule to ensure Blade loop executes line 52
        \App\Models\WorkflowRule::create([
            'name'          => 'Low Stock Alert Rule',
            'trigger_event' => 'low_stock',
            'conditions'    => ['field' => 'qty', 'operator' => '<', 'value' => 5],
            'actions'       => ['type' => 'Notification', 'message' => 'Product stock is running low please replenish immediately'],
            'is_active'     => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/automation');
        $response->assertStatus(200);
        $response->assertSee('Low Stock Alert Rule');
    }

    /**
     * Test 11: Notification Hub mark all as read route executes cleanly
     */
    public function test_notifications_mark_all_as_read(): void
    {
        $response = $this->actingAs($this->admin)->post('/notifications/mark-all');
        $response->assertRedirect();
        $response->assertSessionHas('success', 'All notifications marked as read.');
    }
}
