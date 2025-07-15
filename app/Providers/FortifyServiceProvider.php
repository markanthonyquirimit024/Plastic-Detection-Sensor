<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse;
use Illuminate\Support\Facades\Hash;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // ✅ Custom authentication logic
        Fortify::authenticateUsing(function (Request $request) {
            $user = \App\Models\User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // ✅ Store user ID in session for 2FA, but DO NOT redirect here
                if ($user->two_factor_secret) {
                    session(['2fa:user:id' => $user->id]);
                }
                return $user;
            }

            return null;
        });

        // ✅ Rate limiting for login attempts
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        // ✅ Rate limiting for 2FA attempts
        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // ✅ Customize login response to enforce 2FA
        $this->app->instance(LoginResponse::class, new class implements LoginResponse {
            public function toResponse($request)
            {
                if (session()->has('2fa:user:id')) {
                    return redirect()->route('two-factor.challenge'); // Redirect only after successful authentication
                }
                return redirect()->intended(route('dashboard')); // Redirect to dashboard if no 2FA
            }
        });

        // ✅ Customize 2FA login response
        $this->app->instance(TwoFactorLoginResponse::class, new class implements TwoFactorLoginResponse {
            public function toResponse($request)
            {
                session()->forget('2fa:user:id'); // Remove session after 2FA verification
                return redirect()->intended(route('dashboard'));
            }
        });
    }
}
