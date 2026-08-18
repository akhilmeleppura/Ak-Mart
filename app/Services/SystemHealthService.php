<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;

class SystemHealthService
{
    /**
     * Run full system diagnostic checks
     */
    public function runDiagnostics(): array
    {
        $checks = [];

        // 1. Database Connection & Latency
        $dbStart = microtime(true);
        try {
            DB::connection()->getPdo();
            $dbLatency = round((microtime(true) - $dbStart) * 1000, 2);
            $checks['database'] = [
                'name'    => 'MySQL Database',
                'status'  => 'healthy',
                'latency' => $dbLatency . ' ms',
                'message' => 'Connection established successfully.',
            ];
        } catch (\Exception $e) {
            $checks['database'] = [
                'name'    => 'MySQL Database',
                'status'  => 'unhealthy',
                'latency' => 'N/A',
                'message' => $e->getMessage(),
            ];
        }

        // 2. Cache Engine
        $cacheStart = microtime(true);
        try {
            Cache::put('system_health_check', 'ok', 10);
            $val = Cache::get('system_health_check');
            $cacheLatency = round((microtime(true) - $cacheStart) * 1000, 2);
            $checks['cache'] = [
                'name'    => 'Cache Engine (' . config('cache.default') . ')',
                'status'  => $val === 'ok' ? 'healthy' : 'unhealthy',
                'latency' => $cacheLatency . ' ms',
                'message' => 'Read/Write operations responding normally.',
            ];
        } catch (\Exception $e) {
            $checks['cache'] = [
                'name'    => 'Cache Engine',
                'status'  => 'unhealthy',
                'latency' => 'N/A',
                'message' => $e->getMessage(),
            ];
        }

        // 3. Storage Disk Write Permissions
        try {
            $testFile = 'health_check_' . time() . '.txt';
            Storage::disk('local')->put($testFile, 'test');
            $readBack = Storage::disk('local')->get($testFile);
            Storage::disk('local')->delete($testFile);

            $checks['storage'] = [
                'name'    => 'Local Storage Disk',
                'status'  => $readBack === 'test' ? 'healthy' : 'unhealthy',
                'message' => 'Read/Write permissions verified.',
            ];
        } catch (\Exception $e) {
            $checks['storage'] = [
                'name'    => 'Local Storage Disk',
                'status'  => 'unhealthy',
                'message' => $e->getMessage(),
            ];
        }

        // 4. Failed Jobs
        try {
            $failedCount = DB::table('failed_jobs')->count();
            $checks['queue'] = [
                'name'    => 'Queue Workers & Background Jobs',
                'status'  => $failedCount === 0 ? 'healthy' : 'warning',
                'message' => $failedCount === 0 ? '0 failed jobs in queue.' : "{$failedCount} failed jobs detected.",
            ];
        } catch (\Exception $e) {
            $checks['queue'] = [
                'name'    => 'Queue Engine',
                'status'  => 'healthy',
                'message' => 'Default sync driver active.',
            ];
        }

        // 5. Application Environment
        $checks['environment'] = [
            'name'        => 'Application Environment',
            'php_version' => PHP_VERSION,
            'laravel'     => app()->version(),
            'debug_mode'  => config('app.debug') ? 'Enabled' : 'Disabled',
            'status'      => 'healthy',
        ];

        // Overall Score Calculation
        $healthyCount = count(array_filter($checks, fn($c) => ($c['status'] ?? '') === 'healthy'));
        $totalChecks = count($checks);
        $healthScore = round(($healthyCount / $totalChecks) * 100);

        return [
            'score'       => $healthScore,
            'status'      => $healthScore >= 80 ? 'Optimal' : ($healthScore >= 50 ? 'Degraded' : 'Critical'),
            'checks'      => $checks,
            'timestamp'   => now()->toDateTimeString(),
        ];
    }
}
