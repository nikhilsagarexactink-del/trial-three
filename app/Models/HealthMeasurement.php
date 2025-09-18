<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HealthMeasurement extends Model
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
        return $this->hasMany('App\Models\HealthMeasurementImage', 'health_measurement_id', 'id')->orderBy('id', 'desc');
    }
}
