<?php

namespace App\Mail;

use App\Models\EmailVerification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupStarted extends Mailable
{
    use Queueable, SerializesModels;

    public $email_verification;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EmailVerification $email_verification)
    {
        $this->email_verification = $email_verification;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.code');
    }
}
