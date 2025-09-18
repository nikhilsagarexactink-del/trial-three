<?php
namespace App\Services;

use App\Models\FitnessChallengeLog;
use App\Models\FitnessChallengeSignup;
use App\Models\FitnessChallenge;

class ChallengeLogger
{
    public static function log($type, $moduleLogs): bool
    {
        $userData = getUser();
        $currentDate = getTodayDate('Y-m-d');
        $challenge = FitnessChallenge::where('type', $type)
            ->where('go_live_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate);
        if($type == 'workouts') {
            $challenge->where('workout_id', $moduleLogs['workout_exercise_id']);
        }
        $challenge = $challenge->first();

        if (!$challenge) {
            return false;
        }

        $signup = FitnessChallengeSignup::where('challenge_id', $challenge->id)
            ->where('user_id', $userData->id)
            ->first();

        if (!$signup) {
            return false;
        }

        $existing = FitnessChallengeLog::where('challenge_id', $challenge->id)
            ->where('user_id', $userData->id)
            ->where('date', $currentDate)
            ->first();

        if (!$existing) {
            try {
                $logs = new FitnessChallengeLog();
                $logs->user_id = $userData->id;
                $logs->challenge_id = $challenge->id;
                $logs->date = $currentDate;
                $logs->water_value = ($type == 'water-intake') ?  $moduleLogs['water_value'] : null; // Water Intake
                $logs->workout_complete_time = ($type == 'workouts') ?  $moduleLogs['completed_time'] : null; // Workouts
                $logs->sleep_duration = ($type == 'sleep-tracker') ?  $moduleLogs['sleep_duration'] : null; // Sleep Tracker
                $logs->step_value = ($type == 'step-counter') ?  $moduleLogs['step_value'] : null; // Step Counter
                // Food Tracker
                $logs->calories = ($type == 'food-tracker') ?  $moduleLogs['calories'] : null;
                $logs->proteins = ($type == 'food-tracker') ?  $moduleLogs['proteins'] : null;
                $logs->carbohydrates = ($type == 'food-tracker') ?  $moduleLogs['carbohydrates'] : null;
                $logs->created_by = $userData->id;
                $logs->updated_by = $userData->id;
                $logs->save();
                return true;
            } catch (\Exception $e) {
                // optionally log or handle duplicate insert
                return false;
            }
        }

        return true; // already exists, so consider it successful
    }
}
