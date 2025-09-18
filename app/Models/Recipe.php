<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    /**
     * Categories
     */
    public function categories()
    {
        return $this->hasMany('App\Models\RecipeCategory', 'recipe_id');
    }
    /**
     * Images
     */
    public function images()
    {
        return $this->hasMany('App\Models\RecipeImage', 'recipe_id');
    }
     /**
     * Image
     */
    public function image()
    {
        return $this->hasOne('App\Models\RecipeImage', 'recipe_id');
    }

    /**
     * Rating
     */
    public function rating()
    {
        return $this->hasOne('App\Models\RecipeRating', 'recipe_id');
    }
    /**
     * Ratings
     */
    public function ratings()
    {
        return $this->hasMany('App\Models\RecipeRating', 'recipe_id');
    }
    /**
     * Favourite
     */
    public function favourite()
    {
        return $this->hasOne('App\Models\RecipeFavourite', 'recipe_id');
    }
}
