<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Services\Auth\AuthService;
use App\Services\Auth\PasswordResetService;
use App\DTOs\Auth\LoginDTO;
use App\DTOs\Auth\RegisterDTO;
use App\Http\Resources\EmpleadoResource;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private PasswordResetService $passwordResetService
    ) {}

    public function register(RegisterRequest $request)
    {
        try {
            $result = $this->authService->register(RegisterDTO::fromRequest($request));
            return response()->json([
                'status' => 'success',
                'message' => 'Usuario registrado exitosamente',
                'user' => $result['user'],
                'empleado' => new EmpleadoResource($result['empleado']),
                'rol' => $result['rol'],
                'permisos' => $result['permisos'],
                'token' => $result['token'],
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error al registrar usuario', 'error' => config('app.debug') ? $e->getMessage() : null], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $result = $this->authService->login(LoginDTO::fromRequest($request));
            return response()->json([
                'status' => 'success',
                'user' => $result['user'],
                'empleado' => new EmpleadoResource($result['empleado']),
                'rol' => $result['rol'],
                'permisos' => $result['permisos'],
                'token' => $result['token'],
            ]);
        } catch (\Exception $e) {
            $status = match ($e->getCode()) {
                401, 403, 422, 429 => $e->getCode(),
                default => 500,
            };
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $status);
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request->user());
        return response()->json(['status' => 'success', 'message' => 'Sesión cerrada exitosamente']);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try {
            $this->passwordResetService->sendResetToken($request->validated()['email']);
            return response()->json(['status' => 'success', 'message' => 'Token de restablecimiento de contraseña enviado']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->getCode() ?: 404);
        }
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            $this->passwordResetService->resetPassword($request->validated()['token'], $request->validated()['password']);
            return response()->json(['message' => 'Contraseña actualizada correctamente, ingresa desde el login'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage(), 'error' => config('app.debug') ? $e->getMessage() : null], $e->getCode() ?: 500);
        }
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