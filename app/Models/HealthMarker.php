<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthMarker extends Model
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
     * Images
     */
    public function images()
    {
        return $this->hasMany('App\Models\HealthMarkerImage', 'health_marker_id', 'id')->orderBy('id', 'desc');
    }
}
