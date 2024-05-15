<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Models\Register;


class OtpController extends Controller
{

    public function sendOtp(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'fname' => 'required|alpha',
            'lname' => 'required|alpha',
        ], [
            'fname.alpha' => 'The first name must contain only alphabetic characters.',
            'lname.alpha' => 'The last name must contain only alphabetic characters.',
        ]);

        $email = $validatedData['email'];
        $fname = $validatedData['fname'];
        $lname = $validatedData['lname'];

        $otp = rand(100000, 999999);

        Mail::to($email)->send(new OtpMail($otp));

        Cache::put('otp_' . $email, ['otp' => $otp, 'fname' => $fname, 'lname' => $lname], now()->addSeconds(60));

        return response()->json([
            'message'=>'OTP sent successfully',
            'status' => 'false',
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

            Cache::forget('otp_' . $email);
            return response()->json([
                'status' => 'Verification successful',
            ]);
        } else {
            return response()->json([
                'status' => 'Invalid OTP. Please try again.',
            ], 422);
        }
    }



public function dataSubmit(Request $request)
{
    try {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'fname' => 'required|string',
            'lname' => 'required|string',
            'mobile' => 'required|string',
            'messenger' => 'nullable|string',
            'password' => 'required|string|min:8',
            'confirmpassword' => 'required|string|same:password',
        ]);

        $email = $validatedData['email'];
        $firstName = $validatedData['fname'];
        $lastName = $validatedData['lname'];
        $mobile = $validatedData['mobile'];
        $messenger = $validatedData['messenger'];
        $password = $validatedData['password'];
        $confirmPassword = $validatedData['confirmpassword'];

        $user = new Register([
            'email' => $email,
            'f_name' => $firstName,
            'l_name' => $lastName,
            'mobile' => $mobile,
            'messenger' => $messenger,
            'password' => Hash::make($password),
            'confirmpassword' => Hash::make($password),
            'status'=>'active'
        ]);
        $user->save();
        return response()->json(['message' => 'User created successfully'], 201);

    } catch (ValidationException $e) {
        return response()->json(['message' => $e->errors()], 422);
    } catch (\Exception $e) {
        return response()->json(['message' => $e->getMessage()], 500);
    }

}




}
