<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // Alias middleware
        $middleware->alias([
            'is_admin'             => \App\Http\Middleware\IsAdmin::class,
            'email.verified.custom'=> \App\Http\Middleware\EnsureEmailVerified::class,
        ]);

            $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Kecualikan webhook Midtrans dari CSRF
        $middleware->validateCsrfTokens(except: [
            'payment/webhook',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();