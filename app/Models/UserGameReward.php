<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGameReward extends Model
{
    use HasFactory;

    public function reward_management_game()
    {
        return $this->hasOne('App\Models\RewardManagementGame', 'id', 'reward_management_game_id');
    }
}
