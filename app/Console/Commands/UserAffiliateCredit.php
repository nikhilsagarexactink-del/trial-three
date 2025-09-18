<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\AffiliateRepository;

class UserAffiliateCredit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'affiliate:credit-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Credits affiliates with commissions on the 1st of the month';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        AffiliateRepository::affiliateCreditCron();
    }
}
