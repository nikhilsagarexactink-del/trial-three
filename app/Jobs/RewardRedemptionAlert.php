<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mail;
use App\Mail\UserPurchaseConfirmation;
use App\Mail\AdminPurchaseNotification;

class RewardRedemptionAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $orderDetail;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $orderDetail)
    {
        $this->orderDetail = $orderDetail;   
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Send email to user
        Mail::to($this->orderDetail['email'])->send(new UserPurchaseConfirmation($this->orderDetail));

        // Send email to admin
        Mail::to(env('ADMIN_EMAIL'))->send(new AdminPurchaseNotification($this->orderDetail));
    }
}
