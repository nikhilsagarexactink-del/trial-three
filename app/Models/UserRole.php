<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    use HasFactory;

    public function user_type(){
        return $this->belongsTo(FitnessChallengeUserRole::class, 'id', 'user_role_id');
    }
}
