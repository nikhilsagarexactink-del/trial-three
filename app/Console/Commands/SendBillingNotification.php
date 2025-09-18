<?php

namespace App\Console\Commands;
use App\Repositories\BillingRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Console\Command;

class SendBillingNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:SendBillingNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Billing Notification';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        BillingRepository::userBillingAlertCron();
        PaymentRepository::paymentFailedNotificationCron();
    }
}
