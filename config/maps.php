<?php

return [
    // Google Maps Settings
    'google_api_key' => env('GOOGLE_MAPS_API_KEY', ''),
    'default_center' => [
        'lat' => env('GOOGLE_MAPS_DEFAULT_LAT', 0.0),
        'lng' => env('GOOGLE_MAPS_DEFAULT_LNG', 0.0),
        'zoom' => env('GOOGLE_MAPS_DEFAULT_ZOOM', 10),
    ],
    // Additional map related features can be added here
];
