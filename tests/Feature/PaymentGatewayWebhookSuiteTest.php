<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Backup;
use App\Models\Branch\Branch;
use App\Services\Payment\PaymentGatewayService;
use App\Services\CommunicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentGatewayWebhookSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
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

        $this->customer = User::factory()->create([
            'user_type' => 'customer',
            'branch_id' => $this->branch->id,
        ]);
    }

    /**
     * 1. Test Gateway Session generation for Stripe, Razorpay, and Sandbox UPI
     */
    public function test_payment_gateway_service_creates_valid_sessions(): void
    {
        $gatewayService = app(PaymentGatewayService::class);

        $order = Order::create([
            'order_number'   => 'ORD-GW-001',
            'user_id'        => $this->customer->id,
            'total_amount'   => 120.50,
            'order_status'   => 'pending',
            'payment_status' => 'pending',
            'branch_id'      => $this->branch->id,
        ]);

        // 1. Stripe
        $stripeSession = $gatewayService->createPaymentSession($order, 'stripe');
        $this->assertEquals('stripe', $stripeSession['gateway']);
        $this->assertNotEmpty($stripeSession['session_id']);
        $this->assertStringContainsString('cs_test_', $stripeSession['session_id']);

        // 2. Razorpay
        $rzpOrder = $gatewayService->createPaymentSession($order, 'razorpay');
        $this->assertEquals('razorpay', $rzpOrder['gateway']);
        $this->assertNotEmpty($rzpOrder['razorpay_order_id']);

        // 3. Sandbox UPI
        $upiIntent = $gatewayService->createPaymentSession($order, 'sandbox_upi');
        $this->assertEquals('sandbox_upi', $upiIntent['gateway']);
        $this->assertNotEmpty($upiIntent['transaction_ref']);
        $this->assertStringContainsString('upi://pay', $upiIntent['upi_intent_url']);
    }

    /**
     * 2. Test Forged Webhook is Rejected with 401
     */
    public function test_forged_webhook_request_is_rejected_with_401(): void
    {
        $response = $this->postJson('/api/payment/webhook', [
            'order_number' => 'ORD-FORGED-999',
            'amount'       => 500.00,
        ], [
            'X-Payment-Gateway' => 'stripe',
            'Stripe-Signature'  => 'invalid_forged_signature_header',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['error' => 'Invalid webhook cryptographic signature.']);
    }

    /**
     * 3. Test Verified Stripe Webhook Reconciles Order to Paid and Processing
     */
    public function test_verified_stripe_webhook_reconciles_order(): void
    {
        $order = Order::create([
            'order_number'   => 'ORD-STRIPE-001',
            'user_id'        => $this->customer->id,
            'total_amount'   => 85.00,
            'order_status'   => 'pending',
            'payment_status' => 'pending',
            'branch_id'      => $this->branch->id,
        ]);

        $secret = 'whsec_test_stripe_secret_key_123';
        config(['services.stripe.webhook_secret' => $secret]);

        $payload = [
            'data' => [
                'object' => [
                    'id'                  => 'evt_test_123',
                    'payment_intent'      => 'pi_test_stripe_9988',
                    'client_reference_id' => $order->order_number,
                    'amount_total'        => 8500, // in cents = $85.00
                    'fee'                 => 250,  // in cents = $2.50
                ]
            ]
        ];

        $rawBody = json_encode($payload);
        $timestamp = time();
        $expectedSignature = hash_hmac('sha256', "{$timestamp}.{$rawBody}", $secret);
        $sigHeader = "t={$timestamp},v1={$expectedSignature}";

        $response = $this->call(
            'POST',
            '/api/payment/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'             => 'application/json',
                'HTTP_X_Payment_Gateway'   => 'stripe',
                'HTTP_Stripe_Signature'    => $sigHeader,
            ],
            $rawBody
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->order_status);

        $this->assertDatabaseHas('payment_reconciliations', [
            'transaction_id' => 'pi_test_stripe_9988',
            'gateway'        => 'stripe',
            'order_id'       => $order->id,
            'status'         => 'captured',
        ]);
    }

    /**
     * 4. Test Verified Razorpay Webhook Reconciles Order
     */
    public function test_verified_razorpay_webhook_reconciles_order(): void
    {
        $order = Order::create([
            'order_number'   => 'ORD-RZP-001',
            'user_id'        => $this->customer->id,
            'total_amount'   => 150.00,
            'order_status'   => 'pending',
            'payment_status' => 'pending',
            'branch_id'      => $this->branch->id,
        ]);

        $secret = 'rzp_sec_test_secret_key_456';
        config(['services.razorpay.webhook_secret' => $secret]);

        $payload = [
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id'     => 'pay_rzp_test_8877',
                        'amount' => 15000, // paise = 150.00
                        'fee'    => 300,
                        'notes'  => ['order_number' => $order->order_number],
                    ]
                ]
            ]
        ];

        $rawBody = json_encode($payload);
        $signature = hash_hmac('sha256', $rawBody, $secret);

        $response = $this->call(
            'POST',
            '/api/payment/webhook',
            [],
            [],
            [],
            [
                'CONTENT_TYPE'                  => 'application/json',
                'HTTP_X_Payment_Gateway'        => 'razorpay',
                'HTTP_X_Razorpay_Signature'     => $signature,
            ],
            $rawBody
        );

        $response->assertOk();

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->order_status);
    }

    /**
     * 5. Test Real Database Backup Creation, Integrity Checksum, Download and Delete
     */
    public function test_real_database_backup_creation_and_download(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('app-backups-create'), ['type' => 'database']);
        $response->assertRedirect(route('app-backups'));

        $backup = Backup::latest()->first();
        $this->assertNotNull($backup);
        $this->assertGreaterThan(0, $backup->file_size);
        $this->assertNotEmpty($backup->checksum);

        $filePath = 'backups/' . $backup->file_name;
        $this->assertTrue(Storage::disk('local')->exists($filePath));

        // Test Download
        $downloadRes = $this->get(route('app-backups-download', $backup->id));
        $downloadRes->assertOk();

        // Test Destroy
        $deleteRes = $this->delete(route('app-backups-destroy', $backup->id));
        $deleteRes->assertRedirect(route('app-backups'));
        $this->assertFalse(Storage::disk('local')->exists($filePath));
    }

    /**
     * 6. Test Communication Service Email and WhatsApp Dispatches with real wrappers
     */
    public function test_communication_service_dispatch(): void
    {
        $commService = app(CommunicationService::class);

        $emailLog = $commService->send('email', 'customer@example.com', 'order_confirmation', [
            'customer_name' => 'John Doe',
            'order_number'  => 'ORD-COMM-001',
            'order_total'   => '99.00',
            'tracking_url'  => 'https://example.com/track',
        ]);

        $this->assertEquals('sent', $emailLog->status);
        $this->assertEquals('email', $emailLog->channel);

        $waLog = $commService->send('whatsapp', '9876543210', 'order_confirmation', [
            'customer_name' => 'John Doe',
            'order_number'  => 'ORD-COMM-001',
            'order_total'   => '99.00',
            'tracking_url'  => 'https://example.com/track',
        ]);

        $this->assertEquals('sent', $waLog->status);
        $this->assertEquals('whatsapp', $waLog->channel);
    }
}
