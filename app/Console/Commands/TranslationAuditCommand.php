<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TranslationAuditCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'akmart:translation-audit {--detail : Show specific missing keys for each locale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit translation completeness across all supported locales (en, ml, hi, ar, fr, de)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=================================================");
        $this->info("   AK-MART GLOBAL TRANSLATION PARITY AUDIT       ");
        $this->info("=================================================");

        $locales = ['en', 'ml', 'hi', 'ar', 'fr', 'de'];
        $baseFile = base_path('lang/en.json');

        if (!file_exists($baseFile)) {
            $this->error("Baseline lang/en.json not found!");
            return Command::FAILURE;
        }

        $baseTranslations = json_decode(file_get_contents($baseFile), true) ?: [];
        $totalKeys = count($baseTranslations);
        $baseKeys = array_keys($baseTranslations);

        $tableRows = [];
        $allMissing = [];

        foreach ($locales as $locale) {
            $filePath = base_path("lang/{$locale}.json");
            if (!file_exists($filePath)) {
                $tableRows[] = [$locale, 'MISSING FILE', 0, $totalKeys, '0%'];
                continue;
            }

            $currentTranslations = json_decode(file_get_contents($filePath), true) ?: [];
            $currentKeys = array_keys($currentTranslations);

            $missing = array_diff($baseKeys, $currentKeys);
            $missingCount = count($missing);
            $presentCount = count($currentKeys);
            $coverage = $totalKeys > 0 ? round((($totalKeys - $missingCount) / $totalKeys) * 100, 1) : 100;

            $tableRows[] = [
                strtoupper($locale),
                $missingCount === 0 ? '✓ COMPLETE' : '⚠ PARTIAL',
                $presentCount,
                $missingCount,
                $coverage . '%'
            ];

            if ($missingCount > 0) {
                $allMissing[$locale] = $missing;
            }
        }

        $this->table(
            ['Locale', 'Status', 'Total Keys', 'Missing Keys', 'Coverage'],
            $tableRows
        );

        if ($this->option('detail') && !empty($allMissing)) {
            $this->newLine();
            $this->warn("Missing Keys Breakdown:");
            foreach ($allMissing as $loc => $missingKeys) {
                $this->line("<fg=yellow>[{$loc}]</> " . implode(', ', array_slice($missingKeys, 0, 10)) . (count($missingKeys) > 10 ? ' ... and ' . (count($missingKeys) - 10) . ' more' : ''));
            }
        }

        $this->newLine();
        $this->info("✓ Audit Complete. All 6 core locales checked successfully.");
        return Command::SUCCESS;
    }
}
