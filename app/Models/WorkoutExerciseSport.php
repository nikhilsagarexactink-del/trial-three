<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutExerciseSport extends Model
{
    use HasFactory;

    public function sport()
    {
        return $this->hasOne('App\Models\Sport', 'id', 'sport_id');
    }
}
