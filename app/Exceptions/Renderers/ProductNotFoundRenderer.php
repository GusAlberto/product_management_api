<?php

namespace App\Exceptions\Renderers;

use App\Models\Product;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductNotFoundRenderer
{
    public function __invoke(NotFoundHttpException $exception, Request $request): ?\Illuminate\Http\JsonResponse
    {
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
    }
}