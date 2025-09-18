<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StepCounterGoalLog extends Model
{
    use HasFactory;
    public function userGoal()
    {
        return $this->hasOne('App\Models\StepCounterGoal', 'id', 'step_counter_goal_id');
    }

    /**
     * User
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
