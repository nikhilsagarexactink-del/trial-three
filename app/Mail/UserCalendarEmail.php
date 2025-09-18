<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCalendarEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Data.
     */
    public $emailData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Array $emailData)
    {
        $this->emailData = $emailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Turbo Charged Athletic - Event Alert')->view('layouts.email.user-event-reminder');
    }
}
