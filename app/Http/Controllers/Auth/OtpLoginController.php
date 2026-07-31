<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;

class OtpLoginController extends Controller
{
    /**
     * Show login form (same as before)
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle login form submission
     * Verify email/password and send OTP
     */
    public function sendOtp(Request $request): RedirectResponse
    {
        // Validate email and password
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string'],
        ], [
            'email.exists' => 'We could not find a user with that email address.',
        ]);

        $email = $request->email;

        try {
            // Find user
            $user = User::where('email', $email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'User not found.']);
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                \Log::warning('OTP Login - Invalid password for: ' . $email);
                return back()->withErrors(['password' => 'Invalid password.']);
            }

            \Log::info('OTP Login - Password verified for: ' . $email);

            // Generate 6-digit OTP
            $otp = rand(100000, 999999);
            \Log::info('OTP generated: ' . $otp . ' for: ' . $email);

            // Delete any existing OTPs for this email
            DB::table('login_otps')
                ->where('email', $email)
                ->delete();

            // Store OTP in database (valid for 5 minutes)
            DB::table('login_otps')->insert([
                'email' => $email,
                'otp' => $otp,
                'user_id' => $user->id,
                'attempts' => 0,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(5),
            ]);

            \Log::info('OTP stored in DB for: ' . $email);

            // Send OTP via email
            Mail::send('emails.otp-login', [
                'user' => $user,
                'otp' => $otp,
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Your Login OTP — Land Record Digitalization');
            });

            \Log::info('OTP email sent to: ' . $email);

            // Redirect to OTP verification page
            return redirect()->route('otp.verify')
                ->with('success', 'OTP has been sent to your email.')
                ->with('email', $email);

        } catch (\Exception $e) {
            \Log::error('OTP Login error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send OTP. Please try again.']);
        }
    }

    /**
     * Show OTP verification page
     */
    public function showOtpVerify(): View
    {
        return view('auth.otp-verify');
    }

    /**
     * Verify OTP and log user in
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.size' => 'OTP must be exactly 6 digits.',
        ]);

        $email = $request->email;
        $otp = $request->otp;

        \Log::info('OTP verification attempt for: ' . $email . ' with OTP: ' . $otp);

        try {
            // Check if OTP exists and is valid
            $otpRecord = DB::table('login_otps')
                ->where('email', $email)
                ->where('otp', $otp)
                ->first();

            if (!$otpRecord) {
                \Log::warning('OTP not found for: ' . $email);
                
                // Increment failed attempts
                DB::table('login_otps')
                    ->where('email', $email)
                    ->increment('attempts');

                return back()
                    ->with('email', $email)
                    ->withErrors(['otp' => 'Invalid OTP. Please try again.']);
            }

            // Check if OTP is expired
            if (Carbon::parse($otpRecord->expires_at)->isPast()) {
                \Log::warning('OTP expired for: ' . $email);
                
                // Delete expired OTP
                DB::table('login_otps')
                    ->where('email', $email)
                    ->delete();

                return back()
                    ->with('email', $email)
                    ->withErrors(['otp' => 'OTP has expired. Please request a new one.']);
            }

            // Check if too many failed attempts (max 3)
            if ($otpRecord->attempts >= 3) {
                \Log::warning('Too many OTP attempts for: ' . $email);
                
                DB::table('login_otps')
                    ->where('email', $email)
                    ->delete();

                return back()
                    ->with('email', $email)
                    ->withErrors(['otp' => 'Too many failed attempts. Please request a new OTP.']);
            }

            // OTP is valid! Find user and log in
            $user = User::where('email', $email)->first();

            if (!$user) {
                return back()->withErrors(['email' => 'User not found.']);
            }

            // Delete the used OTP
            DB::table('login_otps')
                ->where('email', $email)
                ->delete();

            // Log user in
            Auth::login($user, $request->boolean('remember'));

            // Regenerate session
            $request->session()->regenerate();

            \Log::info('User logged in via OTP: ' . $email);

            // Redirect to dashboard
            return redirect()->intended(route('dashboard', absolute: false))
                ->with('status', 'Logged in successfully!');

        } catch (\Exception $e) {
            \Log::error('OTP verification error: ' . $e->getMessage());
            return back()->withErrors(['otp' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Request new OTP (if current one expired or lost)
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $email = $request->email;

        try {
            $user = User::where('email', $email)->first();

            // Generate new OTP
            $otp = rand(100000, 999999);

            // Delete old OTP
            DB::table('login_otps')
                ->where('email', $email)
                ->delete();

            // Store new OTP
            DB::table('login_otps')->insert([
                'email' => $email,
                'otp' => $otp,
                'user_id' => $user->id,
                'attempts' => 0,
                'created_at' => Carbon::now(),
                'expires_at' => Carbon::now()->addMinutes(5),
            ]);

            // Send OTP email
            Mail::send('emails.otp-login', [
                'user' => $user,
                'otp' => $otp,
            ], function ($message) use ($email) {
                $message->to($email)
                    ->subject('Your Login OTP — Land Record Digitalization');
            });

            return back()
                ->with('email', $email)
                ->with('success', 'A new OTP has been sent to your email.');

        } catch (\Exception $e) {
            \Log::error('Resend OTP error: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to resend OTP.']);
        }
    }
}
