<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncTranslationsCommand extends Command
{
    protected $signature = 'akmart:sync-translations';
    protected $description = 'Synchronize all translation keys across all 6 locales';

    public function handle(): int
    {
        $enPath = base_path('lang/en.json');
        if (!file_exists($enPath)) {
            $this->error('lang/en.json not found!');
            return Command::FAILURE;
        }

        $en = json_decode(file_get_contents($enPath), true) ?: [];

        $locales = ['ml', 'hi', 'ar', 'fr', 'de', 'ta', 'kn', 'it'];

        foreach ($locales as $locale) {
            $path = base_path("lang/{$locale}.json");
            $data = file_exists($path) ? json_decode(file_get_contents($path), true) ?: [] : [];

            // Add missing keys with fallback or translated value
            foreach ($en as $k => $v) {
                if (!isset($data[$k]) || empty($data[$k])) {
                    $data[$k] = $v;
                }
            }

            file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            $this->info("✓ Synchronized lang/{$locale}.json (" . count($data) . " keys)");
        }

        return Command::SUCCESS;
    }
}
