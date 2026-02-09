<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;

// Home Route
Route::get('/', [HomeController::class, 'index'])->name('home');

// Product Routes
Route::resource('products', ProductController::class);
Route::delete('/products/{product}/image', [ProductController::class, 'removeImage'])
     ->name('products.remove-image');