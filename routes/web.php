<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//users related routes
Route::get('/users', [App\Http\Controllers\Web\UserController::class, 'index'])->name('users.index');
Route::post('/users/store', [App\Http\Controllers\Web\UserController::class, 'store'])->name('users.store');
Route::post('/users/edit', [App\Http\Controllers\Web\UserController::class, 'edit'])->name('users.edit');
Route::post('/users/delete', [App\Http\Controllers\Web\UserController::class, 'destroy'])->name('users.delete');

//products related routes
Route::get('/products', [App\Http\Controllers\Web\ProductController::class, 'index'])->name('products.index');
Route::post('/products/store', [App\Http\Controllers\Web\ProductController::class, 'store'])->name('products.store');
Route::post('/products/edit', [App\Http\Controllers\Web\ProductController::class, 'edit'])->name('products.edit');
Route::post('/products/delete', [App\Http\Controllers\Web\ProductController::class, 'destroy'])->name('products.delete');

//orders related routes
Route::get('/orders', [App\Http\Controllers\Web\OrderController::class, 'index'])->name('orders.index');
Route::post('/orders/store', [App\Http\Controllers\Web\OrderController::class, 'store'])->name('orders.store');
Route::post('/orders/edit', [App\Http\Controllers\Web\OrderController::class, 'edit'])->name('orders.edit');
Route::post('/orders/cancel', [App\Http\Controllers\Web\OrderController::class, 'cancel'])->name('orders.cancel');

//transactions related routes
Route::post('/transactions', [App\Http\Controllers\Web\TransactionController::class, 'index'])->name('transactions.index');
Route::post('/transactions/status', [App\Http\Controllers\Web\TransactionController::class, 'status'])->name('transactions.status');
