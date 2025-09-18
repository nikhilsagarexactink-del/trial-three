<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkoutExerciseGroup extends Model
{
    use HasFactory;

    public function groupUsers()
    {
        return $this->hasMany('App\Models\GroupUser', 'group_id', 'group_id');
    }
}
