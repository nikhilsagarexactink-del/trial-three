<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutExerciseDifficulty extends Model
{
    use HasFactory;

    public function difficulty()
    {
        return $this->hasOne('App\Models\Difficulty', 'id', 'difficulty_id');
    }
}
