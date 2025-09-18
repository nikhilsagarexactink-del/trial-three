<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWorkoutGoal extends Model
{
    use HasFactory;

    public function workoutGoal()
    {
        return $this->hasOne('App\Models\WorkoutGoal', 'id', 'workout_goal_id');
    }
}
