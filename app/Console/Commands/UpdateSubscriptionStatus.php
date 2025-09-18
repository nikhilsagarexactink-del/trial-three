<?php

namespace App\Console\Commands;
use App\Repositories\UserRepository;

use Illuminate\Console\Command;

class UpdateSubscriptionStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:UpdateSubscriptionStatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync payment gateway and db status.';

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
        UserRepository::updateSubscriptionStatusCron();
    }
}
