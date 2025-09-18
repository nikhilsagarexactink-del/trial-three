<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardManagement extends Model
{
    use HasFactory;

    public function reward_game()
    {
        return $this->hasOne('App\Models\RewardManagementGame', 'reward_management_id', 'id');
    }

}
