<?php

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
        'jwt.auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Sadece API isteklerinde (veya JSON bekleniyorsa) çalış
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return true;
            }
            return false;
        });
        //401 DOĞRULAMA HATASI
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            // Laravel'de bu hata normalde web uygulamalarında login sayfasına
            // yönlendirir. Biz sadece JSON bekleyen isteklerde (API) JSON dönmeliyiz.
            if ($request->expectsJson()) {
                // İstediğiniz global hata yapısını kullanın
                return response()->json([
                    'success' => false,
                    'message' => 'Erişim reddedildi. Geçerli bir oturum açın veya token sağlayın.',
                    'data' => (object) [],
                    'errors' => []
                ], 401);
            }
        });

        // 🛑 403 YETKİLENDİRME HATASI (AuthorizationException'dan türetilen hata)
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            // ^------ ARTIK AuthorizationException yerine bu hatayı yakalıyoruz.

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu eylemi gerçekleştirme yetkiniz yok.',
                    'data' => (object) [],
                    'errors' => []
                ], 403); // Status Code 403
            }

            // Web isteği ise
            return response()->view('errors.403', [], 403);
        });
        // 404 Hataları (Model veya Rota bulunamadı)
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'Kaynak bulunamadı.',
                'data' => (object) [],
                'errors' => []
            ], 404);
        });

        // Validasyon Hataları
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'Gönderilen veriler geçersiz.',
                'data' => (object) [],
                'errors' => $e->errors() // Hata detayları
            ], 422);
        });

        // Diğer tüm genel hatalar (500 vb.)
        $exceptions->render(function (Throwable $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => config('app.debug') ? $e->getMessage() : 'Sunucu hatası oluştu.',
                'data' => (object) [],
                'errors' => []
            ], 500);
        });


    })->create();
