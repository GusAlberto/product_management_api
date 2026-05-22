<?php

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
