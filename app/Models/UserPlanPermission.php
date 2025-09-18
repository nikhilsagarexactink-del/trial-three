<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPlanPermission extends Model
{
    use HasFactory;

    public function module()
    {
        return $this->hasOne('App\Models\Module', 'id', 'module_id');
    }
}
