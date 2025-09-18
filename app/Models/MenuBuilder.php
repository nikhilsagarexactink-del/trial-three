<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuBuilder extends Model
{
    use HasFactory;

     /**
     * media
     */
    public function media()
    {
        return $this->hasOne('App\Models\Media', 'id', 'media_id');
    }

    public function module()
    {
        return $this->hasOne('App\Models\Module', 'id', 'module_id');
    }

    public function childs()
    {
        return $this->hasMany('App\Models\MenuBuilder', 'parent_id', 'id');
    }

    public function modulePermission()
    {
        return $this->hasMany('App\Models\UserModulePermission', 'module_id', 'module_id');

    }
}
