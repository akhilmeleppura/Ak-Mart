<?php

namespace App\Services;

use App\Models\Product;
use App\Models\WarehouseStock;
use App\Models\StockReservation;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\OrderItem;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryService
{
    /**
     * Calculate Available Stock = Physical Stock - Active Reservations
     */
    public function getAvailableStock(int $productId, ?int $warehouseId = null): int
    {
        $product = Product::find($productId);
        if (!$product) {
            return 0;
        }

        $physicalQty = $product->qty;

        if ($warehouseId) {
            $whStock = WarehouseStock::where('warehouse_id', $warehouseId)
                ->where('product_id', $productId)
                ->first();
            $physicalQty = $whStock ? $whStock->qty : 0;
        }

        $activeReservations = StockReservation::where('product_id', $productId)
            ->when($warehouseId, fn($q) => $q->where('warehouse_id', $warehouseId))
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->sum('qty');

        return max(0, $physicalQty - $activeReservations);
    }

    /**
     * Reserve stock for a checkout session or order with strict row-level lock
     */
    public function reserveStock(int $productId, int $qty, ?int $orderId = null, ?string $sessionId = null, ?int $warehouseId = null, ?string $idempotencyKey = null, ?int $batchId = null): ?StockReservation
    {
        return DB::transaction(function () use ($productId, $qty, $orderId, $sessionId, $warehouseId, $idempotencyKey, $batchId) {
            // Check idempotency
            if ($idempotencyKey) {
                $existing = StockReservation::where('idempotency_key', $idempotencyKey)->first();
                if ($existing) {
                    return $existing;
                }
            }

            // Lock product record to prevent race conditions
            $product = Product::lockForUpdate()->find($productId);
            if (!$product) {
                return null;
            }

            $available = $this->getAvailableStock($productId, $warehouseId);
            if ($available < $qty) {
                return null;
            }

            if ($warehouseId) {
                $whStock = WarehouseStock::lockForUpdate()->firstOrCreate(
                    ['warehouse_id' => $warehouseId, 'product_id' => $productId],
                    ['qty' => $product->qty]
                );
                $whStock->increment('reserved_qty', $qty);
            }

            return StockReservation::create([
                'product_id'        => $productId,
                'product_batch_id'  => $batchId,
                'warehouse_id'      => $warehouseId,
                'order_id'          => $orderId,
                'session_id'        => $sessionId,
                'qty'               => $qty,
                'status'            => 'active',
                'idempotency_key'   => $idempotencyKey,
                'expires_at'        => now()->addMinutes(30),
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
     * Traceable stock adjustment with immutable ledger recording
     */
    public function adjustStock(
        int $productId,
        int $quantityChange,
        string $reason,
        ?int $userId = null,
        ?int $branchId = null,
        ?string $refType = null,
        ?int $refId = null
    ): StockMovement {
        return DB::transaction(function () use ($productId, $quantityChange, $reason, $userId, $branchId, $refType, $refId) {
            $type = $quantityChange >= 0 ? 'stock_in' : 'stock_out';
            return StockMovement::record(
                $productId,
                $quantityChange,
                $type,
                $reason,
                null,
                $branchId,
                $refType,
                $refId,
                $userId
            );
        });
    }

    /**
     * Create Multi-Branch Stock Transfer
     */
    public function createTransfer(
        int $fromBranchId,
        int $toBranchId,
        array $items,
        ?string $notes = null,
        ?int $userId = null
    ): StockTransfer {
        return DB::transaction(function () use ($fromBranchId, $toBranchId, $items, $notes, $userId) {
            $transfer = StockTransfer::create([
                'transfer_number' => 'TRF-' . strtoupper(Str::random(8)),
                'from_branch_id'  => $fromBranchId,
                'to_branch_id'    => $toBranchId,
                'status'          => 'pending',
                'notes'           => $notes,
                'user_id'         => $userId ?? auth()->id(),
            ]);

            foreach ($items as $item) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'received_quantity' => 0,
                ]);

                // Deduct from source branch with traceable movement
                StockMovement::record(
                    $item['product_id'],
                    -$item['quantity'],
                    'transfer_out',
                    "Dispatched via Transfer {$transfer->transfer_number}",
                    null,
                    $fromBranchId,
                    StockTransfer::class,
                    $transfer->id,
                    $userId
                );
            }

            return $transfer;
        });
    }

    /**
     * Dispatch Transfer (marks as in_transit)
     */
    public function dispatchTransfer(int $transferId): bool
    {
        $transfer = StockTransfer::findOrFail($transferId);
        if ($transfer->status !== 'pending') {
            return false;
        }

        $transfer->update(['status' => 'in_transit']);
        return true;
    }

    /**
     * Receive Transfer at Destination Branch
     */
    public function receiveTransfer(int $transferId, ?array $receivedItems = null, ?int $userId = null): bool
    {
        return DB::transaction(function () use ($transferId, $receivedItems, $userId) {
            $transfer = StockTransfer::with('items')->findOrFail($transferId);
            if ($transfer->status === 'completed' || $transfer->status === 'cancelled') {
                return false;
            }

            foreach ($transfer->items as $item) {
                $qty = $receivedItems[$item->id] ?? $item->quantity;
                $item->update(['received_quantity' => $qty]);

                // Add to destination branch with traceable movement
                StockMovement::record(
                    $item->product_id,
                    $qty,
                    'transfer_in',
                    "Received via Transfer {$transfer->transfer_number}",
                    null,
                    $transfer->to_branch_id,
                    StockTransfer::class,
                    $transfer->id,
                    $userId
                );
            }

            $transfer->update(['status' => 'completed']);
            return true;
        });
    }

    /**
     * Smart Restock Suggestions based on Low Stock and Reorder Points
     */
    public function getRestockSuggestions(?int $branchId = null): array
    {
        $products = Product::where('qty', '<=', 10)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        $suggestions = [];
        foreach ($products as $product) {
            $available = $this->getAvailableStock($product->id);
            $reorderPoint = 10;
            $targetStock = 50;
            $suggestedQty = max(0, $targetStock - $available);

            if ($suggestedQty > 0) {
                $supplier = Supplier::first();
                $suggestions[] = [
                    'product_id'        => $product->id,
                    'product_name'      => $product->name,
                    'sku'               => $product->sku ?? ('SKU-' . $product->id),
                    'current_stock'     => $product->qty,
                    'available_stock'   => $available,
                    'reorder_point'     => $reorderPoint,
                    'suggested_quantity'=> $suggestedQty,
                    'estimated_cost'    => $suggestedQty * ($product->cost_price ?: ($product->price * 0.6)),
                    'preferred_supplier'=> $supplier ? $supplier->name : 'Primary Supplier',
                    'supplier_id'       => $supplier?->id,
                ];
            }
        }

        return $suggestions;
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

        $totalRevenue = $sales->sum('total_revenue') ?: 1;
        $cumulative = 0;
        $classified = [];

        foreach ($sales as $sale) {
            $product = Product::find($sale->product_id);
            if (!$product) continue;

            $rev = (float)$sale->total_revenue;
            $cumulative += $rev;
            $percentage = ($cumulative / $totalRevenue) * 100;

            if ($percentage <= 80) {
                $category = 'A';
            } elseif ($percentage <= 95) {
                $category = 'B';
            } else {
                $category = 'C';
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
