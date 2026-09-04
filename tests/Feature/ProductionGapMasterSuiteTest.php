<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\FulfillmentOrder;
use App\Models\FulfillmentOrderItem;
use App\Models\ReturnRequest;
use App\Models\DeliveryZone;
use App\Models\CreditNote;
use App\Models\PaymentReconciliation;
use App\Models\StoreCredit;
use App\Models\Category;
use App\Models\Branch\Branch;
use App\Services\OrderManagementService;
use App\Services\GroceryProductService;
use App\Services\BatchExpiryService;
use App\Services\InventoryService;
use App\Services\WarehouseFulfillmentService;
use App\Services\ReverseLogisticsService;
use App\Services\PaymentReconciliationService;
use App\Services\CustomerCrmService;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ProductionGapMasterSuiteTest extends TestCase
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
     * 1. Test Order Management state machine transitions and invalid transition rejection
     */
    public function test_order_management_state_machine_valid_transitions_and_illegal_rejections(): void
    {
        $oms = app(OrderManagementService::class);

        $order = Order::create([
            'order_number' => 'ORD-TEST-001',
            'user_id'      => $this->customer->id,
            'total_amount' => 150.00,
            'order_status' => 'pending',
            'branch_id'    => $this->branch->id,
        ]);

        // Valid transition: pending -> processing
        $order = $oms->transitionStatus($order, 'processing', $this->admin->id, 'Processing order in warehouse');
        $this->assertEquals('processing', $order->order_status);
        $this->assertDatabaseHas('order_status_histories', [
            'order_id'    => $order->id,
            'from_status' => 'pending',
            'to_status'   => 'processing',
        ]);

        // Valid transition: processing -> picking -> picked -> packed -> dispatched
        $order = $oms->transitionStatus($order, 'picking', $this->admin->id);
        $this->assertEquals('picking', $order->order_status);

        $order = $oms->transitionStatus($order, 'picked', $this->admin->id);
        $this->assertEquals('picked', $order->order_status);

        $order = $oms->transitionStatus($order, 'packed', $this->admin->id);
        $this->assertEquals('packed', $order->order_status);

        $order = $oms->transitionStatus($order, 'dispatched', $this->admin->id);
        $this->assertEquals('dispatched', $order->order_status);

        // Illegal transition: dispatched -> pending should throw ValidationException
        $this->expectException(ValidationException::class);
        $oms->transitionStatus($order, 'pending', $this->admin->id);
    }

    /**
     * 2. Test Order cancellation with reason code and automatic inventory restoration
     */
    public function test_order_cancellation_with_reason_code_and_automatic_stock_restoration(): void
    {
        $oms = app(OrderManagementService::class);

        $product = Product::factory()->create([
            'name'  => 'Organic Whole Milk',
            'qty'   => 20,
            'price' => 4.50,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-002',
            'user_id'      => $this->customer->id,
            'total_amount' => 18.00,
            'order_status' => 'processing',
            'branch_id'    => 1,
        ]);

        $orderItem = OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'qty'          => 4,
            'price'        => 4.50,
            'total'        => 18.00,
            'item_status'  => 'pending',
        ]);

        // Stock before cancel: 20
        $cancelledOrder = $oms->cancelOrder($order, 'CUSTOMER_REQUEST', 'Customer changed mind before dispatch', $this->admin->id);

        $this->assertEquals('cancelled', $cancelledOrder->order_status);
        $this->assertEquals('CUSTOMER_REQUEST', $cancelledOrder->cancellation_reason_code);

        // Product stock must be restored atomically from 20 to 24
        $product->refresh();
        $this->assertEquals(24, $product->qty);

        // Order item status should reflect cancellation
        $orderItem->refresh();
        $this->assertEquals('cancelled', $orderItem->item_status);
        $this->assertEquals(4, $orderItem->cancelled_qty);
    }

    /**
     * 3. Test Partial item cancellation
     */
    public function test_partial_item_cancellation_and_inventory_restock(): void
    {
        $oms = app(OrderManagementService::class);

        $product = Product::factory()->create([
            'name' => 'Brown Farm Eggs',
            'qty'  => 10,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-TEST-003',
            'user_id'      => $this->customer->id,
            'total_amount' => 30.00,
            'order_status' => 'processing',
        ]);

        $item = OrderItem::create([
            'order_id'      => $order->id,
            'product_id'    => $product->id,
            'product_name'  => $product->name,
            'qty'           => 6,
            'cancelled_qty' => 0,
            'price'         => 5.00,
            'total'         => 30.00,
            'item_status'   => 'pending',
        ]);

        // Cancel 2 out of 6
        $oms->cancelOrderItem($item, 2, 'OUT_OF_STOCK_PARTIAL', $this->admin->id);

        $item->refresh();
        $this->assertEquals(2, $item->cancelled_qty);
        $this->assertEquals('partially_cancelled', $item->item_status);

        $product->refresh();
        $this->assertEquals(12, $product->qty);
    }

    /**
     * 4. Test Order internal notes and delivery rescheduling
     */
    public function test_order_notes_and_delivery_rescheduling(): void
    {
        $oms = app(OrderManagementService::class);

        $order = Order::create([
            'order_number' => 'ORD-TEST-004',
            'user_id'      => $this->customer->id,
            'total_amount' => 45.00,
            'order_status' => 'pending',
        ]);

        $order = $oms->addNote($order, 'Customer requested gate code #4491', true);
        $this->assertStringContainsString('gate code #4491', $order->internal_notes);

        $rescheduleDate = now()->addDays(2)->toDateTimeString();
        $order = $oms->rescheduleDelivery($order, $rescheduleDate, 'Customer will be out of town', $this->admin->id);

        $this->assertEquals('rescheduled', $order->order_status);
        $this->assertEquals($rescheduleDate, $order->rescheduled_delivery_at);
    }

    /**
     * 5. Test Grocery decimal weight pricing and price-per-unit formatting
     */
    public function test_grocery_weight_decimal_pricing_and_price_per_unit(): void
    {
        $grocery = app(GroceryProductService::class);

        $apples = Product::factory()->create([
            'name'                 => 'Gala Apples',
            'is_weight_based'      => true,
            'unit'                 => 'kg',
            'unit_price_ratio'     => 3.20,
            'price'                => 3.20,
            'price_per_unit_label' => '$3.20 / kg',
        ]);

        // 1.450 kg at $3.20/kg = $4.64
        $lineTotal = $grocery->calculateLineTotal($apples, 1.450);
        $this->assertEquals(4.64, $lineTotal);

        $unitLabel = $grocery->formatPricePerUnit($apples);
        $this->assertEquals('$3.20 / kg', $unitLabel);
    }

    /**
     * 6. Test Grocery substitution recommendation
     */
    public function test_grocery_substitution_recommendation_when_out_of_stock(): void
    {
        $grocery = app(GroceryProductService::class);

        $category = Category::create([
            'name'      => 'Dairy & Chilled',
            'slug'      => 'dairy-chilled',
            'is_active' => true,
        ]);

        $original = Product::factory()->create([
            'name'               => 'Greek Yogurt 500g Plain',
            'category_id'        => $category->id,
            'price'              => 4.00,
            'qty'                => 0,
            'allow_substitution' => true,
        ]);

        $alt1 = Product::factory()->create([
            'name'        => 'Organic Greek Yogurt 500g',
            'category_id' => $category->id,
            'price'       => 4.25,
            'qty'         => 15,
        ]);

        $substitutions = $grocery->suggestSubstitutions($original);
        $this->assertTrue($substitutions->contains('id', $alt1->id));
    }

    /**
     * 7. Test FEFO (First Expired, First Out) batch allocation
     */
    public function test_fefo_batch_allocation_selects_earliest_expiry_and_rejects_expired(): void
    {
        $batchService = app(BatchExpiryService::class);

        $product = Product::factory()->create([
            'name' => 'Fresh Pasteurized Cream',
            'qty'  => 50,
        ]);

        // Batch 1: Expired yesterday (must NEVER be allocated)
        ProductBatch::create([
            'product_id'   => $product->id,
            'batch_number' => 'BATCH-EXPIRED',
            'expiry_date'  => now()->subDay()->toDateString(),
            'qty'          => 10,
            'is_active'    => true,
            'status'       => 'active',
        ]);

        // Batch 2: Expiring in 3 days (earliest valid batch)
        $batch2 = ProductBatch::create([
            'product_id'   => $product->id,
            'batch_number' => 'BATCH-EARLIEST',
            'expiry_date'  => now()->addDays(3)->toDateString(),
            'qty'          => 15,
            'is_active'    => true,
            'status'       => 'active',
        ]);

        // Batch 3: Expiring in 14 days (later batch)
        $batch3 = ProductBatch::create([
            'product_id'   => $product->id,
            'batch_number' => 'BATCH-LATER',
            'expiry_date'  => now()->addDays(14)->toDateString(),
            'qty'          => 20,
            'is_active'    => true,
            'status'       => 'active',
        ]);

        // Allocate 18 units: 15 should come from BATCH-EARLIEST, 3 from BATCH-LATER
        $allocations = $batchService->allocateFefo($product->id, 18);

        $this->assertCount(2, $allocations);
        $this->assertEquals('BATCH-EARLIEST', $allocations[0]['batch_number']);
        $this->assertEquals(15, $allocations[0]['qty']);

        $this->assertEquals('BATCH-LATER', $allocations[1]['batch_number']);
        $this->assertEquals(3, $allocations[1]['qty']);

        // Check Batch 2 is now depleted
        $batch2->refresh();
        $this->assertEquals(0, $batch2->qty);
        $this->assertEquals('depleted', $batch2->status);

        // Check Batch 3 has remaining quantity
        $batch3->refresh();
        $this->assertEquals(17, $batch3->qty);
    }

    /**
     * 8. Test Concurrency inventory reservation with row-locking and idempotency
     */
    public function test_inventory_reservation_row_locking_and_idempotency(): void
    {
        $invService = app(InventoryService::class);

        $product = Product::factory()->create([
            'qty' => 10,
        ]);

        $idempotencyKey = 'IDEM-RES-998811';

        // 1st Reservation
        $res1 = $invService->reserveStock($product->id, 4, null, 'session_123', null, $idempotencyKey);
        $this->assertNotNull($res1);
        $this->assertEquals(4, $res1->qty);
        $this->assertEquals($idempotencyKey, $res1->idempotency_key);

        // Duplicate call with same idempotency key must return same reservation without double-decrement
        $res2 = $invService->reserveStock($product->id, 4, null, 'session_123', null, $idempotencyKey);
        $this->assertEquals($res1->id, $res2->id);

        // Available stock must be 10 - 4 = 6
        $available = $invService->getAvailableStock($product->id);
        $this->assertEquals(6, $available);
    }

    /**
     * 9. Test Warehouse picking, item barcode scan, and packing station
     */
    public function test_warehouse_picking_item_verification_and_package_creation(): void
    {
        $whService = app(WarehouseFulfillmentService::class);

        $product = Product::factory()->create([
            'name'    => 'Sparkling Water 6-pack',
            'barcode' => '8901002003004',
            'qty'     => 10,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-WH-001',
            'total_amount' => 24.00,
            'order_status' => 'processing',
        ]);

        $orderItem = OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'qty'          => 2,
            'price'        => 12.00,
            'total'        => 24.00,
        ]);

        $fulfillment = FulfillmentOrder::create([
            'fulfillment_number' => 'FUL-TEST-001',
            'order_id'           => $order->id,
            'status'             => 'unfulfilled',
        ]);

        $fItem = FulfillmentOrderItem::create([
            'fulfillment_order_id' => $fulfillment->id,
            'order_item_id'        => $orderItem->id,
            'qty'                  => 2,
        ]);

        // 1. Start picking
        $fulfillment = $whService->startPicking($fulfillment, $this->admin->id);
        $this->assertEquals('picking', $fulfillment->status);

        // 2. Barcode verification
        $fItem = $whService->verifyPickItem($fItem, '8901002003004', 2);
        $this->assertEquals(2, $fItem->qty);

        // 3. Complete picking
        $fulfillment = $whService->completePicking($fulfillment);
        $this->assertEquals('picked', $fulfillment->status);

        // 4. Create package with weight
        $package = $whService->createPackage($fulfillment, 4.250, 'carton', $this->admin->id);
        $this->assertNotNull($package->package_barcode);
        $this->assertEquals(4.250, $package->weight_kg);

        $fulfillment->refresh();
        $this->assertEquals('packed', $fulfillment->status);

        // 5. Dispatch
        $fulfillment = $whService->dispatchFulfillment($fulfillment, 'FedEx', 'TRACK-998822');
        $this->assertEquals('shipped', $fulfillment->status);
    }

    /**
     * 10. Test RMA Return creation, inspection, restocking, and credit note refund
     */
    public function test_rma_creation_inspection_restocking_and_credit_note_refund(): void
    {
        $reverseService = app(ReverseLogisticsService::class);

        $product = Product::factory()->create([
            'name' => 'Blender 500W',
            'qty'  => 5,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-RMA-001',
            'user_id'      => $this->customer->id,
            'total_amount' => 50.00,
            'order_status' => 'completed',
            'created_at'   => now()->subDays(2),
        ]);

        $item = OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $product->id,
            'product_name' => $product->name,
            'qty'          => 1,
            'price'        => 50.00,
            'total'        => 50.00,
        ]);

        // 1. Create RMA
        $rma = $reverseService->createRma($order, [
            ['order_item_id' => $item->id, 'qty' => 1, 'reason' => 'Changed mind', 'condition' => 'unopened']
        ]);
        $this->assertNotNull($rma->rma_number);
        $this->assertEquals(50.00, $rma->refund_amount);

        // 2. Inspect and approve restock
        $rma = $reverseService->inspectAndDecide($rma, 'approved_restock', $this->admin->id);
        $this->assertEquals('approved', $rma->status);

        // Product stock restored from 5 to 6
        $product->refresh();
        $this->assertEquals(6, $product->qty);

        // 3. Process Wallet refund & credit note
        $creditNote = $reverseService->processRefund($rma, 'wallet', $this->admin->id);
        $this->assertNotNull($creditNote->credit_note_number);
        $this->assertEquals(50.00, $creditNote->total_amount);

        // Customer wallet balance must now be $50
        $wallet = StoreCredit::where('user_id', $this->customer->id)->first();
        $this->assertEquals(50.00, $wallet->balance);
    }

    /**
     * 11. Test Payment Reconciliation, webhook idempotency, and fee calculation
     */
    public function test_payment_reconciliation_webhook_idempotency_and_fee_calculation(): void
    {
        $payService = app(PaymentReconciliationService::class);

        $order = Order::create([
            'order_number'   => 'ORD-PAY-001',
            'user_id'        => $this->customer->id,
            'total_amount'   => 100.00,
            'payment_status' => 'pending',
        ]);

        $txId = 'ch_stripe_test_998877';
        $idempotencyKey = 'IDEM-PAY-776655';

        // First webhook delivery: $100.00 amount, $2.90 gateway fee -> Net settlement $97.10
        $rec1 = $payService->recordPayment(
            'stripe',
            $txId,
            100.00,
            $order,
            2.90,
            'captured',
            true,
            $idempotencyKey,
            ['stripe_status' => 'succeeded']
        );

        $this->assertEquals(97.10, $rec1->net_settlement);
        $this->assertEquals('captured', $rec1->status);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);

        // Duplicate webhook delivery with same transaction ID / idempotency key
        $rec2 = $payService->recordPayment(
            'stripe',
            $txId,
            100.00,
            $order,
            2.90,
            'captured',
            true,
            $idempotencyKey
        );

        $this->assertEquals($rec1->id, $rec2->id);
        $this->assertEquals(1, PaymentReconciliation::where('transaction_id', $txId)->count());
    }

    /**
     * 12. Test Customer CRM RFM segmentation
     */
    public function test_customer_crm_rfm_segmentation_calculation(): void
    {
        $crmService = app(CustomerCrmService::class);

        // High spender ($600) customer -> VIP
        Order::create([
            'order_number' => 'ORD-VIP-001',
            'user_id'      => $this->customer->id,
            'total_amount' => 600.00,
            'order_status' => 'completed',
        ]);

        $updatedCustomer = $crmService->recalculateSegment($this->customer);
        $this->assertEquals('VIP', $updatedCustomer->customer_segment);
        $this->assertEquals(600.00, $updatedCustomer->lifetime_spend);
        $this->assertEquals(1, $updatedCustomer->total_orders_count);

        // Internal note
        $note = $crmService->addCustomerNote($this->customer, 'High value wholesale buyer interested in weekly dairy', $this->admin->id, true);
        $this->assertTrue($note->is_pinned);
    }

    /**
     * 13. Test Immutable Audit Log recording and PII masking
     */
    public function test_immutable_audit_log_recording_and_pii_masking(): void
    {
        $log = AuditLogService::log(
            'order.cancel',
            'Order',
            99,
            ['password' => 'secret123', 'status' => 'processing', 'card_number' => '4111222233334444'],
            ['status' => 'cancelled'],
            $this->admin->id,
            'admin'
        );

        $this->assertNotNull($log->id);
        $this->assertEquals('order.cancel', $log->action);
        $this->assertEquals('********', $log->old_values['password']);
        $this->assertEquals('********', $log->old_values['card_number']);
        $this->assertEquals('processing', $log->old_values['status']);
        $this->assertEquals('cancelled', $log->new_values['status']);
    }

    /**
     * 14. Test Delivery Zone dynamic distance fees and thresholds
     */
    public function test_delivery_zone_dynamic_distance_fees_and_thresholds(): void
    {
        $zone = DeliveryZone::create([
            'name'                    => 'Downtown Metro',
            'branch_id'               => 1,
            'min_order_amount'        => 15.00,
            'free_delivery_threshold' => 50.00,
            'base_delivery_fee'       => 3.99,
            'per_km_fee'              => 0.50,
            'max_distance_km'         => 20.00,
            'is_active'               => true,
        ]);

        // Order subtotal $30 (< $50 threshold), distance 6 km: Fee = $3.99 + (6 * $0.50) = $6.99
        $fee1 = $zone->calculateFee(30.00, 6.0);
        $this->assertEquals(6.99, $fee1);

        // Order subtotal $60 (>= $50 threshold): Fee = $0.00
        $fee2 = $zone->calculateFee(60.00, 6.0);
        $this->assertEquals(0.00, $fee2);
    }
}
