<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BatchExpiryService
{
    /**
     * Allocate inventory from active batches using FEFO (First Expired, First Out)
     */
    public function allocateFefo(int $productId, int $requiredQty, ?int $userId = null): Collection
    {
        return DB::transaction(function () use ($productId, $requiredQty, $userId) {
            $today = now()->toDateString();

            // Select only non-expired, active batches with available quantity, ordered by expiry date ASC
            $batches = ProductBatch::where('product_id', $productId)
                ->where('is_active', true)
                ->where('status', 'active')
                ->where('qty', '>', 0)
                ->where(function ($q) use ($today) {
                    $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', $today);
                })
                ->orderByRaw('expiry_date IS NULL, expiry_date ASC')
                ->lockForUpdate()
                ->get();

            $totalAvailable = $batches->sum('qty');
            if ($totalAvailable < $requiredQty) {
                throw ValidationException::withMessages([
                    'batch' => "Insufficient non-expired batch inventory for Product #{$productId}. Required: {$requiredQty}, Available: {$totalAvailable}"
                ]);
            }

            $allocations = collect();
            $remaining = $requiredQty;

            foreach ($batches as $batch) {
                if ($remaining <= 0) break;

                $allocatedFromBatch = min($batch->qty, $remaining);
                $batch->decrement('qty', $allocatedFromBatch);
                $batch->increment('reserved_qty', $allocatedFromBatch);
                $batch->refresh();

                // Check if depleted
                if ($batch->qty <= 0) {
                    $batch->update(['status' => 'depleted']);
                }

                $allocations->push([
                    'batch_id'     => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'expiry_date'  => $batch->expiry_date?->toDateString(),
                    'qty'          => $allocatedFromBatch,
                ]);

                $remaining -= $allocatedFromBatch;
            }

            return $allocations;
        });
    }

    /**
     * Scan and return near-expiry batches
     */
    public function getNearExpiryBatches(int $days = 7): Collection
    {
        $today = now()->toDateString();
        $targetDate = now()->addDays($days)->toDateString();

        return ProductBatch::with('product')
            ->where('is_active', true)
            ->where('qty', '>', 0)
            ->whereBetween('expiry_date', [$today, $targetDate])
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Scan and mark expired batches
     */
    public function markExpiredBatches(?int $userId = null): int
    {
        $today = now()->toDateString();
        $expired = ProductBatch::where('is_active', true)
            ->where('status', '!=', 'expired')
            ->where('expiry_date', '<', $today)
            ->where('qty', '>', 0)
            ->get();

        $count = 0;
        foreach ($expired as $batch) {
            DB::transaction(function () use ($batch, $userId) {
                $expiredQty = $batch->qty;
                $batch->update([
                    'expired_qty' => $batch->expired_qty + $expiredQty,
                    'qty'         => 0,
                    'status'      => 'expired',
                    'is_active'   => false,
                ]);

                // Deduct from main product physical stock
                $product = Product::lockForUpdate()->find($batch->product_id);
                if ($product) {
                    $product->decrement('qty', min($product->qty, $expiredQty));

                    StockMovement::record(
                        $product->id,
                        -$expiredQty,
                        'wastage',
                        "Batch #{$batch->batch_number} expired on {$batch->expiry_date}",
                        null,
                        $product->branch_id ?? 1,
                        'ProductBatch',
                        $batch->id,
                        $userId
                    );
                }
            });
            $count++;
        }

        return $count;
    }
}
