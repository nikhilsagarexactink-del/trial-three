<?php

namespace App\Console\Commands;
use App\Repositories\FitnessProfileRepository;
use App\Repositories\HealthTrackerRepository;
use App\Repositories\WorkoutBuilderRepository;

use Illuminate\Console\Command;

class ReminderNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ReminderNotification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Fitness Workouts and Health Management Schdule Notification.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try{
            \Log::info('Reminder Notification command started!');
            FitnessProfileRepository::workoutReminderNotificationCron();
            HealthTrackerRepository::healthtReminderNotificationCron();
            WorkoutBuilderRepository::sendCustomWorkoutReminder();
        } catch (\Exception $e) {
            \Log::error('Error triggering reminder Notification: ' . $e->getMessage());
        }
    }
}
