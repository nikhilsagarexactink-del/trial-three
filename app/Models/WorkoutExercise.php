<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutExercise extends Model
{
    use HasFactory;

    public function media()
    {
        return $this->hasOne('App\Models\Media', 'id', 'media_id');
    }

    public function difficulties()
    {
        // return $this->hasMany('App\Models\WorkoutExerciseDifficulty', 'workout_exercise_id', 'id');
        return $this->belongsToMany(Difficulty::class, WorkoutExerciseDifficulty::class);
    }

    public function ageRanges()
    {
        return $this->belongsToMany(AgeRange::class, WorkoutExerciseAgeRange::class);
        // return $this->hasMany('App\Models\WorkoutExerciseAgeRange', 'workout_exercise_id', 'id');
    }

    public function sports()
    {
        return $this->belongsToMany(Sport::class, WorkoutExerciseSport::class);
        // return $this->hasMany('App\Models\WorkoutExerciseSport', 'workout_exercise_id', 'id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, WorkoutExerciseCategory::class);
        // return $this->hasMany('App\Models\WorkoutExerciseCategory', 'workout_exercise_id', 'id');
    }

    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, WorkoutExerciseEquipment::class);
        // return $this->hasMany('App\Models\WorkoutExerciseEquipment', 'workout_exercise_id', 'id');
    }

    public function athletes()
    {
        return $this->belongsToMany(User::class, WorkoutExerciseUsersAssignment::class);
        //return $this->hasMany('App\Models\WorkoutExerciseEquipment', 'workout_exercise_id', 'id');
    }

    public function assignments()
    {
        return $this->hasMany(WorkoutExerciseUsersAssignment::class, 'workout_exercise_id', 'id');
    }

    public function workoutGroups()
    {
        return $this->hasMany(WorkoutExerciseGroup::class, 'workout_exercise_id', 'id');
    }


    public function sets()
    {
        return $this->hasMany('App\Models\WorkoutSet', 'workout_exercise_id', 'id');
    }
}
