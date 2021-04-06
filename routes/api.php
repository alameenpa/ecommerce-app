<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::middleware('auth:api')->group(function () {
    //products related
    Route::get('/products', [App\Http\Controllers\Api\ApiController::class, 'getProducts'])->name('api.products');
    Route::post('/product', [App\Http\Controllers\Api\ApiController::class, 'getSingleProduct'])->name('api.product');

    //orders related
    Route::get('/orders', [App\Http\Controllers\Api\ApiController::class, 'getOrders'])->name('api.orders');
    Route::post('/order', [App\Http\Controllers\Api\ApiController::class, 'getSingleOrder'])->name('api.order');
    Route::post('/order/cancel', [App\Http\Controllers\Api\ApiController::class, 'cancelOrder'])->name('api.order.cancel');
    Route::post('/order/create', [App\Http\Controllers\Api\ApiController::class, 'createOrder'])->name('api.order.create');
});
