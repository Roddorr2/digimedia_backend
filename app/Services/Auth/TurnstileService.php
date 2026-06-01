<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TurnstileService
{
    public function verify(string $token, string $ip): bool
    {
        $secret = config('services.turnstile.secret_key');

        if (empty($secret)) {
            return false;
        }

        try {
            $response = Http::withoutVerifying()
                ->asForm()
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => $secret,
                    'response' => $token,
                    'remoteip' => $ip,
                ]);

            return $response->successful() && $response->json('success') === true;
        } catch (\Exception $e) {
            Log::error('Turnstile API error', ['error' => $e->getMessage(), 'ip' => $ip]);
            return false;
        }
    }
}