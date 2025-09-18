<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeal extends Model
{
    use HasFactory;

    public function meal()
    {
        return $this->hasOne(MealMaster::class,'id','meal_master_id')->where('status','!=','deleted');
    }

}
