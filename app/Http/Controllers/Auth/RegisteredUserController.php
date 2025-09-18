<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form.
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle a new user registration.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => [
            'required',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
            ],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'email_verified_at' => now(),
            'utype' => 'CUST',
        ]);

        return redirect()->route('login')->with('success', 'Registration successful!');
    }
}
