<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeRating extends Model
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
     * Recipe
     */
    public function recipe()
    {
        return $this->hasOne('App\Models\Recipe', 'id', 'recipe_id');
    }
}
