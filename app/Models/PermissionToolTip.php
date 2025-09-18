<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionToolTip extends Model
{
    use HasFactory;
    protected $fillable = [
        'tool_tip_text' ,
        'is_parent_module',
        'module_id',
        'show_as_parent',
        'type',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];
    // protected $fillable = [
    //     'tool_tip_text' ,
    //     'is_parent_module',
    //     'parent_module_id',
    //     'child_module_id',
    //     'status',
    //     'created_by',
    //     'updated_by',
    //     'created_at',
    //     'updated_at'
    // ];

        /**
     * parent
     */
    // public function parentModule()
    // {
    //     return $this->hasOne('App\Models\MasterModule', 'id', 'parent_module_id');
    // }

    //         /**
    //  * child
    //  */
    // public function childModule()
    // {
    //     return $this->hasOne('App\Models\Module', 'id', 'child_module_id');
    // }
    public function module()
    {
        return $this->hasOne('App\Models\Module', 'id', 'module_id');
    }
}