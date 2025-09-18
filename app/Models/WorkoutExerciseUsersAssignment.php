<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutExerciseUsersAssignment extends Model
{
    use HasFactory;

    public function athlete()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
