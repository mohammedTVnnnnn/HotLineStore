<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\InvoiceController;

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

// User Routes
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{id}', [UserController::class, 'show']);
    Route::post('/', [UserController::class, 'store']);
    Route::put('/{id}', [UserController::class, 'update']);
    Route::delete('/{id}', [UserController::class, 'destroy']);
    Route::get('/{id}/relations', [UserController::class, 'showWithRelations']);
    Route::get('/role/{role}', [UserController::class, 'getByRole']);
});

// Product Routes
Route::prefix('products')->group(function () {
    // Public routes (available to everyone)
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/search', [ProductController::class, 'search']);
    Route::get('/price-range', [ProductController::class, 'getByPriceRange']);
    Route::get('/low-stock', [ProductController::class, 'getLowStock']);
    Route::get('/{id}', [ProductController::class, 'show']);
    
    // Admin only routes
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/', [ProductController::class, 'store']);
        Route::put('/{id}', [ProductController::class, 'update']);
        Route::put('/{id}/stock', [ProductController::class, 'updateStock']);
        Route::delete('/{id}', [ProductController::class, 'destroy']);
    });
});

// Cart Routes
Route::prefix('carts')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::get('/{id}', [CartController::class, 'show']);
    Route::post('/', [CartController::class, 'store']);
    Route::delete('/{id}', [CartController::class, 'destroy']);
    Route::get('/user/{userId}', [CartController::class, 'getUserCart']);
    Route::post('/{cartId}/add-product', [CartController::class, 'addProduct']);
    Route::put('/items/{cartItemId}/quantity', [CartController::class, 'updateItemQuantity']);
    Route::delete('/items/{cartItemId}', [CartController::class, 'removeProduct']);
    Route::get('/{cartId}/total', [CartController::class, 'calculateTotal']);
    Route::post('/{cartId}/clear', [CartController::class, 'clear']);
    Route::get('/{cartId}/items-count', [CartController::class, 'getItemsCount']);
    Route::get('/{cartId}/validate-checkout', [CartController::class, 'validateForCheckout']);
});

// Invoice Routes
Route::prefix('invoices')->group(function () {
    Route::get('/', [InvoiceController::class, 'index']);
    Route::get('/{id}', [InvoiceController::class, 'show']);
    Route::post('/from-cart', [InvoiceController::class, 'createFromCart']);
    Route::post('/', [InvoiceController::class, 'store']);
    Route::put('/{id}/status', [InvoiceController::class, 'updateStatus']);
    Route::delete('/{id}', [InvoiceController::class, 'destroy']);
    Route::get('/user/{userId}', [InvoiceController::class, 'getUserInvoices']);
    Route::get('/status/{status}', [InvoiceController::class, 'getByStatus']);
    Route::get('/date-range', [InvoiceController::class, 'getByDateRange']);
    Route::get('/sales-statistics', [InvoiceController::class, 'getSalesStatistics']);
    Route::get('/total-sales', [InvoiceController::class, 'calculateTotalSales']);
});

// ==================== NEW CART SYSTEM ROUTES ====================
// Cart System Routes (Protected by auth middleware)
Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'showUserCart']);           // عرض السلة
    Route::post('/', [CartController::class, 'addToCart']);            // إضافة منتج للسلة
    Route::put('/{id}', [CartController::class, 'updateCartItem']);     // تعديل الكمية
    Route::delete('/{id}', [CartController::class, 'removeFromCart']);   // حذف منتج من السلة
});

// ==================== NEW INVOICE SYSTEM ROUTES ====================
// Invoice System Routes (Protected by auth middleware)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/checkout', [InvoiceController::class, 'checkout']);              // إنشاء فاتورة من السلة
    Route::get('/invoices', [InvoiceController::class, 'indexUserInvoices']);      // عرض فواتير المستخدم
    Route::get('/invoices/{id}', [InvoiceController::class, 'showUserInvoice']);   // عرض تفاصيل فاتورة
});