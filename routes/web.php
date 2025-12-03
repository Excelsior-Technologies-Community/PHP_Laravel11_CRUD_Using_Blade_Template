<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function(){ return redirect()->route('products.index'); });
Route::resource('products', ProductController::class);
Route::get('products/restore/{id}', [ProductController::class, 'restore'])->name('products.restore');
