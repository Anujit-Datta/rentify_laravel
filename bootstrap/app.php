<?php

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
        // Remove EnsureFrontendRequestsAreStateful to allow bearer tokens from mobile apps/Postman
        // If you need CSRF protection for web forms, add it only to web routes

        $middleware->alias([
            'check-role' => \App\Http\Middleware\CheckRole::class,
            'check-ownership' => \App\Http\Middleware\CheckOwnership::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'auth.optional' => \App\Http\Middleware\OptionalAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
