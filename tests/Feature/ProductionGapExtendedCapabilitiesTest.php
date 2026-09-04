<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Order;
use App\Models\ProductSlugHistory;
use App\Models\LoyaltyTier;
use App\Models\Branch\Branch;
use App\Services\LastMileDeliveryService;
use App\Services\CustomerCrmService;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductionGapExtendedCapabilitiesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $driver;
    protected User $customer;
    protected Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = Branch::firstOrCreate(['id' => 1], [
            'name' => 'Main Flagship Branch',
            'code' => 'MAIN',
        ]);

        $this->admin = User::factory()->create([
            'user_type' => 'admin',
            'branch_id' => $this->branch->id,
        ]);

        $this->driver = User::factory()->create([
            'user_type' => 'staff',
            'role'      => 'driver',
            'branch_id' => $this->branch->id,
        ]);

        $this->customer = User::factory()->create([
            'user_type' => 'customer',
            'branch_id' => $this->branch->id,
        ]);
    }

    /**
     * Test Last-Mile Driver Assignment, OTP generation, and Proof of Delivery
     */
    public function test_last_mile_delivery_driver_assignment_and_otp_proof_verification(): void
    {
        $delivery = app(LastMileDeliveryService::class);

        $order = Order::create([
            'order_number' => 'ORD-DEL-001',
            'user_id'      => $this->customer->id,
            'total_amount' => 75.00,
            'order_status' => 'packed',
            'branch_id'    => $this->branch->id,
        ]);

        // 1. Assign Driver
        $order = $delivery->assignDriver($order, $this->driver->id, $this->admin->id);
        $this->assertEquals('out_for_delivery', $order->order_status);
        $this->assertEquals($this->driver->id, $order->driver_id);
        $this->assertNotEmpty($order->delivery_otp);

        $otp = $order->delivery_otp;

        // 2. Complete delivery with valid OTP, signature, and photo
        $order = $delivery->verifyAndCompleteDelivery(
            $order,
            $otp,
            'https://storage.example.com/signatures/ord-001.png',
            'https://storage.example.com/photos/ord-001.jpg',
            $this->driver->id
        );

        $this->assertEquals('delivered', $order->order_status);
        $this->assertEquals('https://storage.example.com/signatures/ord-001.png', $order->delivery_signature_url);
        $this->assertEquals('https://storage.example.com/photos/ord-001.jpg', $order->delivery_proof_photo_url);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id'    => $order->id,
            'from_status' => 'out_for_delivery',
            'to_status'   => 'delivered',
            'actor_type'  => 'driver',
        ]);
    }

    /**
     * Test Last-Mile Delivery Failure and Attempts tracking
     */
    public function test_last_mile_delivery_failure_attempts(): void
    {
        $delivery = app(LastMileDeliveryService::class);

        $order = Order::create([
            'order_number' => 'ORD-DEL-002',
            'user_id'      => $this->customer->id,
            'total_amount' => 50.00,
            'order_status' => 'out_for_delivery',
            'delivery_otp' => '445566',
            'branch_id'    => $this->branch->id,
        ]);

        $order = $delivery->recordDeliveryFailure($order, 'CUSTOMER_UNAVAILABLE', $this->driver->id, 'Gate was locked, no answer to phone');

        $this->assertEquals('failed', $order->order_status);
        $this->assertEquals('CUSTOMER_UNAVAILABLE', $order->delivery_failed_reason);
        $this->assertEquals(1, $order->delivery_attempts);
        $this->assertStringContainsString('Gate was locked', $order->internal_notes);
    }

    /**
     * Test Product 301 Redirect for renamed product slugs (SEO)
     */
    public function test_product_slug_history_301_redirect(): void
    {
        $product = Product::factory()->create([
            'name'      => 'Organic Almond Milk 1L',
            'slug'      => 'organic-almond-milk-1l-new',
            'is_active' => true,
        ]);

        ProductSlugHistory::create([
            'product_id' => $product->id,
            'slug'       => 'organic-almond-milk-1l-old',
        ]);

        $response = $this->get('/store/product/organic-almond-milk-1l-old');
        $response->assertStatus(301);
        $response->assertRedirect(route('storefront.product', $product->slug));
    }

    /**
     * Test Automated Scheduled Commands
     */
    public function test_scheduled_commands_execution(): void
    {
        // 1. reservations:cleanup
        $resCode = Artisan::call('reservations:cleanup');
        $this->assertEquals(0, $resCode);

        // 2. batches:check-expiry
        $batchCode = Artisan::call('batches:check-expiry', ['--days' => 30]);
        $this->assertEquals(0, $batchCode);

        // 3. crm:recalculate-rfm
        $crmCode = Artisan::call('crm:recalculate-rfm');
        $this->assertEquals(0, $crmCode);
    }
}
