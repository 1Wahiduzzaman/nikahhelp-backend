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
        //
        $middleware->appendToGroup('api', [
            \App\Http\Middleware\Cors::class,
         \App\Http\Middleware\JwtMiddleware::class,
         \App\Http\Middleware\AdminMiddleware::class,
         \App\Http\Middleware\EnsureProfileCompleted::class,
         \App\Http\Middleware\Authenticate::class,

    ])
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
