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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

//users related routes
Route::get('/users', [App\Http\Controllers\Web\UserController::class, 'index'])->name('users.index');
Route::post('/users/store', [App\Http\Controllers\Web\UserController::class, 'store'])->name('users.store');
Route::post('/users/edit', [App\Http\Controllers\Web\UserController::class, 'edit'])->name('users.edit');
Route::post('/users/delete', [App\Http\Controllers\Web\UserController::class, 'destroy'])->name('users.delete');
