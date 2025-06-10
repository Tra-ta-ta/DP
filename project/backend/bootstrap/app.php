<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\Auth;
use App\Http\Middleware\Guest;
use App\Http\Middleware\User;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__ . '/../routes/channels.php',
        ['prefix' => 'api', 'middleware' => ['api', 'auth:sanctum']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->alias([
            'Admin' => Admin::class,
            'User' => User::class,
            'Auth' => Auth::class,
            'Guest' => Guest::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
