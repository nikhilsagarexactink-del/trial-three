<?php

namespace App\Console\Commands;

use App\Repositories\FitnessProfileRepository;
use App\Repositories\CalendarRepository;
use Illuminate\Console\Command;

class AddFitnessProfileLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:AddFitnessProfileLog';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add fitness profile log';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            FitnessProfileRepository::saveLogCron();
            $this->info('Fitness profile log saved successfully.');
    
            // Send user calendar event reminders
            CalendarRepository::sendUserCalendarEventReminder();
            $this->info('User calendar event reminders sent successfully.');
        } catch (\Exception $e) {
            $this->error('An error occurred: ' . $e->getMessage());
            \Log::error('Command execution failed', ['exception' => $e]);
        }
    }
}
