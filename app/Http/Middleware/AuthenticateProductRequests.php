<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateProductRequests
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            return $next($request);
        }

        $expectedToken = env('PRODUCTS_API_TOKEN', 'local-demo-token');
        $token = $request->bearerToken();

        if ($token !== null && hash_equals($expectedToken, $token)) {
            return $next($request);
        }

        return response()->json([
            'message' => 'Unauthenticated.',
        ], 401);
    }
}