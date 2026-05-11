<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearCache();
    }

    /**
     * Clear all product-related caches.
     */
    protected function clearCache()
    {
        // If using Redis/Memcached, we can use tags. 
        // For standard file cache, we use a versioning key or clear specific keys.
        Cache::forget('all_active_products');
        Cache::forget('categories_with_products');
    }
}
