<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FitnessChallengeSignup extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_id',
        'user_id',
        'signup_date',
        'status',
        'created_by',
        'updated_by',
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
