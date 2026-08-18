<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Branch\Branch;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\ReturnRequest;
use App\Models\Order;
use App\Models\ExpenseCategory;
use App\Models\Expense;
use App\Models\ImportedProduct;
use App\Models\WorkflowRule;
use App\Models\LoyaltyTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedCommerceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Branch $branchA;
    protected Branch $branchB;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'is_supreme_admin' => 1,
            'is_super_admin'   => 1,
            'user_type'        => 'super_admin'
        ]);

        $this->branchA = Branch::create(['name' => 'Flagship Store', 'code' => 'BR-01', 'is_active' => true]);
        $this->branchB = Branch::create(['name' => 'Metro Express', 'code' => 'BR-02', 'is_active' => true]);
        $this->category = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
    }

    public function test_stock_movement_ledger_tracks_adjustments(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Wireless Headphones',
            'slug'        => 'wireless-headphones',
            'sku'         => 'WH-100',
            'price'       => 99.00,
            'qty'         => 20,
            'is_active'   => true,
            'branch_id'   => $this->branchA->id,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/vendor/inventory/update', [
            'product_id' => $product->id,
            'type'       => 'adjustment',
            'qty'        => 5,
            'reason'     => 'Found extra units in stockroom',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals(25, $product->fresh()->qty);

        $movement = StockMovement::where('product_id', $product->id)->latest()->first();
        $this->assertNotNull($movement);
        $this->assertEquals('adjustment', $movement->type);
        $this->assertEquals(20, $movement->before_qty);
        $this->assertEquals(25, $movement->after_qty);
    }

    public function test_inter_branch_stock_transfer_workflow(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Gaming Mouse',
            'slug'        => 'gaming-mouse',
            'sku'         => 'GM-50',
            'price'       => 45.00,
            'qty'         => 30,
            'is_active'   => true,
            'branch_id'   => $this->branchA->id,
        ]);

        // 1. Create transfer from Branch A to Branch B
        $response = $this->actingAs($this->admin)->post('/vendor/inventory/transfer', [
            'from_branch_id' => $this->branchA->id,
            'to_branch_id'   => $this->branchB->id,
            'items'          => [
                ['product_id' => $product->id, 'qty' => 10]
            ],
            'notes'          => 'Emergency inventory balance'
        ]);

        $response->assertRedirect();
        $transfer = StockTransfer::latest()->first();
        $this->assertNotNull($transfer);
        $this->assertEquals('in_transit', $transfer->status);

        // 2. Receive transfer at Branch B
        $receiveRes = $this->actingAs($this->admin)->post("/vendor/inventory/transfer/{$transfer->id}/receive");
        $receiveRes->assertRedirect();
        $this->assertEquals('completed', $transfer->fresh()->status);
    }

    public function test_purchase_order_receiving_auto_increments_inventory(): void
    {
        $supplier = Supplier::create([
            'name'         => 'Tech Supplier Corp',
            'company_name' => 'Tech Corp LLC',
            'email'        => 'vendor@tech.com',
            'balance'      => 0,
            'is_active'    => true,
        ]);

        $product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'USB-C Fast Cable',
            'slug'        => 'usbc-fast-cable',
            'sku'         => 'CBL-99',
            'price'       => 15.00,
            'qty'         => 10,
            'is_active'   => true,
        ]);

        $po = PurchaseOrder::create([
            'po_number'    => 'PO-TEST-001',
            'supplier_id'  => $supplier->id,
            'total_amount' => 100.00,
            'status'       => 'ordered',
        ]);

        $po->items()->create([
            'product_id'        => $product->id,
            'quantity'          => 20,
            'received_quantity' => 0,
            'unit_cost'         => 5.00,
            'subtotal'          => 100.00,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/purchases/{$po->id}/received");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertEquals('received', $po->fresh()->status);
        $this->assertEquals(30, $product->fresh()->qty);
    }

    public function test_return_request_resolution_and_restocking(): void
    {
        $product = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Bluetooth Speaker',
            'slug'        => 'bluetooth-speaker',
            'sku'         => 'SPK-22',
            'price'       => 60.00,
            'qty'         => 5,
            'is_active'   => true,
        ]);

        $order = Order::create([
            'order_number'   => 'ORD-RET-01',
            'user_id'        => $this->admin->id,
            'total_amount'   => 60.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
            'payment_method' => 'card',
        ]);

        $order->items()->create([
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'qty'          => 2,
            'price'        => 30.00,
            'unit_price'   => 30.00,
            'total'        => 60.00,
            'total_price'  => 60.00,
        ]);

        $returnReq = ReturnRequest::create([
            'order_id'      => $order->id,
            'branch_id'     => $this->branchA->id,
            'reason'        => 'Customer changed mind',
            'status'        => 'pending',
            'refund_amount' => 60.00,
        ]);

        $response = $this->actingAs($this->admin)->post("/vendor/returns/{$returnReq->id}", [
            'status'        => 'refunded',
            'refund_amount' => 60.00,
            'restock_items' => 1,
            'action_notes'  => 'Item inspected and restored to inventory',
        ]);

        $response->assertRedirect();
        $this->assertEquals('refunded', $returnReq->fresh()->status);
        $this->assertEquals(7, $product->fresh()->qty);
    }

    public function test_expense_management(): void
    {
        $cat = ExpenseCategory::create(['name' => 'Store Utilities', 'slug' => 'store-utilities']);

        $response = $this->actingAs($this->admin)->post('/expenses', [
            'title'               => 'Electric Bill March',
            'amount'              => 120.50,
            'expense_category_id' => $cat->id,
            'expense_date'        => now()->format('Y-m-d'),
            'payment_method'      => 'bank_transfer',
            'reference_no'        => 'EB-101',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('expenses', [
            'title'  => 'Electric Bill March',
            'amount' => 120.50,
        ]);
    }

    public function test_catalog_health_scanner_and_duplicate_detector(): void
    {
        Product::create([
            'category_id' => $this->category->id,
            'name'        => 'Healthy Product',
            'slug'        => 'healthy-product',
            'sku'         => 'HLT-01',
            'barcode'     => '123456789012',
            'price'       => 50.00,
            'qty'         => 15,
            'meta_title'  => 'Healthy Product Title',
            'meta_description' => 'Healthy Description for SEO',
            'is_active'   => true,
        ]);

        $response = $this->actingAs($this->admin)->get('/catalog/scanner');
        $response->assertStatus(200);

        $dupRes = $this->actingAs($this->admin)->get('/catalog/duplicates');
        $dupRes->assertStatus(200);
    }

    public function test_smart_product_importer_staging_and_publishing(): void
    {
        $staging = ImportedProduct::create([
            'source_type' => 'file',
            'data'        => [
                'name'        => 'Staging Smartwatch Pro',
                'sku'         => 'WATCH-99',
                'price'       => 199.00,
                'qty'         => 12,
                'description' => 'Modern fitness tracker with OLED display',
            ],
            'status'      => 'draft',
            'user_id'     => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin)->post("/catalog/importer/review/{$staging->id}/publish", [
            'name'        => 'Staging Smartwatch Pro',
            'price'       => 199.00,
            'sku'         => 'WATCH-99',
            'category_id' => $this->category->id,
            'qty'         => 12,
            'description' => 'Modern fitness tracker with OLED display',
        ]);

        $response->assertRedirect();
        $this->assertEquals('published', $staging->fresh()->status);
        $this->assertDatabaseHas('products', ['name' => 'Staging Smartwatch Pro']);
    }

    public function test_ai_tools_content_generator_and_optimizer(): void
    {
        $response = $this->actingAs($this->admin)->postJson('/ai/product/generate', [
            'title'    => 'Noise Cancelling Headphones',
            'category' => 'Audio',
            'brand'    => 'Sony',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => ['description', 'short_description', 'bullet_points', 'meta_title', 'meta_description', 'tags', 'alt_text']
        ]);

        $optRes = $this->actingAs($this->admin)->postJson('/ai/product/optimize', [
            'title'       => 'Headphones',
            'price'       => 50.00,
            'description' => 'A great audio device.',
            'sku'         => 'HP-1',
        ]);

        $optRes->assertStatus(200);
        $optRes->assertJsonStructure(['success', 'score', 'rating', 'suggestions']);
    }

    public function test_storefront_api_v1_catalog_and_orders(): void
    {
        $prod = Product::create([
            'category_id' => $this->category->id,
            'name'        => 'API Product Test',
            'slug'        => 'api-product-test',
            'sku'         => 'API-01',
            'price'       => 40.00,
            'qty'         => 10,
            'is_active'   => true,
        ]);

        // 1. Get products list
        $resList = $this->getJson('/api/v1/products');
        $resList->assertStatus(200);
        $resList->assertJsonStructure(['success', 'data', 'meta']);

        // 2. Get product details
        $resDetail = $this->getJson("/api/v1/products/{$prod->id}");
        $resDetail->assertStatus(200);
        $resDetail->assertJson(['success' => true]);

        // 3. Place order via API
        $resOrder = $this->postJson('/api/v1/orders', [
            'items'            => [
                ['product_id' => $prod->id, 'qty' => 2]
            ],
            'total_amount'     => 80.00,
            'payment_method'   => 'cod',
            'customer_name'    => 'API Customer',
            'customer_email'   => 'api_customer@akmart.com',
            'customer_phone'   => '+1234567890',
            'shipping_address' => '123 Market Street, City',
        ]);

        $resOrder->assertStatus(201);
        $resOrder->assertJson(['success' => true]);
        $this->assertEquals(8, $prod->fresh()->qty);
    }
}
