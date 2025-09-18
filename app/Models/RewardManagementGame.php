<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardManagementGame extends Model
{
    use HasFactory;

    public function reward_management()
    {
        return $this->hasOne('App\Models\RewardManagement', 'id', 'reward_management_id');
    }
    public function game()
    {
        return $this->hasOne('App\Models\GameMaster', 'game_key', 'game_key');
    }

}
