<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Response;
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
            'email' => 'required|email|unique:registers,email',
            'fname' => 'required|alpha',
            'lname' => 'required|alpha',
        ], [
            'email.unique' => 'The email address has already been taken.',
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
            'message' => 'OTP sent successfully',
            'status' => true,
            'otp' => $otp,
        ]);
    }



    public function verifyOtp(Request $request) {
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

    public function valContact(Request $request)
    {
        $mobile = $request->input('mobile');
        $existingUser = Register::where('mobile', $mobile)->first();

        if ($existingUser) {
            // Mobile number already registered, return an error response
            return response()->json(['error' => 'This contact info is already registered.'], 422);
        } else {
            // Mobile number is not registered, return a success response
            return response()->json(['message' => 'Mobile number is available.'], 200);
        }
    }






public function dataSubmit(Request $request){
    try {
        $validatedData = $request->validate([
            'email' => 'required|email|unique:registers,email',
            'fname' => 'required|string|max:20',
            'lname' => 'required|string|max:20',
            'mobile' => 'required|string',
            'messenger' => 'nullable|string|max:10',
            'password' => [
                'required',
                'string',
                'max:8',
                'min:4',
                'regex:/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{4,8}$/',
            ],
            'confirmpassword' => 'required|string|same:password',
        ], [
            'email.unique' => 'Email is already registered',
            'password.regex' => 'Password must contain at least one letter, one number, and one special character',
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



    public function dashboard(Request $request)
    {
        $latestUser = Register::latest()->first();

        if ($latestUser) {
            $userData = [
                'id' => $latestUser->id,
                'fname' => $latestUser->f_name,
                'lname' => $latestUser->l_name,
                'email' => $latestUser->email,
                'mobile' => $latestUser->mobile,
                'messenger' => $latestUser->messenger,
            ];

            return response()->json([
                'success' => true,
                'message' => 'Latest user data retrieved successfully',
                'data' => $userData,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No user found',
                'data' => null,
            ], 404);
        }
    }


}
