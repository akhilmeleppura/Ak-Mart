<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use App\Services\CouponService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedCouponAndPromoSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;
    protected CouponService $couponService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->couponService = app(CouponService::class);

        $cat = Category::create([
            'name'      => 'Electronics',
            'slug'      => 'electronics',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'name'        => 'Wireless Noise-Cancelling Headphones',
            'slug'        => 'wireless-headphones',
            'sku'         => 'W-HEAD-01',
            'price'       => 100.00,
            'qty'         => 20,
            'category_id' => $cat->id,
            'is_active'   => true,
        ]);
    }

    public function test_coupon_service_calculates_discounts_and_identifies_best_coupon()
    {
        // 10% off (on $100 => $10)
        Coupon::create([
            'code'      => 'SAVE10',
            'type'      => 'percentage',
            'value'     => 10.00,
            'is_active' => true,
        ]);

        // 25% off with min spend $50 (on $100 => $25)
        Coupon::create([
            'code'      => 'MEGA25',
            'type'      => 'percentage',
            'value'     => 25.00,
            'min_spend' => 50.00,
            'is_active' => true,
        ]);

        // Flat $30 off with min spend $150 (not eligible for $100)
        Coupon::create([
            'code'      => 'VIP30',
            'type'      => 'fixed',
            'value'     => 30.00,
            'min_spend' => 150.00,
            'is_active' => true,
        ]);

        $available = $this->couponService->getAvailableCoupons(100.00);
        $this->assertCount(3, $available);

        $best = $this->couponService->getBestCoupon(100.00);
        $this->assertNotNull($best);
        $this->assertEquals('MEGA25', $best['code']);
        $this->assertEquals(25.00, $best['discount_amount']);
        $this->assertTrue($best['is_eligible']);

        // Check locked status for VIP30 on $100 subtotal
        $vip = $available->firstWhere('code', 'VIP30');
        $this->assertFalse($vip['is_eligible']);
        $this->assertStringContainsString('Add $50.00 more', $vip['reason']);
        $this->assertEquals(67, $vip['progress_pct']); // 100/150 = 66.67%
    }

    public function test_available_coupons_endpoint_returns_live_cart_eligibility()
    {
        Coupon::create([
            'code'      => 'WELCOME5',
            'type'      => 'fixed',
            'value'     => 5.00,
            'is_active' => true,
        ]);

        // Put item in cart (subtotal = $100)
        $this->withSession([
            'cart' => [
                $this->product->id => [
                    'id'    => $this->product->id,
                    'name'  => $this->product->name,
                    'price' => 100.00,
                    'qty'   => 1,
                    'image' => null,
                ]
            ]
        ]);

        $res = $this->getJson(route('storefront.coupon.available'));
        $res->assertStatus(200);
        $res->assertJson([
            'success'  => true,
            'subtotal' => 100,
        ]);
        $this->assertEquals('WELCOME5', $res->json('best_coupon.code'));
    }

    public function test_auto_apply_best_coupon_automatically_selects_highest_saving_voucher()
    {
        Coupon::create([
            'code'      => 'TINY5',
            'type'      => 'fixed',
            'value'     => 5.00,
            'is_active' => true,
        ]);

        Coupon::create([
            'code'      => 'SUPER20',
            'type'      => 'percentage',
            'value'     => 20.00, // $20 discount on $100
            'is_active' => true,
        ]);

        $this->withSession([
            'cart' => [
                $this->product->id => [
                    'id'    => $this->product->id,
                    'name'  => $this->product->name,
                    'price' => 100.00,
                    'qty'   => 1,
                    'image' => null,
                ]
            ]
        ]);

        $res = $this->postJson(route('storefront.coupon.auto_apply_best'));
        $res->assertStatus(200);
        $res->assertJson([
            'success'     => true,
            'code'        => 'SUPER20',
            'discount'    => '20.00',
            'final_total' => '80.00',
        ]);

        $this->assertEquals('SUPER20', session('coupon.code'));
        $this->assertEquals(20.00, session('coupon.discount'));
    }

    public function test_coupon_removal_via_ajax_resets_session_cleanly()
    {
        $this->withSession([
            'coupon' => [
                'code'     => 'SUPER20',
                'type'     => 'percentage',
                'value'    => 20.00,
                'discount' => 20.00,
            ],
            'cart' => [
                $this->product->id => [
                    'id'    => $this->product->id,
                    'name'  => $this->product->name,
                    'price' => 100.00,
                    'qty'   => 1,
                    'image' => null,
                ]
            ]
        ]);

        $res = $this->postJson(route('storefront.coupon.remove'));
        $res->assertStatus(200);
        $res->assertJson([
            'success' => true,
            'message' => 'Coupon removed successfully.',
        ]);

        $this->assertNull(session('coupon'));
    }

    public function test_order_tracking_and_printable_tax_invoice()
    {
        $order = \App\Models\Order::create([
            'order_number'    => 'ORD-99887766',
            'order_status'    => 'shipped',
            'total_amount'    => 100.00,
            'payment_status'  => 'paid',
            'payment_method'  => 'card',
        ]);

        \App\Models\OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->product->id,
            'product_name' => $this->product->name,
            'qty'          => 1,
            'price'        => 100.00,
            'total'        => 100.00,
        ]);

        // 1. Order Tracking Page
        $resTrack = $this->get(route('storefront.track', ['order_number' => 'ORD-99887766']));
        $resTrack->assertStatus(200);
        $resTrack->assertSee('ORD-99887766');
        $resTrack->assertSee('In Transit');
        $resTrack->assertSee('BlueDart');

        // 2. Printable Tax Invoice Page
        $resInvoice = $this->get(route('storefront.order.invoice', 'ORD-99887766'));
        $resInvoice->assertStatus(200);
        $resInvoice->assertSee('ORIGINAL TAX INVOICE');
        $resInvoice->assertSee('ORD-99887766');
        $resInvoice->assertSee('Wireless Noise-Cancelling Headphones');
        $resInvoice->assertSee('Total Paid:');
    }
}
