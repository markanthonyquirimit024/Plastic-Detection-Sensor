<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TwoFactorMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // ✅ Allow registration to bypass 2FA
        if ($request->routeIs('register') || $request->routeIs('register.post')) {
            return $next($request);
        }

        // ✅ Apply 2FA only after login
        if (Auth::check() && !Session::has('2fa_passed') && !$request->routeIs('auth.two-factor.challenge')) {
            return redirect()->route('auth.two-factor.challenge');
        }

        return $next($request);
    }
}
