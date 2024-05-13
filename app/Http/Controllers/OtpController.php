<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Log;
use App\Models\Register;


class OtpController extends Controller
{

    public function sendOtp(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'fname' => 'required|string',
            'lname' => 'required|string',
        ]);

        $email = $validatedData['email'];
        $fname = $validatedData['fname'];
        $lname = $validatedData['lname'];

        $otp = rand(100000, 999999);

        Mail::to($email)->send(new OtpMail($otp));

        Cache::put('otp_' . $email, ['otp' => $otp, 'fname' => $fname, 'lname' => $lname], now()->addSeconds(60));

        return response()->json([
            'status' => 'OTP sent successfully',
            'otp' => $otp,
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
        ]);

        $email = $validatedData['email'];
        $otp = $validatedData['otp'];

        $cachedData = Cache::get('otp_' . $email);

        if ($cachedData && $cachedData['otp'] == $otp) {
            Register::create([
                'email' => $email,
                'f_name' => $cachedData['fname'],
                'l_name' => $cachedData['lname'],
                'status' => "Active"
            ]);

            Cache::forget('otp_' . $email);

            return response()->json([
                'status' => 'Registration successful',
            ]);
        } else {
            return response()->json([
                'status' => 'Invalid OTP. Please try again.',
            ], 422);
        }
    }



}
