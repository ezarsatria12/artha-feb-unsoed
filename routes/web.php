<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MenuController;


Route::resource('products', ProductController::class);
Route::resource('orders', OrderController::class);
Route::resource('menu', MenuController::class);