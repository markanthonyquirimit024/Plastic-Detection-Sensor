<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Handle the email verification process and show a message.
     *
     * @param  \Illuminate\Foundation\Auth\EmailVerificationRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        // Check if the email is already verified
        if ($request->user()->hasVerifiedEmail()) {
            // If the email is already verified, redirect to login with a message
            return redirect()->route('login')->with('message', 'Your email has already been verified. Please log in.');
        }

        // Mark the email as verified
        $request->user()->markEmailAsVerified();

        // Fire the Verified event
        event(new Verified($request->user()));

        // Redirect to login with a success message
        return redirect()->route('login')->with('message', 'Your email has been successfully verified! Please log in.');
    }
}
