<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultProfilePicture extends Model
{
    use HasFactory;

    /**
     * media
     */
    public function media()
    {
        return $this->hasOne('App\Models\Media', 'id', 'media_id');
    }
}
