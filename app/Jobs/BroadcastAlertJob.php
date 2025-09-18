<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\TwilioService;

class BroadcastAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    // The method that handles the job logic
    public function handle(TwilioService $twilioService)
    {
        $messageBody = $this->user['message'];
        $userName = $this->user['name'];
        $phoneNumber = $this->user['cell_phone_number'];

        // Strip HTML tags from the message body
        $messageBodyPlainText = strip_tags($messageBody);

        // Create the message template
        $message = "Hi " . $userName . ",\n\n" 
             . $messageBodyPlainText . "\n\n"
             . "Warm Regards,\n"
             . "Turbo Charged Athletics Team";

        // Send the SMS using the SmsService
        if(!empty($phoneNumber)){
            $twilioService->sendSms($phoneNumber, $message);
        }
    }
}
