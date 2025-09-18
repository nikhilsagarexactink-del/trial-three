<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitnessChallenge extends Model
{
    use HasFactory;

    /**
     * workout
     */
    public function workout()
    {
        return $this->hasOne('App\Models\WorkoutExercise', 'id', 'workout_id');
    }

    public function user_roles()
    {
        return $this->hasMany('App\Models\FitnessChallengeUserRole', 'challenge_id', 'id');
    }

    public function userType()
    {
        return $this->belongsToMany(UserRole::class, 'fitness_challenge_user_roles', 'challenge_id', 'user_role_id');
    }

    public function signUps()
    {
        return $this->hasMany('App\Models\FitnessChallengeSignup', 'challenge_id', 'id');
    }

    public function plans()
    {
        return $this->belongsToMany(Plan::class, 'fitness_challenge_plans', 'challenge_id', 'plan_id');
    }

}
