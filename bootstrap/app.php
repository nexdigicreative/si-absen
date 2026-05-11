<?php

use App\Http\Middleware\CheckActiveUser;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        // Register named middlewares
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'check.active' => CheckActiveUser::class,
        ]);

        // CheckActiveUser applied via route middleware 'check.active' — not duplicated here
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e) {
            return response()->view('errors.403', ['message' => $e->getMessage()], 403);
        });
    })
    ->create();