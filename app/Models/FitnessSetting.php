<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitnessSetting extends Model
{
    use HasFactory;

    public function staticExercises()
    {
        return $this->hasOne('App\Models\FitnessProfileExercise', 'id', 'fitness_profile_exercise_id');
    }

    public function customExercises()
    {
        return $this->hasOne('App\Models\WorkoutExercise', 'id', 'workout_exercise_id');
    }
}
