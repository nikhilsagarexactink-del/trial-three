<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        '\App\Console\Commands\AddFitnessProfileLog',
        '\App\Console\Commands\UpdateSubscriptions',
        // '\App\Console\Commands\UpdateSubscriptionStatus',
        '\App\Console\Commands\SendBillingNotification',
        '\App\Console\Commands\BroadcastNotification',
        '\App\Console\Commands\RecurringBroadcast',
        '\App\Console\Commands\ReminderNotification',

    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('command:AddFitnessProfileLog')->hourly();
        $schedule->command('command:UpdateSubscriptions')->daily();
        // $schedule->command('command:UpdateSubscriptionStatus')->everyFourHours();
        $schedule->command('command:SendBillingNotification')->everyMinute();
        $schedule->command('command:BroadcastNotification')->everyMinute();
        $schedule->command('command:RecurringBroadcast')->everyMinute();
        $schedule->command('command:ReminderNotification')->everyMinute();
        $schedule->command('command:UserAffiliateCredit')->monthly();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
