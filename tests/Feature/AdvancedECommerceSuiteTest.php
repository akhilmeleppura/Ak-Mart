<?php

namespace Tests\Feature;

use App\Models\B2bCompany;
use App\Models\B2bTierPrice;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\GiftCard;
use App\Models\Product;
use App\Models\StoreCredit;
use App\Models\User;
use App\Services\CommunicationService;
use App\Services\PricingEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedECommerceSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected Product $product;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'supreme_admin@akmart.com',
            'is_supreme_admin' => 1,
        ]);

        $this->customer = User::factory()->create([
            'email' => 'customer@test.com',
            'phone' => '9876543210',
            'marketing_opt_out' => false,
        ]);

        $this->category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name'             => 'Pro Wireless Bluetooth Earbuds with ANC',
            'slug'             => 'pro-wireless-bluetooth-earbuds',
            'category_id'      => $this->category->id,
            'price'            => 1000.00,
            'compare_at_price' => 1500.00,
            'qty'              => 100,
            'sku'              => 'AKM-AUDIO-101',
            'barcode'          => '8901234567890',
            'brand'            => 'AK-Audio',
            'description'      => 'High-definition sound with active noise cancellation, 30 hours battery backup, IPX5 water resistance, and fast charging support.',
            'image'            => 'assets/img/products/earbuds.jpg',
            'meta_title'       => 'Pro Wireless Earbuds - Buy Online',
            'meta_description' => 'Get the best deal on ANC Pro wireless earbuds with 30h battery.',
            'is_active'        => true,
        ]);
    }

    /**
     * Test 1: Zero-trust server side calculation of pricing, tax, discount, gift card & store credit
     */
    public function test_pricing_engine_server_side_recalculation(): void
    {
        $engine = new PricingEngine();

        $coupon = Coupon::create([
            'code'      => 'SAVE10',
            'type'      => 'percentage',
            'value'     => 10,
            'is_active' => true,
        ]);

        $giftCard = GiftCard::create([
            'code'            => 'GC-TEST-50',
            'initial_balance' => 50.00,
            'current_balance' => 50.00,
            'is_active'       => true,
        ]);

        $storeCredit = StoreCredit::create([
            'user_id' => $this->customer->id,
            'balance' => 200.00,
        ]);

        $result = $engine->calculateCart(
            cartItems: [['product_id' => $this->product->id, 'qty' => 2]], // Subtotal: 2000.00
            couponCode: 'SAVE10', // 10% off -> -200.00 -> 1800.00 taxable
            shippingRate: 50.00,
            taxPercent: 18.00, // 18% of 1800 -> 324.00 tax (162 CGST, 162 SGST)
            storeCreditApplied: 100.00,
            giftCardCode: 'GC-TEST-50' // -50.00
        );

        $this->assertEquals(2000.00, $result['subtotal']);
        $this->assertEquals(200.00, $result['discount_amount']);
        $this->assertEquals(1800.00, $result['taxable_amount']);
        $this->assertEquals(324.00, $result['tax_amount']);
        $this->assertEquals(162.00, $result['cgst']);
        $this->assertEquals(162.00, $result['sgst']);
        $this->assertEquals(2174.00, $result['gross_total']); // 1800 + 324 + 50
        $this->assertEquals(50.00, $result['gift_card_deducted']);
        $this->assertEquals(100.00, $result['store_credit_deducted']);
        $this->assertEquals(2024.00, $result['net_payable']); // 2174 - 50 - 100
    }

    /**
     * Test 2: B2B Volume Wholesale tier pricing brackets
     */
    public function test_pricing_engine_b2b_wholesale_volume_tiers(): void
    {
        $engine = new PricingEngine();

        $company = B2bCompany::create([
            'name'          => 'Apex Wholesale Ltd',
            'company_code'  => 'APEX-01',
            'contact_email' => 'sales@apex.com',
            'credit_limit'  => 50000.00,
            'status'        => 'active',
        ]);

        B2bTierPrice::create([
            'b2b_company_id' => $company->id,
            'product_id'     => $this->product->id,
            'min_qty'        => 50,
            'unit_price'     => 750.00, // Regular is 1000
        ]);

        // Below volume minimum -> gets standard price
        $priceLow = $engine->calculateUnitPrice($this->product, 10, $company->id);
        $this->assertEquals(1000.00, $priceLow);

        // At or above 50 units -> gets B2B tier price 750
        $priceHigh = $engine->calculateUnitPrice($this->product, 50, $company->id);
        $this->assertEquals(750.00, $priceHigh);
    }

    /**
     * Test 3: Unified Communication Service sends Email and WhatsApp with variable interpolation
     */
    public function test_communication_service_email_and_whatsapp_dispatch(): void
    {
        $commService = new CommunicationService();

        // Email dispatch test
        $emailLog = $commService->send(
            channel: 'email',
            recipient: $this->customer->email,
            templateCode: 'order_confirmation',
            variables: [
                'customer_name' => 'John Doe',
                'order_number'  => 'ORD-99881',
                'order_total'   => '1,850.00',
            ]
        );

        $this->assertEquals('sent', $emailLog->status);
        $this->assertEquals('email', $emailLog->channel);
        $this->assertStringContainsString('ORD-99881', $emailLog->message_body);
        $this->assertStringContainsString('John Doe', $emailLog->message_body);

        // WhatsApp dispatch test
        $waLog = $commService->send(
            channel: 'whatsapp',
            recipient: '9876543210',
            templateCode: 'order_shipped',
            variables: [
                'customer_name'   => 'John Doe',
                'order_number'    => 'ORD-99881',
                'carrier'         => 'BlueDart Express',
                'tracking_number' => 'BD-77665544',
            ]
        );

        $this->assertEquals('sent', $waLog->status);
        $this->assertEquals('whatsapp', $waLog->channel);
        $this->assertStringContainsString('BD-77665544', $waLog->message_body);
    }

    /**
     * Test 4: Communication service respects marketing opt-out preferences
     */
    public function test_communication_service_respects_opt_out_preferences(): void
    {
        $optedOutUser = User::factory()->create([
            'email'             => 'unsubscribed@test.com',
            'marketing_opt_out' => true,
        ]);

        $commService = new CommunicationService();

        $log = $commService->send(
            channel: 'email',
            recipient: $optedOutUser->email,
            templateCode: 'abandoned_cart',
            variables: ['customer_name' => 'Unsubscribed User'],
            type: 'marketing'
        );

        $this->assertEquals('skipped', $log->status);
        $this->assertStringContainsString('opt-out', $log->message_body);
    }

    /**
     * Test 5: Communication Center web views and campaign dispatch
     */
    public function test_communication_center_ui_and_campaigns(): void
    {
        $response = $this->actingAs($this->admin)->get('/communication');
        $response->assertStatus(200);
        $response->assertSee('Unified Communication Center');

        // Launch campaign
        $campResponse = $this->actingAs($this->admin)->post('/communication/campaigns', [
            'name'            => 'Festive Mega Sale 2026',
            'channel'         => 'omnichannel',
            'audience_type'   => 'all',
            'message_content' => 'Special 20% discount on all catalog items!',
        ]);

        $campResponse->assertRedirect();
        $campResponse->assertSessionHas('success');
        $this->assertDatabaseHas('marketing_campaigns', [
            'name'    => 'Festive Mega Sale 2026',
            'channel' => 'omnichannel',
        ]);
    }

    /**
     * Test 6: Product Listing Quality Score Engine evaluates completeness
     */
    public function test_product_quality_scoring_system(): void
    {
        $score = $this->product->quality_score;
        $this->assertGreaterThanOrEqual(80, $score);

        $breakdown = $this->product->quality_breakdown;
        $this->assertArrayHasKey('title', $breakdown);
        $this->assertArrayHasKey('description', $breakdown);
        $this->assertArrayHasKey('pricing', $breakdown);
        $this->assertArrayHasKey('identifiers', $breakdown);
        $this->assertArrayHasKey('seo', $breakdown);
    }

    /**
     * Test 7: Language Switcher supports Malayalam (ml) and sets session
     */
    public function test_language_switch_to_malayalam_and_others(): void
    {
        $response = $this->get('/lang/ml');
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'ml');

        // Test with Authenticated Request
        $authResponse = $this->actingAs($this->admin)->get('/lang/hi');
        $authResponse->assertRedirect();
        $authResponse->assertSessionHas('locale', 'hi');
    }
}
