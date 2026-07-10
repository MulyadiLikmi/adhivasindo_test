<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes - Pasarin (Toko Online Sederhana)
|--------------------------------------------------------------------------
| Semua route di sini otomatis diprefix /api dan pakai driver JWT (auth:api).
*/

// ==== Auth (public) ====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ==== Produk (public: list & search) ====
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// ==== Kategori (public: dipakai untuk filter chip di landing page) ====
Route::get('/categories', [CategoryController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // ==== Customer only: transaksi ====
    Route::middleware('role:customer')->group(function () {
        Route::post('/checkout', [OrderController::class, 'checkout']);
        Route::get('/my-orders', [OrderController::class, 'myOrders']);
    });

    // ==== Admin only: kelola toko & laporan ====
    Route::middleware('role:admin')->group(function () {
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{product}', [ProductController::class, 'update']);
        Route::delete('/products/{product}', [ProductController::class, 'destroy']);

        Route::get('/report/sales', [OrderController::class, 'report']);
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus']);
    });
});
