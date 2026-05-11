<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\DunningService;

class ProcessDunning extends Command
{
    protected $signature   = 'dunning:process';
    protected $description = 'Process dunning sequences for all past-due subscriptions (run daily via scheduler).';

    public function handle(DunningService $dunningService): int
    {
        $this->info('Starting dunning processing...');
        $dunningService->process();
        $this->info('Dunning processing complete.');
        return Command::SUCCESS;
    }
}
