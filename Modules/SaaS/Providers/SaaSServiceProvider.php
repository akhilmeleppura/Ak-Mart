<?php

namespace Modules\SaaS\Providers;

use Illuminate\Support\ServiceProvider;

class SaaSServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if (file_exists(__DIR__ . '/../routes/web.php')) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }
        if (file_exists(__DIR__ . '/../resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'saas');
        }
    }

    public function register()
    {
    }
}
