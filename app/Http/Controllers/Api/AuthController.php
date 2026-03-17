<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Empleado;
use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\ForgotPassword;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:empleados|unique:users',
            'dni' => 'required|string|max:20|unique:empleados',
            'telefono' => 'nullable|string|max:20',
            'id_rol' => 'required|exists:roles,id_rol',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // crea usuario
            $user = User::create([
                'name' => $request->nombre . ' ' . $request->apellido,
                'email' => $request->email,
                'password' => Hash::make('1234'),
            ]);

            // crea empleado
            $empleado = Empleado::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'email' => $request->email,
                'dni' => $request->dni,
                'telefono' => $request->telefono,
                'id_user' => $user->id,
                'id_rol' => $request->id_rol,
            ]);

            DB::commit();

            // token incluyendo rol
            $rol = Rol::find($request->id_rol);
            $abilities = [$rol->nombre]; // capacidad del token => rol

            $token = $user->createToken('auth_token', $abilities)->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario registrado exitosamente',
                'user' => $user,
                'empleado' => $empleado,
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

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'         => 'required|email',
                'password'      => 'required',
                'captcha_token' => 'required|string',
            ]);

            // verificar si la cuenta está bloqueada por backoff progresivo
            $backoffKey  = 'login_backoff:' . strtolower($request->email);
            $attemptsKey = 'login_attempts:' . strtolower($request->email);
            $backoffExpiry = Cache::get($backoffKey);

            if ($backoffExpiry && now()->lessThan($backoffExpiry)) {
                $waitSeconds = (int) now()->diffInSeconds($backoffExpiry);
                $waitMinutes = ceil($waitSeconds / 60);

                Log::warning('Login bloqueado por backoff progresivo', [
                    'email'        => $request->email,
                    'ip'           => $request->ip(),
                    'wait_minutes' => $waitMinutes,
                ]);

                return response()->json([
                    'status'      => 'error',
                    'message'     => "Cuenta temporalmente bloqueada. Intenta de nuevo en {$waitMinutes} minuto(s).",
                    'retry_after' => $waitSeconds,
                ], 429);
            }

            // verificar Turnstile CAPTCHA antes de consultar la BD
            if (!$this->verifyTurnstile($request->captcha_token, $request->ip())) {
                Log::warning('Turnstile verification failed', [
                    'email' => $request->email,
                    'ip'    => $request->ip(),
                ]);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Verificación de seguridad fallida. Recarga la página e intenta de nuevo.',
                ], 422);
            }

            $user = User::where('email', $request->email)->first();

            if (!$user) {
                // registrar intento fallido en backoff (cuenta no encontrada)
                $this->registrarIntentoFallido($attemptsKey, $backoffKey, $request, null);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'Esta cuenta no está registrada en Digimedia.'
                ], 404);
            }

            if (!Hash::check($request->password, $user->password)) {
                // registrar intento fallido en backoff (contraseña incorrecta)
                $this->registrarIntentoFallido($attemptsKey, $backoffKey, $request, $user);

                return response()->json([
                    'status'  => 'error',
                    'message' => 'El email o la contraseña son incorrectos.'
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

            return response()->json([
                'status'   => 'success',
                'user'     => $user,
                'empleado' => $empleado,
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
     * Los contadores expiran automáticamente a las 2 horas de inactividad.
     */
    private function registrarIntentoFallido(string $attemptsKey, string $backoffKey, Request $request, ?User $user): void
    {
        if (Cache::has($attemptsKey)) {
            $attempts = Cache::increment($attemptsKey);
        } else {
            $attempts = 1;
            Cache::put($attemptsKey, 1, now()->addHours(2));
        }

        $lockoutMinutes = match (true) {
            $attempts >= 15 => 60,
            $attempts >= 10 => 15,
            $attempts >= 7  => 5,
            $attempts >= 4  => 2,
            default         => 0,
        };

        if ($lockoutMinutes > 0) {
            Cache::put($backoffKey, now()->addMinutes($lockoutMinutes), now()->addMinutes($lockoutMinutes));
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
            'reason'               => !$user ? 'user_not_found' : 'wrong_password',
            'accumulated_attempts' => $attempts,
        ]);
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

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

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

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        Log::info('Token recibido: ' . $request->token);

        $tokenUser = DB::table('password_reset_tokens')
            ->whereRaw('LOWER(token) = ?', [strtolower($request->token)])
            ->first();

        if (!$tokenUser) {
            $exactToken = DB::table('password_reset_tokens')
                ->where('token', $request->token)
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

        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_reset_tokens')->where('token', $request->token)->delete();

        return response()->json(['message' => 'Contraseña actualizada correctamente, ingresa desde el login'], 200);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        $empleado = $user->empleado;
        $rol = $empleado ? $empleado->rol : null;

        $permisos = $rol ? $rol->permisos->pluck('slug')->toArray() : [];

        return response()->json([
            'user' => $user,
            'empleado' => $empleado,
            'rol' => $rol ? $rol->nombre : null,
            'abilities' => $user->currentAccessToken()->abilities,
            'permisos' => $permisos
        ]);
    }
}
