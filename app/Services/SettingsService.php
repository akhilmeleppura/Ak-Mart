<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Service responsible for handling application settings.
 * It merges configuration file defaults with values stored in the database.
 */
class SettingsService
{
    /**
     * Retrieve AI settings, merging config/ai.php with the ai_settings table.
     *
     * @return array
     */
    public function getAISettings(): array
    {
        $config = Config::get('ai');
        $db = DB::table('ai_settings')->first();
        if ($db) {
            $config = array_merge($config, (array) $db);
        }
        return $config;
    }

    /**
     * Persist AI settings to the database.
     *
     * @param array $data
     * @return void
     */
    public function saveAISettings(array $data): void
    {
        // Use a single row with id=1 as a simple key/value store
        DB::table('ai_settings')->updateOrInsert(['id' => 1], $data);
    }

    /**
     * Retrieve Maps settings, merging config/maps.php with the maps_settings table.
     *
     * @return array
     */
    public function getMapsSettings(): array
    {
        $config = Config::get('maps');
        $db = DB::table('maps_settings')->first();
        if ($db) {
            // The DB columns use flat names; map them to the nested config structure
            $dbArray = (array) $db;
            $config['google_api_key'] = $dbArray['google_api_key'] ?? $config['google_api_key'];
            $config['default_center']['lat'] = $dbArray['default_lat'] ?? $config['default_center']['lat'];
            $config['default_center']['lng'] = $dbArray['default_lng'] ?? $config['default_center']['lng'];
            $config['default_center']['zoom'] = $dbArray['default_zoom'] ?? $config['default_center']['zoom'];
        }
        return $config;
    }

    /**
     * Persist Maps settings to the database.
     *
     * @param array $data Expected keys: google_api_key, default_center (lat, lng, zoom)
     * @return void
     */
    public function saveMapsSettings(array $data): void
    {
        $payload = [
            'google_api_key' => $data['google_api_key'] ?? null,
            'default_lat'    => $data['default_center']['lat'] ?? null,
            'default_lng'    => $data['default_center']['lng'] ?? null,
            'default_zoom'   => $data['default_center']['zoom'] ?? null,
        ];
        DB::table('maps_settings')->updateOrInsert(['id' => 1], $payload);
    }
}
?>
