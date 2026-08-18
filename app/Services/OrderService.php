<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\LoyaltyTransaction;
use App\Models\GiftCard;
use App\Models\StoreCredit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create order with stock reservation/deduction, gift card/store credit application, and loyalty accumulation
     */
    public function placeOrder(array $data, ?int $userId = null): Order
    {
        return DB::transaction(function () use ($data, $userId) {
            $totalAmount = (float)$data['total_amount'];
            $giftCardAmount = 0.0;
            $storeCreditAmount = 0.0;

            // Apply Gift Card
            if (!empty($data['gift_card_code'])) {
                $gc = GiftCard::where('code', $data['gift_card_code'])->first();
                if ($gc && $gc->isValid()) {
                    $giftCardAmount = min($totalAmount, (float)$gc->current_balance);
                    $gc->decrement('current_balance', $giftCardAmount);
                    $totalAmount -= $giftCardAmount;
                }
            }

            // Apply Store Credit
            if (!empty($data['use_store_credit']) && $userId) {
                $sc = StoreCredit::where('user_id', $userId)->first();
                if ($sc && $sc->balance > 0) {
                    $storeCreditAmount = min($totalAmount, (float)$sc->balance);
                    $sc->debit($storeCreditAmount, 'purchase', null, 'Applied to order');
                    $totalAmount -= $storeCreditAmount;
                }
            }

            $order = Order::create([
                'order_number'        => 'ORD-' . strtoupper(Str::random(8)),
                'user_id'             => $userId,
                'total_amount'        => max(0, $totalAmount),
                'payment_status'      => $data['payment_status'] ?? 'pending',
                'order_status'        => 'processing',
                'payment_method'      => $data['payment_method'] ?? 'card',
                'shipping_address'    => $data['shipping_address'] ?? 'In-Store',
                'branch_id'           => $data['branch_id'] ?? 1,
                'delivery_slot_id'    => $data['delivery_slot_id'] ?? null,
                'is_pickup'           => $data['is_pickup'] ?? false,
                'gift_card_code'      => $data['gift_card_code'] ?? null,
                'gift_card_amount'    => $giftCardAmount,
                'store_credit_amount' => $storeCreditAmount,
            ]);

            // Process Items
            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if (!$product) continue;

                $qty = (int)$item['qty'];
                $price = isset($item['price']) ? (float)$item['price'] : (float)$product->price;
                $lineTotal = $qty * $price;

                $order->items()->create([
                    'product_id'   => $product->id,
                    'product_name' => $product->name,
                    'qty'          => $qty,
                    'price'        => $price,
                    'unit_price'   => $price,
                    'total'        => $lineTotal,
                    'total_price'  => $lineTotal,
                    'hsn_code'     => $product->hsn_code,
                    'gst_rate'     => $product->gst_rate,
                ]);

                // Deduct stock & record movement
                $beforeQty = $product->qty;
                $afterQty = max(0, $product->qty - $qty);
                $product->update(['qty' => $afterQty]);

                StockMovement::record(
                    $product->id,
                    -$qty,
                    'sale',
                    "Order #{$order->order_number} checkout",
                    null,
                    $order->branch_id,
                    'Order',
                    $order->id,
                    $userId
                );
            }

            // Award Loyalty Points (1 pt per $10 spent)
            if ($userId && $order->total_amount >= 10) {
                $points = (int)floor($order->total_amount / 10);
                LoyaltyTransaction::recordPoints($userId, $points, 'earned', $order->id, "Points earned on Order #{$order->order_number}");
            }

            return $order;
        });
    }
}
