<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCodeMail extends Mailable
{
    use SerializesModels; // ❌ Removed Queueable to send instantly

    public $code;

    /**
     * Create a new message instance.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Your 2FA Code')
                    ->view('emails.two-factor-code')
                    ->with(['code' => $this->code]);
    }
}
