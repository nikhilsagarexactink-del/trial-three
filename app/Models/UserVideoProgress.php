<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVideoProgress extends Model
{
    use HasFactory;
    public function video()
    {
        return $this->belongsTo('App\Models\TrainingVideo', 'video_id', 'id');
    }
}
