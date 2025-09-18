<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpsellPlan extends Model
{
    use HasFactory;
    public function plan()
    {
        return $this->hasOne('App\Models\Plan', 'id', 'plan_id')->where('status','!=','deleted');
    }
}
