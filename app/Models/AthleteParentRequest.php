<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AthleteParentRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_type',
        'subscription_type',
        'status',
        'verify_token',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
