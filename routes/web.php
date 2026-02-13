<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\CartController;

Route::get('/cart/{userId}', [CartController::class, 'show']);
Route::post('/cart/add/{userId}', [CartController::class, 'addItem']);
Route::post('/cart/remove/{userId}', [CartController::class, 'removeItem']);
