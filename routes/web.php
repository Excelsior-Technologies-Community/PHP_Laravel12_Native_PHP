<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::resource('products', ProductController::class);

Route::delete(
     '/products/{product}/image',
     [ProductController::class, 'removeImage']
)->name('products.remove-image');

Route::get('/products-export', [ProductController::class, 'export'])
     ->name('products.export');
