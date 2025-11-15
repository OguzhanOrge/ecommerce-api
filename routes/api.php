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

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/test/{id}', [TestController::class, 'index']);
