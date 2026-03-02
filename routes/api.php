<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthenticationController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\CartController;

// Group everything under V1
Route::prefix('v1')->group(function () {

    // Auth Routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthenticationController::class, 'register']);
        Route::post('/login', [AuthenticationController::class, 'authenticate']);
        Route::post('/verify-token', [AuthenticationController::class, 'verifyUser']);
        Route::post('/resend-token', [AuthenticationController::class, 'resendToken']);
    });

    // Order Routes
    Route::prefix('orders')->group(function () {
        Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/place', [OrderController::class, 'placeOrder']);
            Route::post('/{orderId}/initiate-payment', [OrderController::class, 'initiatePayment']);
        });
        Route::get('/', [OrderController::class, 'getOrders']);
        Route::get('/{id}', [OrderController::class, 'getOrderById']);
    });

    // Product Routes
    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
            Route::post('/add', [ProductController::class, 'store']);
            Route::put('/{productId}', [ProductController::class, 'update']);
            Route::put('/{productId}/restock', [ProductController::class, 'restock']);
        });
    });

    // Cart Routes (Now merged)
    Route::prefix('cart')->middleware('auth:sanctum')->group(function () {
        Route::get('/{userId}', [CartController::class, 'show']);
        Route::post('/{userId}/add', [CartController::class, 'addItem']);
        Route::post('/{userId}/remove', [CartController::class, 'removeItem']);
    });

    // Payment Webhooks (No Auth)
    Route::post('payments/webhook', [PaymentController::class, 'handlePaystackWebhook']);
});
