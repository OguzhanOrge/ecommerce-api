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

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/test/{id}', [TestController::class, 'index']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::apiResource('users', UserController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);


/*
|--------------------------------------------------------------------------
| JWT Protected Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    Route::get('/profile', function () {
        return auth()->user();
    });

    /*
    |--------------------------------------------------------------------------
    | Cart Endpoints
    |--------------------------------------------------------------------------
    */
    Route::prefix('cart')->group(function () {
        Route::get('/',             [CartController::class, 'index']);
        Route::post('/add',         [CartController::class, 'add']);
        Route::put('/update',       [CartController::class, 'update']);
        Route::delete('/remove/{product_id}', [CartController::class, 'remove']);
        Route::delete('/clear',     [CartController::class, 'clear']);
    });

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */
    Route::post('/orders',            [OrderController::class, 'makeOrder']);
    Route::get('/orders',             [OrderController::class, 'index']);
    Route::get('/orders/{orderId}',   [OrderController::class, 'show']);
    Route::put('/orders/{orderId}',   [OrderController::class, 'update']);

});
