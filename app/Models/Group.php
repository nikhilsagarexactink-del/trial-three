<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

        /**
     * User
     */
    public function groupUsers()
    {
        return $this->hasMany('App\Models\GroupUser', 'group_id', 'id');
    }

    public function media()
    {
        return $this->hasOne('App\Models\Media', 'id', 'media_id');
    }
}
