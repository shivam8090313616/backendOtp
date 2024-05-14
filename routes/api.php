<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OtpController;

Route::post('/send-otp', [OtpController::class, 'sendOtp']);
Route::post('/test-mail', [OtpController::class, 'testMail']);
Route::post('/verify-otp', [OtpController::class, 'verifyOtp']);
Route::post('/dataSubmit', [OtpController::class, 'dataSubmit']);
