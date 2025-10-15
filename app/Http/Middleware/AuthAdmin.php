<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthAdmin
{
    public function handle($request, Closure $next)
    {
        if (Auth::check() && Auth::user()->utype === 'Admin') {
            return $next($request);
        }
        abort(404,'Page not Found');
    }
}
