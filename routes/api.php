<?php
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Ecommerce API",
 *      description="Ecommerce API Documentation",
 *      @OA\Contact(
 *          email="example@example.com"
 *      )
 * )
 */
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test/{id}', [TestController::class, 'index']);
Route::apiResource('users', UserController::class);
Route::apiResource('categories', CategoryController::class);
// Products resource (correct controller)
Route::apiResource('products', ProductController::class)->middleware('jwt.auth');

// Cart routes (explicit endpoints) protected by JWT middleware
Route::group(['prefix' => 'cart', 'middleware' => ['jwt.auth']], function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/add', [CartController::class, 'add']);
    Route::put('/update', [CartController::class, 'update']);
    Route::delete('/remove/{product_id}', [CartController::class, 'remove']);
    Route::delete('/clear', [CartController::class, 'clear']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::group(['middleware' => ['jwt.auth']], function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::get('/profile', function () {
        return auth()->user();
    });

    Route::post('/orders', [OrderController::class, 'makeOrder']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{orderId}', [OrderController::class, 'show']);
});
