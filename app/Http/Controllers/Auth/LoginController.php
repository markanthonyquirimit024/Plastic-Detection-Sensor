<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate login form inputs
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
        ]);

        // ✅ Prevent login if CAPTCHA is required
        if (session('captcha_required', false)) {
            return redirect()->route('captcha')->withErrors(['captcha' => 'Please complete the CAPTCHA verification.']);
        }

        // Attempt to authenticate the user
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // ✅ Clear CAPTCHA-related session data after successful login
            session()->forget(['captcha_failed_attempts', 'captcha_required']);

            // ✅ Check if 2FA is enabled and redirect to the 2FA page
            if (!empty($user->two_factor_secret)) {
                Session::put('2fa:user:id', $user->id);
                Auth::logout();
                return Redirect::route('2fa.index');  // Redirecting to the 2FA verification page
            }
            
            $this->createUserSession($request, $user);

            // ✅ Redirect to the dashboard if everything is fine
            return Redirect::route('dashboard');
        }

        // ❌ Failed login attempt - increment CAPTCHA failed attempts counter
        session()->increment('captcha_failed_attempts');

        // ✅ If failed attempts reach 3, force CAPTCHA before login
        if (session('captcha_failed_attempts') >= 3) {
            session(['captcha_required' => true]);
            return redirect()->route('captcha')->withErrors(['captcha' => 'Too many failed attempts. Please verify CAPTCHA.']);
        }

        // Invalid credentials - return with error
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        $userSessionId = session('user_session_id');

        if ($userSessionId) {
        DB::table('user_sessions')
            ->where('id', $userSessionId)
            ->update([
                'logout_time' => now()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function verifyCaptcha(Request $request)
    {
        // Validate CAPTCHA input
        $request->validate([
            'captcha' => 'required|captcha',
        ]);

        // ✅ Clear CAPTCHA session and reset failed login attempts
        session()->forget(['captcha_failed_attempts', 'captcha_required']);

        // ✅ Redirect to login page after successful CAPTCHA verification
        return redirect()->route('login')->with('success', 'CAPTCHA verified! Please log in.');
    }

    protected function authenticated(Request $request, $user)
    {
        // ✅ Ensure the user’s email is verified before allowing login
        if (!$user->hasVerifiedEmail()) {
            Auth::logout();
            return redirect()->route('verification.notice')->with('message', 'Please verify your email first.');
        }

        // ✅ Check if 2FA has been completed
        if (!session()->has('2fa_passed')) {
            return redirect()->route('2fa.index');
        }

        // Redirect to the dashboard if everything is okay
        return redirect()->route('dashboard');
    }

    protected function createUserSession(Request $request, $user)
    {
        $sessionId = DB::table('user_sessions')->insertGetId([
            'user_id' => $user->id,
            'login_time' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent(), 0, 500),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session(['user_session_id' => $sessionId]);

        $user->last_login = now();
        $user->save();
    }
}

