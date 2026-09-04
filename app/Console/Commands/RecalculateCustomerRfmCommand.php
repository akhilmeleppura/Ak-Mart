<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\CustomerCrmService;

class RecalculateCustomerRfmCommand extends Command
{
    protected $signature = 'crm:recalculate-rfm';
    protected $description = 'Recalculate RFM scores and customer segmentation for all registered customers';

    public function handle(CustomerCrmService $crmService): int
    {
        $this->info('Starting customer RFM segmentation recalculation...');

        $customers = User::where('user_type', 'customer')->orWhereNull('user_type')->get();
        $bar = $this->output->createProgressBar($customers->count());
        $bar->start();

        $updated = 0;
        foreach ($customers as $customer) {
            $crmService->recalculateSegment($customer);
            $updated++;
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Recalculated RFM segments for {$updated} customers.");

        return self::SUCCESS;
    }
}
