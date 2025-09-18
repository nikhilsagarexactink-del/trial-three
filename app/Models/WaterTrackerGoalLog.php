<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaterTrackerGoalLog extends Model
{
    use HasFactory;

    public function userGoal()
    {
        return $this->hasOne('App\Models\WaterTrackerGoal', 'id', 'water_tracker_goal_id');
    }

    /**
     * User
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
