<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\MasterNotificationType;
use App\Models\User;

class UserModuleNotificationSetting extends Model
{
    use HasFactory;

    public function notificationType(){
        return $this->hasOne(MasterNotificationType::class,'id', 'master_notification_type_id');
    }

    public function users(){
        return $this->hasOne(User::class,'id', 'user_id');
    }
}
