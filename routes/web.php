<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/**
 * Redirect root URL (/) to products list page.
 * When user opens the home page, we directly send them to products.index route.
 */
Route::get('/', function () {
    return redirect()->route('products.index');
});

/**
 * Resource Route for CRUD
 * This single line creates all 7 routes:
 * index, create, store, show, edit, update, destroy
 */
Route::resource('products', ProductController::class);

/**
 * Restore Deleted Product (Soft Delete Restore)
 * This route is used when you want to restore a soft-deleted product.
 * Example URL: /products/restore/5
 */
Route::get('products/restore/{id}', [ProductController::class, 'restore'])
    ->name('products.restore');

// Export products as CSV
Route::get('products/export/csv', [ProductController::class, 'exportCSV'])->name('products.export.csv');
