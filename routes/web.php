<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BarcodeScannerController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\DesktopStudioController;
use App\Http\Controllers\SupplierController;

// Dashboard & Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products CRUD
Route::resource('products', ProductController::class);
Route::delete('/products/{product}/image', [ProductController::class, 'removeImage'])->name('products.remove-image');
Route::get('/products-export', [ProductController::class, 'export'])->name('products.export');

// Feature 1: Barcode & QR Code Studio & Webcam Scanner
Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/barcodes', [BarcodeScannerController::class, 'index'])->name('barcodes');
    Route::get('/scanner', [BarcodeScannerController::class, 'scanner'])->name('scanner');
    Route::get('/barcodes/print', [BarcodeScannerController::class, 'printSheet'])->name('barcodes.print');
    Route::post('/scanner/lookup', [BarcodeScannerController::class, 'lookup'])->name('scanner.lookup');

    // Feature 3: Stock Movement Audit Ledger & Low-Stock Auto-Reorder Engine
    Route::get('/ledger', [StockMovementController::class, 'index'])->name('ledger');
    Route::post('/ledger', [StockMovementController::class, 'store'])->name('ledger.store');
    Route::get('/watchdog', [StockMovementController::class, 'reorderWatchdog'])->name('watchdog');
    Route::get('/purchase-order', [StockMovementController::class, 'generatePurchaseOrder'])->name('po.generate');
});

// Feature 5: Native Desktop Integration & OS Notification Radar
Route::prefix('desktop')->name('desktop.')->group(function () {
    Route::get('/studio', [DesktopStudioController::class, 'index'])->name('studio');
});

// Global Desktop Spotlight Search & Notification Radar APIs
Route::get('/api/spotlight-search', [DesktopStudioController::class, 'spotlightSearch'])->name('api.spotlight.search');
Route::get('/api/test-notification', [DesktopStudioController::class, 'testNotification'])->name('api.test.notification');

// Feature 7: Suppliers Directory & Vendor Management
Route::resource('suppliers', SupplierController::class);

// Dashboard & Profile Routes (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

