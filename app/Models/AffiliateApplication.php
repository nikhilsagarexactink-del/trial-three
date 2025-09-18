<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;


class AffiliateApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'address',
        'status',
        'token',
        'total_earnings',
        'terms_agreed_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userPayoutSetting()
    {
        return $this->hasOne('App\Models\UserAffiliatePayoutSetting', 'user_id', 'user_id');
    }
    

    public function scopeActiveApplication(Builder $query)
    {
        return $query->where([
            ['status', '=', 'approved'],
            ['terms_agreed_at', '!=', null],
            ['is_enabled', '=', 1],
        ]);
    }

}
