<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Display the forgot password form
     */
    public function request(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link to user's email
     * 
     * We manually generate token and send email instead of using Password broker
     */
    public function email(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'We could not find a user with that email address.',
        ]);

        $email = $request->email;

        // Find user
        $user = User::where('email', $email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        try {
            // Generate a random token (NOT hashed, stored as-is)
            $token = Str::random(80);

            // DELETE any existing tokens for this email (cleanup)
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();

            // Store token in database (plain text, not hashed)
            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now(),
            ]);

            \Log::info('Password reset token generated for ' . $email);
            \Log::info('Token stored in DB (first 20 chars): ' . substr($token, 0, 20));

            // Send email with reset link
            Mail::send('emails.password-reset', [
                'user' => $user,
                'token' => $token,
                'resetUrl' => route('password.reset', ['token' => $token]),
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Password Reset Request — Land Record Digitalization');
            });

            return back()->with('status', 'Password reset link has been sent to your email.');

        } catch (\Exception $e) {
            \Log::error('Password reset email error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send reset email. Please try again.']);
        }
    }

    /**
     * Display the password reset form
     * 
     * Token is stored plain in database, so we compare directly (no hashing)
     */
    public function reset(string $token): View
    {
        $token = urldecode($token);

        \Log::info('Reset page - Looking for token (first 20 chars): ' . substr($token, 0, 20));

        // Query password_reset_tokens table - token is stored plain!
        $passwordReset = DB::table('password_reset_tokens')
            ->where('token', $token)  // Plain comparison, NO hashing
            ->first();

        if ($passwordReset) {
            \Log::info('Token found in DB for: ' . $passwordReset->email);
        } else {
            \Log::warning('Token NOT found in DB');
            $allTokens = DB::table('password_reset_tokens')->get();
            \Log::warning('Tokens in DB: ' . json_encode($allTokens));
        }

        $email = null;
        $isValid = false;

        if ($passwordReset) {
            // Check if token is expired (60 minutes)
            if (!Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
                $email = $passwordReset->email;
                $isValid = true;
                \Log::info('Token is valid for: ' . $email);
            } else {
                \Log::warning('Token expired');
                // Delete expired token
                DB::table('password_reset_tokens')
                    ->where('token', $token)
                    ->delete();
            }
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
            'isValid' => $isValid
        ]);
    }

    /**
     * Update password with valid token
     * 
     * Token comparison is plain, no hashing needed
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',              // uppercase
                'regex:/[a-z]/',              // lowercase
                'regex:/[0-9]/',              // number
                'regex:/[!@#$%^&*()_+\-=\[\]{};:\'"|,.<>?]/',  // special char
                'confirmed'
            ],
        ], [
            'password.regex' => 'Password must contain uppercase, lowercase, number, and special character.',
            'password.min' => 'Password must be at least 8 characters.',
        ]);

        $token = urldecode($request->token);

        \Log::info('Update - Looking for token and email: ' . $request->email);

        // Verify token exists in password_reset_tokens table - plain comparison!
        $passwordReset = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            \Log::error('Token-email mismatch for: ' . $request->email);
            return back()->withErrors(['token' => 'Invalid reset token.']);
        }

        // Check if token is expired (60 minutes)
        if (Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            \Log::warning('Token expired for: ' . $request->email);
            DB::table('password_reset_tokens')
                ->where('token', $token)
                ->delete();

            return back()->withErrors(['token' => 'This password reset link has expired.']);
        }

        // Find user
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        try {
            // Update password
            $user->update([
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(60),  // Invalidate all sessions
            ]);

            // Delete the used token
            DB::table('password_reset_tokens')
                ->where('token', $token)
                ->delete();

            \Log::info('Password successfully reset for: ' . $request->email);

            // Log out from all sessions
            auth()->logout();

            return redirect()->route('login')
                ->with('status', 'Password reset successful! Please log in with your new password.');

        } catch (\Exception $e) {
            \Log::error('Password update error: ' . $e->getMessage());
            return back()->withErrors(['password' => 'Failed to update password. Please try again.']);
        }
    }
}