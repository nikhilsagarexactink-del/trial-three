<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BillingNotificationEmail extends Mailable
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
    public function build()
    {
        return $this->subject('Turbo Charged Athletic - Billing Alert')->view('layouts.email.billing-alert');
    }
}
