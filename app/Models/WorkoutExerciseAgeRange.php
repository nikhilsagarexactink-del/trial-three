<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutExerciseAgeRange extends Model
{
    use HasFactory;

    public function ageRange()
    {
        return $this->hasOne('App\Models\AgeRange', 'id', 'age_range_id');
    }
}
