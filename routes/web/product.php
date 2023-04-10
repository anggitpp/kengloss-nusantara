<?php

use App\Http\Controllers\Product\MasterCategoryController;
use App\Http\Controllers\Product\MasterFileController;
use App\Http\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::name('products.')->group(function () {
    Route::get('/products/products/data', [ProductController::class, 'data'])->name('products.data');
    Route::resource('/products/products', ProductController::class);

    Route::get('/products/master-files/data', [MasterFileController::class, 'data'])->name('master-files.data');
    Route::resource('/products/master-files', MasterFileController::class);

    Route::get('/products/master-categories/data', [MasterCategoryController::class, 'data'])->name('master-categories.data');
    Route::resource('/products/master-categories', MasterCategoryController::class);
});


