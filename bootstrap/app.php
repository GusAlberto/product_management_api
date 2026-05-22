<?php

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'product.auth' => \App\Http\Middleware\AuthenticateProductRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (! $request->expectsJson()) {
                return null;
            }

            $previous = $exception->getPrevious();

            if (! $previous instanceof ModelNotFoundException) {
                return null;
            }

            if ($previous->getModel() !== Product::class) {
                return null;
            }

            return response()->json([
                'message' => 'O produto não foi encontrado. Talvez ele tenha sido removido ou o ID esteja incorreto.',
            ], 404);
        });
    })->create();
