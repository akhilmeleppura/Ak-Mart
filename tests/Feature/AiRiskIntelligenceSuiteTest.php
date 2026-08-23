<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTransaction;
use App\Services\Ai\RiskIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AiRiskIntelligenceSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;
    protected \App\Models\Branch\Branch $branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->branch = \App\Models\Branch\Branch::create([
            'name'      => 'Main Branch',
            'code'      => 'MAIN-01',
            'is_active' => true,
        ]);

        $this->customer = User::factory()->create([
            'email'     => 'regular_shopper@example.com',
            'branch_id' => $this->branch->id,
        ]);
    }

    public function test_normal_order_low_risk_assessment()
    {
        $riskService = app(RiskIntelligenceService::class);

        // Historical orders with average $100
        Order::create([
            'order_number'   => 'ORD-NORM-01',
            'user_id'        => $this->customer->id,
            'total_amount'   => 100.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);
        Order::create([
            'order_number'   => 'ORD-NORM-02',
            'user_id'        => $this->customer->id,
            'total_amount'   => 120.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        // Current order of $110
        $currentOrder = Order::create([
            'order_number'   => 'ORD-NORM-03',
            'user_id'        => $this->customer->id,
            'total_amount'   => 110.00,
            'payment_status' => 'paid',
            'order_status'   => 'pending',
        ]);

        $risk = $riskService->assessOrderRisk($currentOrder);

        $this->assertEquals(0, $risk['risk_score']);
        $this->assertEquals('Low', $risk['risk_level']);
        $this->assertEquals('Auto-Approve', $risk['recommended_action']);
    }

    public function test_high_risk_order_with_aov_spike_and_payment_failures()
    {
        $riskService = app(RiskIntelligenceService::class);

        // Historical orders with average $50
        Order::create([
            'order_number'   => 'ORD-HIST-01',
            'user_id'        => $this->customer->id,
            'total_amount'   => 50.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);
        Order::create([
            'order_number'   => 'ORD-HIST-02',
            'user_id'        => $this->customer->id,
            'total_amount'   => 50.00,
            'payment_status' => 'paid',
            'order_status'   => 'completed',
        ]);

        // Current anomalous order of $800 (> 16x historical AOV) with failed payment
        $anomalousOrder = Order::create([
            'order_number'   => 'ORD-ANOM-01',
            'user_id'        => $this->customer->id,
            'total_amount'   => 800.00,
            'payment_status' => 'failed',
            'order_status'   => 'pending',
        ]);

        $risk = $riskService->assessOrderRisk($anomalousOrder);

        $this->assertGreaterThanOrEqual(55, $risk['risk_score']);
        $this->assertContains($risk['risk_level'], ['Medium', 'High', 'Critical']);
        $this->assertStringContainsString('Payment status is currently recorded as failed', implode(' ', $risk['signals']));
    }

    public function test_cod_risk_assessment()
    {
        $riskService = app(RiskIntelligenceService::class);

        // 3 past COD orders, 2 cancelled
        Order::create([
            'order_number'   => 'ORD-COD-01',
            'user_id'        => $this->customer->id,
            'total_amount'   => 80.00,
            'payment_method' => 'cod',
            'order_status'   => 'cancelled',
        ]);
        Order::create([
            'order_number'   => 'ORD-COD-02',
            'user_id'        => $this->customer->id,
            'total_amount'   => 90.00,
            'payment_method' => 'cod',
            'order_status'   => 'failed',
        ]);
        Order::create([
            'order_number'   => 'ORD-COD-03',
            'user_id'        => $this->customer->id,
            'total_amount'   => 85.00,
            'payment_method' => 'cod',
            'order_status'   => 'completed',
        ]);

        $codRisk = $riskService->assessCodRisk($this->customer);

        $this->assertEquals('High', $codRisk['risk_level']);
        $this->assertEquals('Require Prepaid Payment', $codRisk['recommended_policy']);
    }

    public function test_payment_anomaly_and_coupon_abuse_detection()
    {
        $riskService = app(RiskIntelligenceService::class);

        // 1. Payment anomaly
        $paymentHealth = $riskService->detectPaymentAnomalies();
        $this->assertArrayHasKey('failure_rate_pct', $paymentHealth);

        // 2. Coupon abuse
        $couponCheck = $riskService->detectCouponAbuse('MEGA50');
        $this->assertEquals('MEGA50', $couponCheck['coupon_code']);
        $this->assertArrayHasKey('abuse_risk', $couponCheck);
    }
}
