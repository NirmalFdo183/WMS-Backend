<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Batch_StockController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\SupplierInvoiceController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\SalesRepController;
use App\Http\Controllers\LoadingController;
use App\Http\Controllers\LoadingItemsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::get('/supplier-invoices/total-sum', [SupplierInvoiceController::class, 'totalSum']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('products', ProductController::class);
    Route::get('/batch-stocks/product/{productId}', [Batch_StockController::class, 'byProduct']);
    Route::apiResource('batch-stocks', Batch_StockController::class);
    Route::apiResource('routes', RouteController::class);
    Route::apiResource('trucks', TruckController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('shops', ShopController::class);
    Route::apiResource('supplier-invoices', SupplierInvoiceController::class);
    Route::apiResource('sales-reps', SalesRepController::class);
    Route::apiResource('loadings', LoadingController::class);
    Route::apiResource('loading-items', LoadingItemsController::class);
    Route::post('/supplies', [SupplyController::class, 'store']);
});

