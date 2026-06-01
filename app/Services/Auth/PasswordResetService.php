<?php

namespace App\Services\Auth;

use App\Mail\ForgotPassword;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Mail;

class PasswordResetService
{
    public function __construct(
        private PasswordResetRepository $passwordResetRepository,
        private UserRepository $userRepository
    ) {}

    public function sendResetToken(string $email): void
    {
        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new \Exception('El usuario no existe', 404);
        }

        $token = $this->passwordResetRepository->createOrUpdate($email);
        Mail::to($user->email)->send(new ForgotPassword($user, $token));
    }

    public function resetPassword(string $token, string $newPassword): void
    {
        $tokenUser = $this->passwordResetRepository->findByToken($token);

        if (!$tokenUser) {
            throw new \Exception('Token inválido o expirado', 404);
        }

        $user = $this->userRepository->findByEmail($tokenUser->email);

        if (!$user) {
            throw new \Exception('Usuario no encontrado', 404);
        }

        $this->userRepository->updatePassword($user, $newPassword);
        $this->passwordResetRepository->deleteByToken($token);
    }
}