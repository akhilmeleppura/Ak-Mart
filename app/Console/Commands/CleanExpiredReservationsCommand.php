<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\InventoryService;

class CleanExpiredReservationsCommand extends Command
{
    protected $signature = 'reservations:cleanup';
    protected $description = 'Clean up expired cart and checkout stock reservations and return available quantity';

    public function handle(InventoryService $inventoryService): int
    {
        $this->info('Starting expired reservation cleanup...');
        $releasedCount = $inventoryService->releaseExpiredReservations();
        $this->info("Successfully released {$releasedCount} expired stock reservation(s).");

        return self::SUCCESS;
    }
}
