<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringBroadcastLog extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'broadcast_id',
        'user_id',
        'trigger_event',
        'created_at',
        'updated_at',
        'anniversary_date',
        'send_type'
    ];
}
