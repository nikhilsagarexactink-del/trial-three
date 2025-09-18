<?php

namespace App\Mail;

use App\Models\RecurringBroadcast; // Import RecurringBroadcast
use App\Models\User; // Import User
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BroadcastMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $broadcast;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param  RecurringBroadcast  $broadcast
     * @param  User  $user
     * @return void
     */
    public function __construct(RecurringBroadcast $broadcast, User $user)
    {
        $this->broadcast = $broadcast;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->broadcast->title)
                    ->view('layouts.email.recurring-broadcast')
                    ->with([
                        'content' => $this->broadcast->message,
                        'user' => $this->user,
                    ]);
    }
}
