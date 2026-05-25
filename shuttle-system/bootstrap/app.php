<?php

// Increase memory limit for file uploads and processing
ini_set('memory_limit', '256M');

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
    ->withMiddleware(function (Middleware $middleware): void {
        // 🔥 Custom middleware
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'bearer.auth' => \App\Http\Middleware\BearerTokenAuth::class,
        ]);

        // 🔥 Fix redirect untuk Auth & Guest
        $middleware->redirectGuestsTo('/admin/login');
        $middleware->redirectUsersTo('/admin/dashboard');
        
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();