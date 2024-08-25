<?php
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'getUser']);
Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('cart-items', CartController::class);
    Route::post('payments', [PaymentController::class, 'store']);
    Route::get('payments/{id}', [PaymentController::class, 'show']);
    Route::apiResource('categories', CategoryController::class);
Route::apiResource('subcategories', SubcategoryController::class);
});
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/subcategories', [SubcategoryController::class, 'index']);
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::put('/products/{product}', [ProductController::class, 'update']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
});
Route::get('/getUsers', [UserController::class, 'getUsers']);
Route::post('/verify-email', [UserController::class, 'verifyEmail']);

Route::put('/resetPassword', [UserController::class, 'resetPassword']);
Route::apiResource('orders', OrderController::class);

Route::get('/getCards', [OrderController::class, 'getCards']);

