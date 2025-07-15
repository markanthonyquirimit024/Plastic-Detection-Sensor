<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;

class TwoFactorController extends Controller
{
    /**
     * Show the Two-Factor Authentication form.
     */
    public function showTwoFactorForm()
    {
        if (Auth::check() && Session::has('2fa_passed')) {
            return redirect()->route('dashboard');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Generate and send a 2FA code to the user's email.
     */
    public function sendTwoFactorCode()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User not authenticated.']);
        }

        $code = mt_rand(100000, 999999); // Generate a secure 6-digit code

        // Store 2FA code in session (expires after 5 minutes)
        session([
            '2fa_code' => $code, 
            '2fa_expires_at' => now()->addMinutes(5)
        ]);

        try {
            // Ensure real-time email sending
            Mail::to($user->email)->send(new TwoFactorCodeMail($code));
        } catch (\Exception $e) {
            return redirect()->route('2fa.index')->withErrors([
                'email' => 'Failed to send email. Please try again.'
            ]);
        }

        return redirect()->route('2fa.index')->with('message', 'A verification code has been sent to your email.');
    }

    /**
     * Resend the Two-Factor Authentication Code.
     */
    public function resendTwoFactorCode()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        $code = mt_rand(100000, 999999); // Generate new 6-digit code

        // Store new 2FA code in session
        session([
            '2fa_code' => $code,
            '2fa_expires_at' => now()->addMinutes(5)
        ]);

        try {
            Mail::to($user->email)->send(new TwoFactorCodeMail($code));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to send email.'], 500);
        }

        return response()->json(['message' => 'A new 2FA code has been sent to your email.']);
    }

    /**
     * Verify the Two-Factor Authentication Code.
     */
    public function verifyTwoFactorCode(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $storedCode = session('2fa_code');
        $expiresAt = session('2fa_expires_at');

        if (!$storedCode || now()->greaterThan($expiresAt)) {
            return back()->withErrors([
                'code' => 'The 2FA code has expired. Please request a new one.'
            ]);
        }

        if ($request->input('code') == $storedCode) {
            session()->forget(['2fa_code', '2fa_expires_at']); // Remove 2FA code after use
            session(['2fa_passed' => true]); // Mark 2FA as passed
            return redirect()->route('dashboard');
        }

        return back()->withErrors(['code' => 'Invalid authentication code.']);
    }

    /**
     * Display the Two-Factor Challenge Page.
     */
    public function challenge()
    {
        return view('auth.two-factor-challenge');
    }
}
