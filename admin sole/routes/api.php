<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ShippingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| File ini berisi rute API (prefix default /api).
| Semua rute di sini otomatis berada pada "api" middleware group (rate limiting, dll.).
| Konvensi respons: JSON.
*/

/* =========================================================================
|  GET /api/user
|  Middleware: auth:sanctum
|  Handler   : Closure → return $request->user()
|  Fungsi    : Mengambil data user yang sedang login (berdasarkan token Sanctum).
=========================================================================== */
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/* =========================================================================
|  AUTH (PUBLIC)
|  POST /api/register      → AuthController@register
|  POST /api/login         → AuthController@login
=========================================================================== */
Route::post('register', [AuthController::class, 'register']);
Route::post('login',    [AuthController::class, 'login']);

/* =========================================================================
|  PRODUCT (PUBLIC)
|  GET /api/products           → ProductController@index
|  GET /api/products/{product} → ProductController@show
=========================================================================== */
Route::get('products',           [ProductController::class, 'index']);
Route::get('products/{product}', [ProductController::class, 'show']);

/* =========================================================================
|  SHIPPING QUOTE (PUBLIC)
|  POST /api/shipping/quote → ShippingController@quote
|  Dipakai oleh halaman keranjang (fetch("/api/shipping/quote", ...)).
=========================================================================== */
Route::post('shipping/quote', [ShippingController::class, 'quote']);

/* =========================================================================
|  GRUP RUTE TERPROTEKSI (BUTUH TOKEN)
|  Middleware: auth:api
|  Catatan: pastikan guard di AuthController konsisten (sanctum/passport).
=========================================================================== */
Route::middleware('auth:api')->group(function () {

    /* -------------------------- USER INFO / LOGOUT ----------------------- */
    Route::get('user',    [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    /* -------------------------- CART (KERANJANG) ------------------------- */
    Route::get('cart',                         [CartController::class, 'index']);
    Route::post('cart/add',                    [CartController::class, 'addToCart']);
    Route::put('cart/{cartItem}',              [CartController::class, 'updateCartItem']);
    Route::delete('cart/{cartItem}',           [CartController::class, 'removeFromCart']);
    Route::delete('cart/item/{itemId}',        [CartController::class, 'destroy']);
    Route::patch('cart/item/{itemId}',         [CartController::class, 'updateQty']);

    /* ----------------------------- ORDER --------------------------------- */
    Route::post('checkout',                         [OrderController::class, 'checkout']);
    Route::post('orders/{order}/upload-proof',      [OrderController::class, 'uploadPaymentProof']);
    Route::get('orders',                            [OrderController::class, 'orderHistory']);
    Route::get('unpaid-orders',                     [OrderController::class, 'unpaidOrders']);
    Route::get('orders/{orderId}',                  [OrderController::class, 'orderDetail']);
});
