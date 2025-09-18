<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    public function rolePermission()
    {
        return $this->hasOne('App\Models\UserModulePermission', 'module_id', 'id');
    }

    public function planPermission()
    {
        return $this->hasOne('App\Models\UserPlanPermission', 'module_id', 'id');
    }

    public function media()
    {
        return $this->hasOne('App\Models\Media', 'id', 'media_id');
    }

    /**
     * Module Permission
     */
    // public function actions()
    // {
    //     return $this->hasMany('App\Models\ModuleAction', 'module_id', 'id');
    // }


    // public function toolTip()
    // {
    //     return $this->hasOne('App\Models\PermissionToolTip', 'child_module_id', 'id')->where('status', 'active');
    // }
    public function toolTip()
    {
        return $this->hasOne('App\Models\PermissionToolTip', 'module_id', 'id')->where('status', 'active');
    }

    public function master()
    {
        return $this->hasOne('App\Models\MasterModule', 'id', 'master_module_id');
    }

    public function childs()
    {
        return $this->hasMany(Module::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    public function userPermissions()
    {
        return $this->hasMany('App\Models\UserModulePermission', 'module_id', 'id');
    }

    public function menuBuilders()
    {
        return $this->hasMany('App\Models\MenuBuilder', 'module_id', 'id');
    }

    public function userPlanPermissions()
    {
        return $this->hasMany('App\Models\UserPlanPermission', 'module_id', 'id');
    }

}
