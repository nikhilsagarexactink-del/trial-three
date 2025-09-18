<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitnessChallengeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_id',
        'date',
        'completed',
        'log_id',
        'status',
        'created_by',
        'updated_by'
    ];

    /**
     * User
     */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

             /**
     * Challenge
     */
    public function challenge()
    {
        return $this->hasOne('App\Models\FitnessChallenge', 'id', 'challenge_id')->where('status', '!=', 'deleted');;
    }
}
