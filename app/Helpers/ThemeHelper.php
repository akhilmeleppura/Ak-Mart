<?php

use App\Models\StoreSetting;

if (!function_exists('get_store_setting')) {
    function get_store_setting($key, $default = null)
    {
        return StoreSetting::get($key, $default);
    }
}
