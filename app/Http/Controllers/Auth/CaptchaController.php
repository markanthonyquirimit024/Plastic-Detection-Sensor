<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CaptchaController extends Controller
{
    public function showCaptcha()
    {
        $captcha = strtoupper(substr(hash_hmac('md5', uniqid(mt_rand(), true), config('app.key')), 0, 6));
        session()->put('captcha_text', $captcha); // Store generated captcha in session

        return view('auth.captcha');
    }

    public function verifyCaptcha(Request $request)
    {
        $inputCaptcha = strtoupper(trim($request->captcha));
        $storedCaptcha = session()->get('captcha_text');

        if ($inputCaptcha === $storedCaptcha) {
            // ✅ Captcha is correct, allow login
            session()->forget('captcha_text'); // Remove stored captcha
            session()->put('captcha_verified', true);
            session(['login_attempts' => 0]); // Reset failed login attempts

            return redirect()->route('login')->with('message', 'Captcha verified! Please log in.');
        }

        // ❌ If incorrect, increase failed attempt count
        session()->increment('login_attempts');

        return redirect()->back()->with('error', 'Incorrect captcha. Try again.');
    }
    public function refreshCaptcha()
{
    $newCaptcha = strtoupper(substr(hash_hmac('md5', uniqid(mt_rand(), true), config('app.key')), 0, 6));
    session()->put('captcha_text', $newCaptcha);

    return response()->json(['captcha' => $newCaptcha]); // ✅ Return JSON response
}

}
