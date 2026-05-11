<?php

namespace Modules\Accounting\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Accounting\Events\EntryCreated;
use Modules\Accounting\Listeners\UpdateCumulativeBalance;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the module.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        EntryCreated::class => [
            UpdateCumulativeBalance::class,
        ],
    ];

    /**
     * Register any module services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }
}
