<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingVideoRating extends Model
{
    use HasFactory;

    /**
     * User
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    /**
     * TrainingVideo
     */
    public function trainingVideo()
    {
        return $this->hasOne('App\Models\TrainingVideo', 'id', 'training_video_id');
    }
}
