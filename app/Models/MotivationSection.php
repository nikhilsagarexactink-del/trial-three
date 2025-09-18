<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MotivationSection extends Model
{
    use HasFactory;
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
}
