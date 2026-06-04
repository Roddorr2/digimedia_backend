<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PasswordResetRepository
{
    public function createOrUpdate(string $email): string
    {
        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()]
        );
        return $token;
    }

    public function findByToken(string $token): ?object
    {
        return DB::table('password_reset_tokens')
            ->whereRaw('LOWER(token) = ?', [strtolower($token)])
            ->first();
    }

    public function deleteByToken(string $token): void
    {
        DB::table('password_reset_tokens')->where('token', $token)->delete();
    }
}