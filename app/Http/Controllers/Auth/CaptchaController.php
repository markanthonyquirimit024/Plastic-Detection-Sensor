<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CaptchaController extends Controller
{
    public function showCaptcha()
    {
        session()->put('captcha_text', strtoupper(substr(md5(rand()), 0, 6))); // Generate random 6-letter captcha
        return view('auth.captcha');
    }

    public function verifyCaptcha(Request $request)
    {
        if ($request->captcha === session('captcha_text')) {
            session()->put('captcha_verified', true);
            session(['login_attempts' => 0]); // Reset failed login attempts
            return redirect()->route('login')->with('message', 'Captcha verified! You can now log in.');
        }

        return back()->with('error', 'Incorrect captcha. Try again.');
    }
}
