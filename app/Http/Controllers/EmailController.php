<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Services\EmailVerificationService;
use Carbon\Carbon;

class EmailController extends Controller
{
    public function sendCode(Request $request) 
    { 
        $request->validate([ 'email' => 'required|email' ]); 
        
        $email = $request->email; 
        
        $attempts = session()->get('otp_attempts', 0); 
        
        if ($attempts >= 3) { 
            return response()->json([ 
                'message' => 'Bạn đã gửi mã quá nhiều lần' 
            ], 429); 
        } 
        
        $lastSent = session()->get('otp_last_sent_at'); 
        
        if ($lastSent && Carbon::now()->diffInSeconds($lastSent) < 60) 
        { 
            return response()->json([ 
                'message' => 'Vui lòng đợi 60 giây để gửi lại' 
            ], 429); 
        } 
    
        $service = app(EmailVerificationService::class); 
        $otp = $service->create($email); 
        
        session([ 
            'booking_email' => $email, 
            'booking_verified' => false, 
            'otp_attempts' => $attempts + 1, 
            'otp_last_sent_at' => now(), ]
        ); 
        
        Mail::raw("Mã OTP của bạn là: $otp", function ($m) use ($email) { 
            $m->to($email) ->subject('Mã xác nhận đặt lịch'); 
        }); 
        
        return response()->json([ 
            'message' => 'Đã gửi mã', 
            'attempts_left' => 3 - ($attempts + 1) 
        ]); 
    } 
    
    public function verifyCode(Request $request) { 
        if (session('booking_verified')) 
        { 
            return response()->json([ 
                'message' => 'Đã xác nhận rồi' 
            ]); 
        } 
        
        $request->validate([ 
            'email' => 'required|email', 
            'code' => 'required|digits:6' 
        ]); 
        
        if ($request->email !== session('booking_email')) 
        { 
            return response()->json([ 
                'message' => 'Email không khớp' 
            ], 422); 
        } 
        
        $service = app(EmailVerificationService::class); 
        if (! $service->verify($request->email, $request->code)) 
        { 
            return response()->json([
                'message' => 'Sai mã' 
            ], 422); 
        } 
        
        session([ 
            'booking_verified' => true, 
            'otp_attempts' => 0, 
            'otp_last_sent_at' => null, 
        ]); 
        
        return response()->json([ 'message' => 'OK' ]); 
    }

    public function resetOtp()
    {
        session()->forget([
            'otp_last_sent_at',
            'otp_attempts',
            'booking_verified',
            'booking_email',
        ]);

        return response()->json(['ok' => true]);
    }
}