<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Upsell extends Model
{
    use HasFactory;
    
    public function plans()
    {
        return $this->hasMany('App\Models\UpsellPlan', 'upsell_id', 'id');
    }
}
