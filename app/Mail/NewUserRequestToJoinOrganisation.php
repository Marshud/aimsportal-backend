<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserRequestToJoinOrganisation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The new userinstance.
     *
     * @var \App\Models\User
     */
    public $newUser;
    
    public $frontEndUrl;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $newUser)
    {
        $this->newUser = $newUser;
        $this->frontEndUrl = env('FRONTEND_URL', 'http://aims.mofp.gov.ss')."/organisation/".$newUser->currentOrganisation->id;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'New User Request To Join Organisation',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.users.join-organisation',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
