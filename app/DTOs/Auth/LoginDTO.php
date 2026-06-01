<?php

namespace APP\DTOs\Auth;

use App\Http\Requests\Auth\LoginRequest;

class LoginDTO 
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $captcha_token,
        public readonly string $ip
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        $validated = $request->validated();
        return new self(
            email: $validated['email'],
            password: $validated['password'],
            captcha_token: $validated['captcha_token'],
            ip: $request->ip()
        );
    }
}