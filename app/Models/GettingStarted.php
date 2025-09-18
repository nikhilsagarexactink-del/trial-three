<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GettingStarted extends Model
{
    use HasFactory;

    public $table = 'getting_started';

    /**
     * Image
     */
    public function media()
    {
        return $this->hasOne('App\Models\Media', 'id', 'media_id');
    }

    /**
     * category
     */
    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }

    /**
     *  User Getting started
     */
    public function userGettingStarted()
    {
        $userData = getUser();

        return $this->hasOne('App\Models\UserGettingStarted', 'getting_started_id', 'id')->where('user_id', $userData->id); // Add condition for user_id
    }
}
