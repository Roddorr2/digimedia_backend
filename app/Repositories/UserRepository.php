<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function create(array $data): User
    {
        return User::create([
            'name' => $data['nombre'] . ' ' . $data['apellido'],
            'email' => $data['email'],
            'password' => Hash::make('1234'),
        ]);
    }

    public function updatePassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }

    public function deleteTokens(User $user): void
    {
        $user->tokens()->delete();
    }

    public function createToken(User $user, string $name, array $abilities = []): string
    {
        return $user->createToken($name, $abilities)->plainTextToken;
    }
}