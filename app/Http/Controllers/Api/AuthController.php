<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Resources\EmpleadoResource;
use App\Models\User;
use App\Models\Empleado;
use App\Models\Rol;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();
        try {
            // crea usuario
            $user = User::create([
                'name' => $validated['nombre'] . ' ' . $validated['apellido'],
                'email' => $validated['email'],
                'password' => Hash::make('1234'),
            ]);

            // crea empleado
            $empleado = Empleado::create([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email' => $validated['email'],
                'dni' => $validated['dni'],
                'telefono' => $validated['telefono'] ?? null,
                'id_user' => $user->id,
                'id_rol' => $validated['id_rol'],
            ]);

            DB::commit();

            // token incluyendo rol
            $rol = Rol::find($validated['id_rol']);
            $abilities = [$rol->nombre]; // capacidad del token => rol

            $token = $user->createToken('auth_token', $abilities)->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario registrado exitosamente',
                'user' => $user,
                'empleado' => new EmpleadoResource($empleado),
                'rol' => $rol->nombre,
                'token' => $token,
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al registrar usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $validated = $request->validated();
            $email = $validated['email'];

            $normalizedEmail = strtolower($email);
            $backoffKey = 'login_backoff:' . $normalizedEmail;
            $attemptsKey = 'login_attempts:' . $normalizedEmail;

            // verificar Turnstile CAPTCHA antes de consultar la BD
            if (!$this->verifyTurnstile($validated['captcha_token'], $request->ip())) {
                Log::warning('Turnstile verification failed', [
                    'email' => $email,
                    'ip'    => $request->ip(),
                ]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Verificación de seguridad fallida. Recarga la página e intenta de nuevo.',
                    ], 422);
                }
                    
            $activeBackoffSeconds = $this->obtenerEsperaBackoffSegundos($backoffKey);
            if ($activeBackoffSeconds > 0) {
                $attemptResult = $this->registrarIntentoFallido(
                    $attemptsKey,
                    $backoffKey,
                    $request,
                    'blocked_by_backoff'
                );

                return $this->responderBloqueoBackoff(
                    $request,
                    max($activeBackoffSeconds, $attemptResult['wait_seconds'])
                );
            }

            $user = User::where('email', $email)->first();

            if (!$user) {
                $attemptResult = $this->registrarIntentoFallido(
                    $attemptsKey,
                    $backoffKey,
                    $request,
                    'user_not_found'
                );

                if ($attemptResult['wait_seconds'] > 0) {
                    return $this->responderBloqueoBackoff($request, $attemptResult['wait_seconds']);
                }

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Esta cuenta no está registrada en Digimedia.',
                ], 404);
            }

            if (!Hash::check($validated['password'], $user->password)) {
                $attemptResult = $this->registrarIntentoFallido(
                    $attemptsKey,
                    $backoffKey,
                    $request,
                    'wrong_password'
                );

                if ($attemptResult['wait_seconds'] > 0) {
                    return $this->responderBloqueoBackoff($request, $attemptResult['wait_seconds']);
                }

                return response()->json([
                    'status'  => 'error',
                    'message' => 'El email o la contraseña son incorrectos.',
                ], 401);
            }

            // login exitoso → resetear contadores de backoff
            Cache::forget($backoffKey);
            Cache::forget($attemptsKey);

            $empleado = $user->empleado;
            if (!$empleado || !$empleado->rol) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'El usuario no tiene un rol asignado'
                ], 403);
            }

            /**
             * Aquí se carga la información necesaria para la cookie que almacenará la jerarquía
             * de administrador
             */
            $empleado->load(['rol', 'subtipoAdmin']);

            $rol = $empleado->rol;
            $abilities = [$rol->nombre];
            $permisos = $rol->permisos->pluck('slug')->toArray();

            // quitar tokens anteriores
            $user->tokens()->delete();
            // token incluyendo rol (capcidad)
            $token = $user->createToken('auth_token', $abilities)->plainTextToken;

            // Evitar que la relación empleado se serialice dentro de user
            $user->unsetRelation('empleado');

            return response()->json([
                'status'   => 'success',
                'user'     => $user,
                'empleado' => new EmpleadoResource($empleado),
                'rol'      => $rol->nombre,
                'permisos' => $permisos,
                'token'    => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error en el servidor',
                'error'   => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    //logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    /**
     * Incrementa el contador de intentos fallidos y aplica bloqueo progresivo si corresponde.
     * Escala: 4+ intentos → 2min, 7+ → 5min, 10+ → 15min, 15+ → 60min.
     * Los contadores expiran automáticamente tras 120 minutos de inactividad,
     * asegurando TTL mayor al mayor bloqueo configurado.
     */
    private function registrarIntentoFallido(
        string $attemptsKey,
        string $backoffKey,
        Request $request,
        string $reason
    ): array
    {
        if (Cache::has($attemptsKey)) {
            $attempts = Cache::increment($attemptsKey);
        } else {
            $attempts = 1;
        }

        Cache::put($attemptsKey, $attempts, now()->addMinutes(120));

        $lockoutMinutes = $this->obtenerMinutosBloqueo($attempts);
        $waitSeconds = 0;

        if ($lockoutMinutes > 0) {
            $backoffExpiry = now()->addMinutes($lockoutMinutes);
            Cache::put($backoffKey, $backoffExpiry->timestamp, $backoffExpiry);
            $waitSeconds = $this->obtenerEsperaBackoffSegundos($backoffKey);

            Log::warning('Backoff progresivo activado', [
                'email'           => $request->email,
                'ip'              => $request->ip(),
                'attempts'        => $attempts,
                'lockout_minutes' => $lockoutMinutes,
            ]);
        }

        Log::info('Login fallido', [
            'email'                => $request->email,
            'ip'                   => $request->ip(),
            'reason'               => $reason,
            'accumulated_attempts' => $attempts,
        ]);

        return [
            'attempts'     => $attempts,
            'wait_seconds' => $waitSeconds,
        ];
    }

    private function obtenerMinutosBloqueo(int $attempts): int
    {
        return match (true) {
            $attempts >= 15 => 60,
            $attempts >= 10 => 15,
            $attempts >= 7  => 5,
            $attempts >= 4  => 2,
            default         => 0,
        };
    }

    private function obtenerEsperaBackoffSegundos(string $backoffKey): int
    {
        $rawExpiry = Cache::get($backoffKey);
        if (!$rawExpiry) {
            return 0;
        }

        if ($rawExpiry instanceof \DateTimeInterface) {
            $expiryTimestamp = $rawExpiry->getTimestamp();
        } elseif (is_numeric($rawExpiry)) {
            $expiryTimestamp = (int) $rawExpiry;
        } else {
            try {
                $expiryTimestamp = Carbon::parse((string) $rawExpiry)->timestamp;
            } catch (\Throwable $e) {
                Cache::forget($backoffKey);
                return 0;
            }
        }

        $waitSeconds = $expiryTimestamp - now()->timestamp;
        if ($waitSeconds <= 0) {
            Cache::forget($backoffKey);
            return 0;
        }

        return $waitSeconds;
    }

    private function responderBloqueoBackoff(Request $request, int $waitSeconds): JsonResponse
    {
        $waitMinutes = (int) ceil($waitSeconds / 60);

        Log::warning('Login bloqueado por backoff progresivo', [
            'email'                => $request->email,
            'ip'                   => $request->ip(),
            'wait_minutes'         => $waitMinutes,
            'retry_after_seconds'  => $waitSeconds,
        ]);

        return response()->json([
            'status'      => 'error',
            'message'     => "Cuenta temporalmente bloqueada. Intenta de nuevo en {$waitMinutes} minuto(s).",
            'retry_after' => $waitSeconds,
        ], 429);
    }

    /**
     * Verifica el token de Cloudflare Turnstile contra la API de siteverify.
     * Si TURNSTILE_SECRET_KEY está vacío o no definido
     */
    private function verifyTurnstile(string $token, string $ip): bool
    {
        $secret = config('services.turnstile.secret_key');

        try {
            $response = Http::asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret'   => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);

            return $response->successful() && ($response->json('success') === true);
        } catch (\Exception $e) {
            Log::error('Turnstile API error', [
                'error' => $e->getMessage(),
                'ip'    => $ip,
            ]);

            return false;
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $validated = $request->validated();
        $email = $validated['email'];

        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'El usuario no existe'
            ], 404);
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $token,
                'created_at' => now()
            ]
        );

        Mail::to($user->email)->send(new ForgotPassword($user, $token));

        return response()->json([
            'status' => 'success',
            'message' => 'Token de restablecimiento de contraseña enviado'
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $validated = $request->validated();
        $token = $validated['token'];

        Log::info('Token recibido: ' . $token);

        $tokenUser = DB::table('password_reset_tokens')
            ->whereRaw('LOWER(token) = ?', [strtolower($token)])
            ->first();

        if (!$tokenUser) {
            $exactToken = DB::table('password_reset_tokens')
                ->where('token', $token)
                ->first();

            Log::info('Token no encontrado. Tokens disponibles: ' .
                json_encode(DB::table('password_reset_tokens')->pluck('token')->toArray()));

            return response()->json([
                'status' => 'error',
                'message' => 'Token inválido o expirado'
            ], 404);
        }

        $user = User::where('email', $tokenUser->email)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        DB::table('password_reset_tokens')->where('token', $token)->delete();

        return response()->json(['message' => 'Contraseña actualizada correctamente, ingresa desde el login'], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $empleado = $user->empleado;
        $rol = $empleado ? $empleado->rol : null;

        $permisos = $rol ? $rol->permisos->pluck('slug')->toArray() : [];

        $user->unsetRelation('empleado');

        return response()->json([
            'user' => $user,
            'empleado' => $empleado ? new EmpleadoResource($empleado) : null,
            'rol' => $rol ? $rol->nombre : null,
            'abilities' => $user->currentAccessToken()->abilities,
            'permisos' => $permisos
        ]);
    }
}
