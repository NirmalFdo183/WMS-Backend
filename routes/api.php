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
use App\Http\Controllers\PasswordResetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/
Route::get('/ping', PingController::class);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [PasswordResetController::class, 'sendCode']);
Route::post('/reset-password', [PasswordResetController::class, 'reset']);

/*
|--------------------------------------------------------------------------
| Authenticated User Base Routes
|--------------------------------------------------------------------------
| Available to any authenticated token.
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

/*
|--------------------------------------------------------------------------
| POS Operations (Cashier, Admin & Super Admin Access)
|--------------------------------------------------------------------------
| Accessible to both 'admin' and 'cashier' roles for POS checkout & sales
*/
Route::middleware(['auth:sanctum', 'role:super_admin,admin,cashier,data_entry'])->group(function () {
    // Inventory reading for POS catalog, scanning, and stock checks
    Route::get('/batch-stocks', [Batch_StockController::class, 'index']);
    Route::get('/batch-stocks/{batch_stock}', [Batch_StockController::class, 'show']);
    Route::get('/batch-stocks/product/{productId}', [Batch_StockController::class, 'byProduct']);
    Route::get('/products/search', [ProductController::class, 'search']);

});

// POS sales are reserved for checkout staff and management.
Route::middleware(['auth:sanctum', 'role:super_admin,admin,cashier'])->group(function () {
    Route::get('/sales', [SaleController::class, 'index']);
    Route::get('/sales/{sale}', [SaleController::class, 'show']);
    Route::post('/sales', [SaleController::class, 'store']);
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| End-of-day data entry
|--------------------------------------------------------------------------
| Data-entry users can record deliveries, loadings and returns, and read
| the reference data required by those forms. They cannot alter master data.
*/
Route::middleware(['auth:sanctum', 'role:super_admin,admin,data_entry'])->group(function () {
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/suppliers', [SupplierController::class, 'index']);
    Route::get('/supplier-invoices', [SupplierInvoiceController::class, 'index']);
    Route::get('/trucks', [TruckController::class, 'index']);
    Route::get('/routes', [RouteController::class, 'index']);
    Route::get('/employees', [EmployeeController::class, 'index']);
    Route::get('/sales-reps', [SalesRepController::class, 'index']);
    Route::get('/loadings', [LoadingController::class, 'index']);
    Route::get('/returns', [LoadingReturnController::class, 'index']);
    Route::post('/supplies', [SupplyController::class, 'store']);
    Route::post('/loadings', [LoadingController::class, 'store']);
    Route::post('/loadings/{loadingId}/returns', [LoadingReturnController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Full System & Warehouse Management (Owner & Admin Access)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'role:super_admin,admin'])->group(function () {
    // User provisioning: admins can manage data-entry users and cashiers;
    // only the owner can manage administrator accounts (enforced by the controller).
    Route::post('/register', [AuthController::class, 'register']);
    Route::apiResource('users', UserController::class);
    Route::put('/users/{user}/password', [UserController::class, 'updatePassword']);

    // Financial valuation
    Route::get('/supplier-invoices/total-sum', [SupplierInvoiceController::class, 'totalSum']);

    // Product master catalog management
    Route::apiResource('products', ProductController::class)->except(['search', 'index']);

    // Batch stock management (creation, manual edits, deletions outside sales)
    Route::post('/batch-stocks', [Batch_StockController::class, 'store']);
    Route::put('/batch-stocks/{batch_stock}', [Batch_StockController::class, 'update']);
    Route::patch('/batch-stocks/{batch_stock}', [Batch_StockController::class, 'update']);
    Route::delete('/batch-stocks/{batch_stock}', [Batch_StockController::class, 'destroy']);

    // Warehouse master resources
    Route::apiResource('suppliers', SupplierController::class)->except('index');
    Route::apiResource('routes', RouteController::class)->except('index');
    Route::apiResource('trucks', TruckController::class)->except('index');
    Route::apiResource('employees', EmployeeController::class)->except('index');
    Route::apiResource('shops', ShopController::class);
    Route::apiResource('supplier-invoices', SupplierInvoiceController::class)->except('index');
    Route::apiResource('sales-reps', SalesRepController::class)->except('index');

    // Loadings & dispatches
    Route::apiResource('loadings', LoadingController::class)->except(['index', 'store']);
    Route::apiResource('loading-items', LoadingItemsController::class);

    // Inward stock supply & Dashboard metrics
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats']);
    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
});
