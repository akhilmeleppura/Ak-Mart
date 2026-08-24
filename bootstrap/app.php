<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\LocaleMiddleware;
use App\Http\Middleware\BranchSessionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->web(LocaleMiddleware::class);
        $middleware->web(BranchSessionMiddleware::class);
        $middleware->validateCsrfTokens(except: [
            '/store/ai-assistant/chat',
            '/store/sandbox/*',
            'api/*',
            'webhook/*',
        ]);
        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard'
        );
        $middleware->alias([
            'branch.access' => \App\Http\Middleware\EnsureBranchAccess::class,
            'tenant.subscription' => \App\Http\Middleware\EnsureActiveSubscription::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        // Run dunning processing every day at 8:00 AM
        $schedule->command('dunning:process')->dailyAt('08:00')->withoutOverlapping();
    })
    ->create();
