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
use App\Http\Controllers\LoadingReturnController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PingController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::get('/ping', PingController::class);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Authenticated User Base Routes
|--------------------------------------------------------------------------
| Available to any authenticated token (admin or cashier)
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

/*
|--------------------------------------------------------------------------
| POS Operations (Cashier & Admin Access)
|--------------------------------------------------------------------------
| Accessible to both 'admin' and 'cashier' roles for POS checkout & sales
*/
Route::middleware(['auth:sanctum', 'role:admin,cashier'])->group(function () {
    // Inventory reading for POS catalog, scanning, and stock checks
    Route::get('/batch-stocks', [Batch_StockController::class, 'index']);
    Route::get('/batch-stocks/{batch_stock}', [Batch_StockController::class, 'show']);
    Route::get('/batch-stocks/product/{productId}', [Batch_StockController::class, 'byProduct']);
    Route::get('/products/search', [ProductController::class, 'search']);

    // POS sales checkout, history view, and voiding
    Route::get('/sales', [SaleController::class, 'index']);
    Route::get('/sales/{sale}', [SaleController::class, 'show']);
    Route::post('/sales', [SaleController::class, 'store']);
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Full System & Warehouse Management (Admin Access Only)
|--------------------------------------------------------------------------
| Strictly restricted to 'admin' role. Cashiers will receive 403 Forbidden.
*/
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    // User provisioning & management (Admins & Cashiers)
    Route::post('/register', [AuthController::class, 'register']);
    Route::apiResource('users', UserController::class);
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword']);

    // Financial valuation
    Route::get('/supplier-invoices/total-sum', [SupplierInvoiceController::class, 'totalSum']);

    // Product master catalog management
    Route::apiResource('products', ProductController::class)->except(['search']);

    // Batch stock management (creation, manual edits, deletions outside sales)
    Route::post('/batch-stocks', [Batch_StockController::class, 'store']);
    Route::put('/batch-stocks/{batch_stock}', [Batch_StockController::class, 'update']);
    Route::patch('/batch-stocks/{batch_stock}', [Batch_StockController::class, 'update']);
    Route::delete('/batch-stocks/{batch_stock}', [Batch_StockController::class, 'destroy']);

    // Warehouse master resources
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('routes', RouteController::class);
    Route::apiResource('trucks', TruckController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('shops', ShopController::class);
    Route::apiResource('supplier-invoices', SupplierInvoiceController::class);
    Route::apiResource('sales-reps', SalesRepController::class);

    // Loadings & dispatches
    Route::apiResource('loadings', LoadingController::class);
    Route::post('/loadings/{loadingId}/returns', [LoadingReturnController::class, 'store']);
    Route::get('/returns', [LoadingReturnController::class, 'index']);
    Route::apiResource('loading-items', LoadingItemsController::class);

    // Inward stock supply & Dashboard metrics
    Route::post('/supplies', [SupplyController::class, 'store']);
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
});
