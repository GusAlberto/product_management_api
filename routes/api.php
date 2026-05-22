<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'throttle:products-api'])->apiResource('products', ProductController::class);