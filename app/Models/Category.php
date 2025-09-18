<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\GettingStarted;

class Category extends Model
{
    use HasFactory;

    public function gettingStartedVideos()
    {
        return $this->hasMany(GettingStarted::class)->where('status','active')->orderBy('order', 'ASC');
    }
}
