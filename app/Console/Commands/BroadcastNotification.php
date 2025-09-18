<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\BroadcastRepository;

class BroadcastNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:BroadcastNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Broadcast Notification';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        BroadcastRepository::broadcastMessageCron();
    }
}
