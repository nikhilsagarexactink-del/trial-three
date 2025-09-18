<?php

namespace App\Listeners;

use App\Events\UserRegistered; // Import the UserRegistered event
use App\Models\RecurringBroadcast; // Import the RecurringBroadcast model
use App\Mail\BroadcastMail; // Import the BroadcastMail mailable
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail; // Import Mail facade
use Illuminate\Support\Facades\Log; // Import Log facade for logging

class SendRecurringBroadcast implements ShouldQueue // Implements ShouldQueue for queued processing
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered  $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        // Fetch active recurring broadcasts for this trigger
        $broadcasts = RecurringBroadcast::where('trigger_event', 'sign_up')->get();

        foreach ($broadcasts as $broadcast) {
            // Send email using Laravel Mail
            Mail::to($event->user->email)->send(new BroadcastMail($broadcast, $event->user));
        }
    }
}
