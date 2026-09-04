<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Production enterprise automated schedules
\Illuminate\Support\Facades\Schedule::command('reservations:cleanup')->everyTenMinutes();
\Illuminate\Support\Facades\Schedule::command('batches:check-expiry --days=30')->dailyAt('06:00');
\Illuminate\Support\Facades\Schedule::command('crm:recalculate-rfm')->dailyAt('02:00');

