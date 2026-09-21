<?php

use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\ForceHttps;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Runs before anything else, but is a no-op unless FORCE_HTTPS=true in
        // .env — see App\Http\Middleware\ForceHttps for why this isn't unconditional.
        $middleware->prepend(ForceHttps::class);

        // Needed for Sanctum's SPA cookie auth (admin.php and Laravel share a domain).
        $middleware->statefulApi();

        $middleware->alias([
            'password.changed' => EnsurePasswordChanged::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
