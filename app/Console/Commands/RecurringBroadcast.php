<?php

namespace App\Console\Commands;
use App\Repositories\RecurringBroadcastRepository;

use Illuminate\Console\Command;

class RecurringBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected  $signature = 'command:RecurringBroadcast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Recurring broadcast Notification';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
        \Log::info('RecurringBroadcast command started!');
        RecurringBroadcastRepository::triggerRecurringBroadcast();
    } catch (\Exception $e) {
        \Log::error('Error triggering recurring broadcast: ' . $e->getMessage());
    }
    }
}
