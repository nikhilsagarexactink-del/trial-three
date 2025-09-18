<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMenuLink extends Model
{
    use HasFactory;

    /**
     * media
     */
    public function menu()
    {
        return $this->hasOne('App\Models\Menu', 'id', 'menu_id');
    }
}
