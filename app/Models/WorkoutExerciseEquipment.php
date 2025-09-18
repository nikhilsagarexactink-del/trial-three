<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutExerciseEquipment extends Model
{
    use HasFactory;

    protected $table = 'workout_exercise_equipments';

    public function equipment()
    {
        return $this->hasOne('App\Models\Equipment', 'id', 'equipment_id');
    }
}
