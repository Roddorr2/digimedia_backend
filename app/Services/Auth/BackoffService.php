<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BackoffService
{
    private const ATTEMPTS_TTL_MINUTES = 120;

    public function registerFailedAttempt(string $email, string $ip, string $reason): void
    {
        $attemptsKey = $this->getAttemptsKey($email);
        $backoffKey = $this->getBackoffKey($email);

        $attempts = Cache::has($attemptsKey) ? Cache::increment($attemptsKey) : 1;
        Cache::put($attemptsKey, $attempts, now()->addMinutes(self::ATTEMPTS_TTL_MINUTES));

        $lockoutMinutes = $this->getLockoutMinutes($attempts);

        if ($lockoutMinutes > 0) {
            $backoffExpiry = now()->addMinutes($lockoutMinutes);
            Cache::put($backoffKey, $backoffExpiry->timestamp, $backoffExpiry);
        }

        Log::info('Login fallido', [
            'email' => $email,
            'ip' => $ip,
            'reason' => $reason,
            'attempts' => $attempts,
        ]);
    }

    public function isBlocked(string $email): bool
    {
        return $this->getRemainingWaitSeconds($email) > 0;
    }

    public function getRemainingWaitSeconds(string $email): int
    {
        $backoffKey = $this->getBackoffKey($email);
        $rawExpiry = Cache::get($backoffKey);

        if (!$rawExpiry) {
            return 0;
        }

        $expiryTimestamp = $rawExpiry instanceof \DateTimeInterface
            ? $rawExpiry->getTimestamp()
            : (int) $rawExpiry;

        $waitSeconds = $expiryTimestamp - now()->timestamp;

        if ($waitSeconds <= 0) {
            Cache::forget($backoffKey);
            return 0;
        }

        return $waitSeconds;
    }

    public function reset(string $email): void
    {
        Cache::forget($this->getBackoffKey($email));
        Cache::forget($this->getAttemptsKey($email));
    }

    private function getLockoutMinutes(int $attempts): int
    {
        return match (true) {
            $attempts >= 15 => 60,
            $attempts >= 10 => 15,
            $attempts >= 7 => 5,
            $attempts >= 4 => 2,
            default => 0,
        };
    }

    private function getAttemptsKey(string $email): string
    {
        return 'login_attempts:' . strtolower($email);
    }

    private function getBackoffKey(string $email): string
    {
        return 'login_backoff:' . strtolower($email);
    }
}