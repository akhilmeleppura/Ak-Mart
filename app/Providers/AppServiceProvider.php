<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production') || env('APP_ENV') === 'production' || str_starts_with(env('APP_URL', ''), 'https://')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            if (
                $user->is_supreme_admin == 1 ||
                $user->is_super_admin == 1 ||
                $user->user_type === 'super_admin' ||
                (method_exists($user, 'hasRole') && ($user->hasRole('Super Admin') || $user->hasRole('Admin') || $user->hasRole('admin')))
            ) {
                return true;
            }
            return null;
        });

        \App\Models\Product::observe(\App\Observers\ProductObserver::class);

        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src) ? 'template-customizer-core-css' : (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src) ? 'template-customizer-theme-css' : '')
                ];
            }
            return [];
        });

        // Register Enterprise Domain Event Listeners
        \Illuminate\Support\Facades\Event::listen(\App\Events\OrderCreated::class, [\App\Listeners\SendOrderNotificationListener::class, 'handle']);
        \Illuminate\Support\Facades\Event::listen(\App\Events\OrderPaid::class, [\App\Listeners\SendOrderNotificationListener::class, 'handle']);
        \Illuminate\Support\Facades\Event::listen(\App\Events\OrderPaid::class, [\App\Listeners\SyncOrderLedgerListener::class, 'handle']);
        \Illuminate\Support\Facades\Event::listen(\App\Events\OrderCancelled::class, [\App\Listeners\SyncOrderLedgerListener::class, 'handle']);

        // Share Category & Sub-Category Tree to all Storefront Views
        view()->composer(['layouts.storefrontMaster', 'storefront.*'], function ($view) {
            try {
                $navCategories = \App\Models\Category::where('is_active', true)
                    ->whereNull('parent_id')
                    ->with(['children' => fn($q) => $q->where('is_active', true)->withCount('products')])
                    ->withCount('products')
                    ->orderBy('sort_order')
                    ->get();
                $view->with('navCategories', $navCategories);
            } catch (\Throwable $e) {
                $view->with('navCategories', collect());
            }
        });
    }
}
