<?php

namespace App\Services;

use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\StockReservation;
use App\Models\StockMovement;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Reserve stock for a checkout session or order
     */
    public function reserveStock(int $productId, int $qty, ?int $orderId = null, ?string $sessionId = null, ?int $warehouseId = null): ?StockReservation
    {
        return DB::transaction(function () use ($productId, $qty, $orderId, $sessionId, $warehouseId) {
            $product = Product::lockForUpdate()->find($productId);
            if (!$product || $product->qty < $qty) {
                return null;
            }

            // Deduct available qty and increase committed qty if warehouse stock exists
            if ($warehouseId) {
                $whStock = WarehouseStock::firstOrCreate(
                    ['warehouse_id' => $warehouseId, 'product_id' => $productId],
                    ['qty' => $product->qty]
                );
                $whStock->increment('reserved_qty', $qty);
            }

            return StockReservation::create([
                'product_id'   => $productId,
                'warehouse_id' => $warehouseId,
                'order_id'     => $orderId,
                'session_id'   => $sessionId,
                'qty'          => $qty,
                'status'       => 'active',
                'expires_at'   => now()->addMinutes(30),
            ]);
        });
    }

    /**
     * Release expired stock reservations
     */
    public function releaseExpiredReservations(): int
    {
        $expired = StockReservation::where('status', 'active')
            ->where('expires_at', '<', now())
            ->get();

        $count = 0;
        foreach ($expired as $res) {
            DB::transaction(function () use ($res) {
                if ($res->warehouse_id) {
                    $whStock = WarehouseStock::where('warehouse_id', $res->warehouse_id)
                        ->where('product_id', $res->product_id)
                        ->first();
                    if ($whStock && $whStock->reserved_qty >= $res->qty) {
                        $whStock->decrement('reserved_qty', $res->qty);
                    }
                }
                $res->update(['status' => 'released']);
            });
            $count++;
        }
        return $count;
    }

    /**
     * ABC Inventory Analysis (Rank A: top 80% revenue, Rank B: next 15%, Rank C: bottom 5%)
     */
    public function calculateAbcAnalysis(): array
    {
        $sales = OrderItem::select('product_id', DB::raw('SUM(total_price) as total_revenue'), DB::raw('SUM(qty) as units_sold'))
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->get();

        $totalRevenue = $sales->sum('total_revenue');
        if ($totalRevenue <= 0) {
            $totalRevenue = 1;
        }

        $cumulative = 0;
        $classified = [];

        foreach ($sales as $sale) {
            $product = Product::find($sale->product_id);
            if (!$product) continue;

            $rev = (float)$sale->total_revenue;
            $cumulative += $rev;
            $percentage = ($cumulative / $totalRevenue) * 100;

            if ($percentage <= 80) {
                $category = 'A'; // High value / High priority
            } elseif ($percentage <= 95) {
                $category = 'B'; // Moderate value
            } else {
                $category = 'C'; // Low value
            }

            $classified[] = [
                'product'       => $product,
                'revenue'       => $rev,
                'units_sold'    => $sale->units_sold,
                'stock'         => $product->qty,
                'abc_category'  => $category,
                'revenue_share' => round(($rev / $totalRevenue) * 100, 2),
            ];
        }

        return $classified;
    }

    /**
     * Dead Stock & Slow Mover Detection (No sales in last 60+ days)
     */
    public function getDeadStock(int $days = 60): array
    {
        $recentSoldProductIds = OrderItem::where('created_at', '>=', now()->subDays($days))
            ->pluck('product_id')
            ->unique()
            ->toArray();

        $deadStockProducts = Product::whereNotIn('id', $recentSoldProductIds)
            ->where('qty', '>', 0)
            ->get();

        $result = [];
        foreach ($deadStockProducts as $p) {
            $result[] = [
                'product'          => $p,
                'qty'              => $p->qty,
                'tied_up_capital'  => $p->qty * $p->price,
                'days_without_sale'=> $days,
            ];
        }

        return $result;
    }
}
