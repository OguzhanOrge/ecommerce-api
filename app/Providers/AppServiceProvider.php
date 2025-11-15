<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Başarılı yanıt makrosu
        Response::macro('success', function ($data = null, $message = 'İşlem başarılı.', $statusCode = 200) {
            return Response::json([
                'status' => 'success',
                'message' => $message,
                'data' => $data,
                'errors' => [],
            ], $statusCode);
        });

        // Hata yanıtı makrosu (Manuel hatalar için)
        Response::macro('error', function ($message = 'Bir hata oluştu.', $data = null, $statusCode = 400) {
            return Response::json([
                'status' => 'error',
                'message' => $message,
                'data' => (object) [],
                'errors' => $data,
            ], $statusCode);
        });
    }
}
