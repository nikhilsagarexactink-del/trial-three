<?php

namespace App\Observers;

use App\Models\FitnessProfileExercise;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $user)
    {
        // $currentDateTime = getTodayDate('Y-m-d H:i:s');
        // $days = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
        // $exercises = ['STRETCHING', 'STRENGTH', 'WEIGHT_LIFTING', 'AEROBIC_EXERCISES', 'SPORT_PRACTICE', 'DAY_OFF'];
        // foreach ($days as $dayKey => $day) {
        //     $exerciseData = [];
        //     foreach ($exercises as $exKey => $exercise) {
        //         $exerciseData[$exKey]['day'] = $day;
        //         $exerciseData[$exKey]['title'] = $exercise;
        //         $exerciseData[$exKey]['field_type'] = 'checkbox';
        //         $exerciseData[$exKey]['durations'] = $exercise == 'DAY_OFF' ? '' : '1,5,15,20,25,45,60,75,90,120,150';
        //         $exerciseData[$exKey]['is_custom'] = 0;
        //         $exerciseData[$exKey]['created_by'] = $user->id;
        //         $exerciseData[$exKey]['updated_by'] = $user->id;
        //         $exerciseData[$exKey]['created_at'] = $currentDateTime;
        //         $exerciseData[$exKey]['updated_at'] = $currentDateTime;
        //     }
        //     FitnessProfileExercise::insert($exerciseData);
        // }
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }
}
