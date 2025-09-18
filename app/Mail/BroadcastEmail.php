<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Swift_Message;
use Symfony\Component\Mime\Email;

class BroadcastEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Data.
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    

    // public function build()
    // {
    //     return $this->subject('Turbo Charged Athletics - Notification')
    //                 ->view('layouts.email.broadcast')
    //                 ->with(['data' => $this->data])
    //                 ->withSymfonyMessage(function (Email $message) {
    //                     // Add custom SES headers for aws matric
    //                     // $message->getHeaders()->addTextHeader('X-SES-CONFIGURATION-SET', 'turbo-broadcast-report-tracking');
    //                     // $message->getHeaders()->addTextHeader('X-SES-MESSAGE-TAGS', 'BroadcastID=' . $this->data['broadcast_id']);
    //                     // Add custom SES headers for twilio SendGrid
    //                     $message->getHeaders()->addTextHeader('X-Custom-Arg', 'broadcastId=' . $this->data['broadcast_id']);
    //                 });
    // }
    public function build() {
        return $this->subject('Turbo Charged Athletics - Notification')
                    ->view('layouts.email.broadcast')
                    ->with(['data' => $this->data])
                    ->withSymfonyMessage(function (Email $message) {
                        $message->getHeaders()->addTextHeader('X-PM-Tag', 'broadcast_' . $this->data['broadcast_id']);
                    });
    }    
}
