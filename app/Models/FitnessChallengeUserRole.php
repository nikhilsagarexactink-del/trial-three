<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitnessChallengeUserRole extends Model
{
    use HasFactory;

    /**
     * role
     */
    public function role()
    {
        return $this->hasOne('App\Models\UserRole', 'id', 'user_role_id');
    }
}
