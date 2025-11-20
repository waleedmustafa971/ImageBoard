<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'supervisor.can_moderate' => \App\Http\Middleware\EnsureSupervisorCanModerate::class,
            'check.banned' => \App\Http\Middleware\CheckIfBanned::class,
            'rate.limit.posts' => \App\Http\Middleware\RateLimitPosts::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
