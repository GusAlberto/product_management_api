<?php

use App\Http\Controllers\Api\ProductController;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login-temporario', function () {
    User::firstOrCreate(
        ['email' => 'admin@example.com'],
        [
            'name' => 'Admin Demo',
            'password' => 'password',
        ]
    );

    return view('session-login', [
        'swaggerToken' => env('PRODUCTS_API_TOKEN', 'local-demo-token'),
    ]);
})->name('login');

Route::get('/login', function () {
    return redirect('/login-temporario');
});

Route::middleware(['auth', 'throttle:products-api'])->get('/browser/products', [ProductController::class, 'index']);

Route::post('/session-login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    if (! Auth::attempt($credentials)) {
        return response()->json([
            'message' => 'Credenciais inválidas.',
        ], 422);
    }

    $request->session()->regenerate();

    return response()->json([
        'message' => 'Sessão autenticada criada com sucesso.',
    ]);
})->withoutMiddleware(ValidateCsrfToken::class);
