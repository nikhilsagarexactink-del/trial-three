<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBroadcastAlert extends Model
{
    use HasFactory;

    public function broadcast() {
        return $this->belongsTo('App\Models\Broadcast', 'broadcast_id', 'id');
    }
}
