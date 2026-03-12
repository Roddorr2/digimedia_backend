<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    
    public function boot(): void
    {

        RateLimiter::for('login', function (Request $request) {
            return [

                // Límite por IP: máx 5 intentos/min (protege contra brute force distribuido)
                Limit::perMinute(5)
                    ->by('login_ip:' . $request->ip())
                    ->response(function () {
                        Log::warning('Rate limit alcanzado por IP', [
                            'ip'    => request()->ip(),
                            'email' => request()->input('email'),
                        ]);

                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Demasiados intentos. Intenta de nuevo en unos minutos.',
                        ], 429);
                    }),

                // Límite por email: máx 3 intentos/min (protege cuentas individuales)
                Limit::perMinute(3)
                    ->by('login_email:' . strtolower($request->input('email', 'unknown')))
                    ->response(function () {
                        Log::warning('Rate limit alcanzado por email', [
                            'ip'    => request()->ip(),
                            'email' => request()->input('email'),
                        ]);

                        return response()->json([
                            'status'  => 'error',
                            'message' => 'Demasiados intentos para esta cuenta. Intenta de nuevo en unos minutos.',
                        ], 429);
                    }),
            ];
        });
    }
}