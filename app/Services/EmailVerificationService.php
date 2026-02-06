<?php

namespace App\Services;

use App\Models\EmailVerification;
use Illuminate\Support\Facades\Http;

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

        if (! $record) {
            return false;
        }

        $record->delete();

        return true;
    }

    public static function sendOtp(string $email, string $code): void
    {
        self::send(
            $email,
            'Mã xác nhận đặt lịch',
            view('bookings.email', [
                'otp' => $code,
            ])->render()
        );
    }

    protected static function send(
        string $toEmail,
        string $subject,
        string $htmlContent
    ): void {
        Http::withHeaders([
            'api-key' => config('services.brevo.key'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'email' => config('mail.from.address'),
                'name'  => config('mail.from.name'),
            ],
            'to' => [
                ['email' => $toEmail],
            ],
            'subject' => $subject,
            'htmlContent' => $htmlContent,
        ])->throw();
    }
}
