<?php

namespace App\Console\Commands;
use App\Repositories\UserRepository;

use Illuminate\Console\Command;

class UpdateSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdateSubscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Subscriptions when user select downgrade plan.';

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
        UserRepository::updateSubscriptionCron();
        UserRepository::subscriptionGracePeriodEndCron();
    }
}
