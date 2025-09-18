<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use HasFactory;

    /**
     * From User
     */
    public function fromUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'from_user_id');
    }

    /**
     * To User
     */
    public function toUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'to_user_id');
    }

    /**
     * To User
     */
    public function message()
    {
        return $this->hasOne('App\Models\Message', 'thread_id', 'id')->latest();
    }

    /**
     * To User
     */
    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'category_id');
    }
}
