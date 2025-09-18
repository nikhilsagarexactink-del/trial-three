<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutSet extends Model
{
    use HasFactory;

    public function workoutSetExercises()
    {
        return $this->hasMany('App\Models\WorkoutSetExercise', 'workout_set_id', 'id');
    }

    public function exercise()
    {
       return $this->hasOne('App\Models\WorkoutExercise','id', 'workout_exercise_id');
        //return $this->belongsToMany(WorkoutExercise::class, WorkoutSetExercise::class);
    }

    public function setExercises()
    {
       return $this->hasMany('App\Models\WorkoutSetExercise','workout_set_id', 'id');
        //return $this->belongsToMany(WorkoutExercise::class, WorkoutSetExercise::class);
    }
}
