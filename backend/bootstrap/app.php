<?php

use App\Http\Middleware\CheckStatus;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 1. Включаем поддержку сессий/cookie для фронтенда (Sanctum)
        $middleware->statefulApi();
        $middleware->alias([
            'check.status' => CheckStatus::class,
        ]);


       // $middleware->appendToGroup('api', \App\Http\Middleware\CheckStatus::class);

        // НАПРАВЛЯЕМ ОЧЕРЕДЬ: Говорим Laravel, что CheckStatus должен выполняться строго после аутентификации сессий
       /* $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \App\Http\Middleware\CheckStatus::class, // Наш мидлвар встает после них
        ]);*/

        //$middleware->append(\App\Http\Middleware\CheckStatus::class);
        /* $middleware->web(append: [
             \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
         ]);*/
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // ЕСЛИ ПРОИСХОДИТ ОШИБКА АВТОРИЗАЦИИ — ВОЗВРАЩАЕМ JSON ВМЕСТО РЕДИРЕКТА
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return true; // Всегда возвращать JSON при ошибках
        });
        /* $exceptions->shouldRenderJsonWhen(
             fn (Request $request) => $request->is('auth/*'),
         );*/
    })->create();
