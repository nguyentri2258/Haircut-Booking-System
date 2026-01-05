<?php

namespace App\Services;

use App\Models\EmailVerification;

class EmailVerificationService
{
    public function create(string $email): string
    {
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        EmailVerification::updateOrCreate(
            ['email' => $email],
            [
                'code' => $code,
                'expires_at' => now()->addMinute(),
            ]
        );

        return $code;
    }

    public function verify(string $email, string $code): bool
    {
        return EmailVerification::where('email', $email)
            ->where('code', $code)
            ->where('expires_at', '>', now())
            ->exists();
    }
}