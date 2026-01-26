<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\InventoryLocationController;
use App\Http\Controllers\Api\ProductImageController;
use App\Http\Controllers\Api\InventoryMovementController;
use App\Http\Controllers\Api\BarcodeController;
use App\Http\Controllers\Api\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Product Management API Routes
Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/stats', [ProductController::class, 'stats']);
    Route::get('/low-stock', [ProductController::class, 'lowStock']);
    Route::get('/out-of-stock', [ProductController::class, 'outOfStock']);
    Route::get('/top-selling', [ProductController::class, 'topSelling']);
    Route::post('/bulk-update', [ProductController::class, 'bulkUpdate']);
    Route::delete('/bulk-delete', [ProductController::class, 'bulkDelete']);
    
    Route::prefix('{id}')->group(function () {
        Route::get('/', [ProductController::class, 'show']);
        Route::put('/', [ProductController::class, 'update']);
        Route::delete('/', [ProductController::class, 'destroy']);
        Route::get('/analytics', [ProductController::class, 'analytics']);
    });
});

// Category Management API Routes
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::post('/', [CategoryController::class, 'store']);
    Route::get('/tree', [CategoryController::class, 'tree']);
    Route::get('/stats', [CategoryController::class, 'stats']);
    
    Route::prefix('{id}')->group(function () {
        Route::get('/', [CategoryController::class, 'show']);
        Route::put('/', [CategoryController::class, 'update']);
        Route::delete('/', [CategoryController::class, 'destroy']);
    });
});

// Supplier Management API Routes
Route::prefix('suppliers')->group(function () {
    Route::get('/', [SupplierController::class, 'index']);
    Route::post('/', [SupplierController::class, 'store']);
    Route::get('/select', [SupplierController::class, 'select']);
    Route::get('/stats', [SupplierController::class, 'stats']);
    
    Route::prefix('{id}')->group(function () {
        Route::get('/', [SupplierController::class, 'show']);
        Route::put('/', [SupplierController::class, 'update']);
        Route::delete('/', [SupplierController::class, 'destroy']);
        Route::get('/products', [SupplierController::class, 'products']);
    });
});

// Inventory Location Management API Routes
Route::prefix('inventory-locations')->group(function () {
    Route::get('/', [InventoryLocationController::class, 'index']);
    Route::post('/', [InventoryLocationController::class, 'store']);
    Route::get('/select', [InventoryLocationController::class, 'select']);
    Route::get('/stats', [InventoryLocationController::class, 'stats']);
    
    Route::prefix('{id}')->group(function () {
        Route::get('/', [InventoryLocationController::class, 'show']);
        Route::put('/', [InventoryLocationController::class, 'update']);
        Route::delete('/', [InventoryLocationController::class, 'destroy']);
        Route::get('/inventory', [InventoryLocationController::class, 'inventory']);
    });
});

// Product Image Management API Routes
Route::prefix('product-images')->group(function () {
    Route::get('/', [ProductImageController::class, 'index']);
    Route::post('/', [ProductImageController::class, 'store']);
    Route::post('/upload', [ProductImageController::class, 'upload']);
    Route::post('/reorder', [ProductImageController::class, 'reorder']);
    
    Route::prefix('{id}')->group(function () {
        Route::get('/', [ProductImageController::class, 'show']);
        Route::put('/', [ProductImageController::class, 'update']);
        Route::delete('/', [ProductImageController::class, 'destroy']);
        Route::post('/set-primary', [ProductImageController::class, 'setPrimary']);
    });
});

// Inventory Movement Management API Routes
Route::prefix('inventory-movements')->group(function () {
    Route::get('/', [InventoryMovementController::class, 'index']);
    Route::post('/', [InventoryMovementController::class, 'store']);
    Route::get('/stats', [InventoryMovementController::class, 'stats']);
    Route::get('/types', [InventoryMovementController::class, 'types']);
    Route::get('/monthly-summary', [InventoryMovementController::class, 'monthlySummary']);
    Route::get('/trends', [InventoryMovementController::class, 'trends']);
    Route::get('/by-product/{productId}', [InventoryMovementController::class, 'byProduct']);
    Route::get('/by-location/{locationId}', [InventoryMovementController::class, 'byLocation']);
    
    Route::prefix('{id}')->group(function () {
        Route::get('/', [InventoryMovementController::class, 'show']);
    });
});

// Inventory Management API Routes
Route::prefix('inventory')->group(function () {
    Route::post('/update', [InventoryController::class, 'updateInventory']);
    Route::post('/reserve', [InventoryController::class, 'reserveInventory']);
    Route::post('/unreserve', [InventoryController::class, 'unreserveInventory']);
    Route::post('/transfer', [InventoryController::class, 'transferInventory']);
    Route::get('/by-location', [InventoryController::class, 'getByLocation']);
    Route::get('/low-stock-alerts', [InventoryController::class, 'lowStockAlerts']);
    Route::get('/out-of-stock-alerts', [InventoryController::class, 'outOfStockAlerts']);
    Route::get('/movement-history', [InventoryController::class, 'movementHistory']);
    Route::get('/valuation', [InventoryController::class, 'valuation']);
    Route::get('/monthly-summary', [InventoryController::class, 'monthlySummary']);
    Route::post('/set-reorder-point', [InventoryController::class, 'setReorderPoint']);
    Route::get('/stats', [InventoryController::class, 'stats']);
});

// Barcode Management API Routes
Route::prefix('barcodes')->group(function () {
    Route::post('/generate', [BarcodeController::class, 'generate']);
    Route::post('/generate-sequential', [BarcodeController::class, 'generateSequential']);
    Route::post('/generate-image', [BarcodeController::class, 'generateImage']);
    Route::post('/validate', [BarcodeController::class, 'validate']);
    Route::post('/check-exists', [BarcodeController::class, 'checkExists']);
    Route::post('/bulk-generate', [BarcodeController::class, 'bulkGenerate']);
});

// Payment Management API Routes
Route::prefix('payments')->group(function () {
    Route::get('/', [PaymentController::class, 'index']);
    Route::post('/', [PaymentController::class, 'store']);
    Route::get('/stats', [PaymentController::class, 'stats']);
    
    Route::prefix('{id}')->group(function () {
        Route::get('/', [PaymentController::class, 'show']);
        Route::put('/', [PaymentController::class, 'update']);
        Route::delete('/', [PaymentController::class, 'destroy']);
    });
});
