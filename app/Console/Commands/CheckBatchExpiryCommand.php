<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\Log;

class CheckBatchExpiryCommand extends Command
{
    protected $signature = 'batches:check-expiry {--days=30 : Number of days threshold for near-expiry alert}';
    protected $description = 'Scan warehouse product batches for expired and near-expiry items';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $thresholdDate = now()->addDays($days)->toDateString();
        $today = now()->toDateString();

        // Expired batches that are still marked active
        $expiredBatches = ProductBatch::where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', $today)
            ->get();

        $expiredCount = 0;
        foreach ($expiredBatches as $batch) {
            $batch->update(['status' => 'expired']);
            $expiredCount++;
        }

        // Batches nearing expiry within threshold
        $nearExpiryBatches = ProductBatch::where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '>=', $today)
            ->where('expiry_date', '<=', $thresholdDate)
            ->where('qty', '>', 0)
            ->get();

        $this->info("Expired batches updated to 'expired': {$expiredCount}");
        $this->info("Near-expiry batches (< {$days} days): {$nearExpiryBatches->count()}");

        Log::info("Batch expiry check completed. Expired: {$expiredCount}, Near Expiry: {$nearExpiryBatches->count()}");

        return self::SUCCESS;
    }
}
