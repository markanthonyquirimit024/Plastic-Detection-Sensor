<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use PragmaRX\Google2FA\Google2FA;
use Illuminate\Support\Facades\Crypt;
use App\Models\Analyst;
use App\Models\Customer;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    const ADM = 'Admin';
    const SVP = 'Analyst';                                                                                
    const CST = 'Customer';

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'email_verified_at',
        'utype'
    ];

    public function utype()
    {
        return $this->getAttribute('utype');
    }


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'two_factor_confirmed_at' => 'datetime',
    ];

    /**
     * Check if the user has 2FA enabled.
     *
     * @return bool
     */
    public function hasTwoFactorEnabled()
    {
        return !empty($this->two_factor_secret);
    }

    /**
     * Validate the user's two-factor authentication code.
     *
     * @param string $code
     * @return bool
     */
    public function validateTwoFactorCode($code)
    {
        if (!$this->hasTwoFactorEnabled()) {
            return false;
        }

        try {
            $google2fa = new Google2FA();
            return $google2fa->verifyKey(Crypt::decryptString($this->two_factor_secret), $code);
        } catch (\Exception $e) {
            return false;
        }
    }
}
