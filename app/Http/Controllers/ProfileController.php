<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;


class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request)
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));      
    }

public function update_profile(Request $request)
{
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name'  => 'required|string|max:255',
        'email'      => 'required|email|unique:users,email,' . $user->id,
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $user->first_name = $request->input('first_name');
    $user->last_name  = $request->input('last_name');
    $user->email      = $request->input('email');

    $user->save();

    return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
}

public function change_password(Request $request)
{
    $request->validate([
        'current_password' => ['required'],
        'password' => [
            'required',
            'string',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols(),
        ],
    ]);

    $user = Auth::user();

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Your current password is incorrect.']);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    return redirect()
        ->route('profile.edit')
        ->with('success', 'Password updated successfully!');
}


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
