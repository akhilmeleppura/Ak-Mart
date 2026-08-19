<?php

namespace Modules\Logistics\Providers;

use Illuminate\Support\ServiceProvider;

class LogisticsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (file_exists(__DIR__ . '/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }
        if (file_exists(__DIR__ . '/../routes/api.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        }
        if (file_exists(__DIR__ . '/../resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'logistics');
        }
        if (file_exists(__DIR__ . '/../database/migrations')) {
            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    public function register(): void
    {
    }
}