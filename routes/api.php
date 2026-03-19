<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ── Public Auth Routes ────────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ── Protected Routes ──────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Shop
    Route::get('/shop',          [ShopController::class, 'show']);
    Route::post('/shop',         [ShopController::class, 'upsert']);
    Route::post('/shop/logo',    [ShopController::class, 'uploadLogo']);

    // Products — barcode lookup MUST come before {product} to avoid route conflicts
    Route::get('/products/barcode/{barcode}', [ProductController::class, 'findByBarcode']);
    Route::apiResource('/products', ProductController::class);

    // Transactions
    Route::apiResource('/transactions', TransactionController::class)->only([
        'index', 'store', 'show',
    ]);
});
