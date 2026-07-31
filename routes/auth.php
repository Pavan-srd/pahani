<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Show login form (same as before)
    Route::get('login', [\App\Http\Controllers\Auth\OtpLoginController::class, 'showLogin'])
        ->name('login');

    // Send OTP after email/password verification (instead of direct login)
    Route::post('login', [\App\Http\Controllers\Auth\OtpLoginController::class, 'sendOtp'])
        ->name('login.sendOtp');

    // Show OTP verification page
    Route::get('verify-otp', [\App\Http\Controllers\Auth\OtpLoginController::class, 'showOtpVerify'])
        ->name('otp.verify');

    // Verify OTP and log in
    Route::post('verify-otp', [\App\Http\Controllers\Auth\OtpLoginController::class, 'verifyOtp'])
        ->name('otp.verify.store');

    // Resend OTP
    Route::post('resend-otp', [\App\Http\Controllers\Auth\OtpLoginController::class, 'resendOtp'])
        ->name('otp.resend');

    // Keep existing password reset routes
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'update'])->name('password.update');

    // Register routes (if you have them)
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
