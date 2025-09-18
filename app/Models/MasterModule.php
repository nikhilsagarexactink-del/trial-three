<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterModule extends Model
{
    use HasFactory;

    protected $appends = ['icon_url'];

    public function modules()
    {
        return $this->hasMany('App\Models\Module', 'master_module_id', 'id');
    }

    public function childs()
    {
        return $this->hasMany('App\Models\Module', 'parent_id', 'id')->where('status', '!=', 'deleted');
    }

    public function menus()
    {
        return $this->hasMany('App\Models\Menu', 'id', 'master_module_id');
    }

    public function getIconUrlAttribute()
    {
        return url('assets/images/'.$this->attributes['icon']);
    }

        public function toolTip()
    {
        return $this->hasOne('App\Models\PermissionToolTip', 'parent_module_id', 'id')->where('status', 'active');
    }

}