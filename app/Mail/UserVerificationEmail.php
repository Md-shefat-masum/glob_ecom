<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserVerificationEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $sendLinkInfo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->sendLinkInfo = $data;
        Log::info(  config('mail.mailers.smtp'));
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->subject('User Verification Email')
            ->view('backend.mail.userVerificationEmail')->with([
                'name' => 'User',
            ]);
    }
}
