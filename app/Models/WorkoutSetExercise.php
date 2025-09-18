<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutSetExercise extends Model
{
    use HasFactory;

    public function exercise()
    {
        return $this->hasOne('App\Models\WorkoutExercise', 'id', 'workout_exercise_id');
        //return $this->belongsToMany(WorkoutExercise::class, WorkoutSetExercise::class);
    }
}
