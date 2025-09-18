<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'screen_name',
        'user_type',
        'profile_picture',
        'email',
        'username',
        'is_username',
        'password',
        'age',
        'gender',
        'school_name',
        'grade',
        'email_verified_at',
        'cell_phone_number',
        'address',
        'country',
        'state',
        'city',
        'zip_code',
        'latitude',
        'longitude',
        'remember_token',
        'verify_token',
        'favorite_athlete',
        'favorite_sport',
        'favorite_sport_play_years',
        'loggedin_parent_id',
        'is_parent_login',
        'parent_id',
        'quote_id',
        'stripe_customer_id',
        'stripe_status',
        'media_id',
        'status',
        'last_login_date',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'total_reward_points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Always encrypt the password when it is updated.
     *
     * @return string
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Media
     */
    public function media()
    {
        return $this->hasOne('App\Models\Media', 'id', 'media_id');
    }

    /**
     * Media
     */
    public function parent()
    {
        return $this->hasOne('App\Models\User', 'id', 'parent_id');
    }

    /**
     * Media
     */
    public function loginParent()
    {
        return $this->hasOne('App\Models\User', 'id', 'loggedin_parent_id');
    }

    /**
     * Sports
     */
    public function sports()
    {
        return $this->hasMany('App\Models\UserSport', 'user_id', 'id');
    }

    /**
     * User Subsription
     */
    public function userSubsription()
    {
        return $this->hasOne('App\Models\UserSubscription', 'user_id', 'id')->orderBy('created_at', 'desc');
    }

    public function notificationSetting()
    {
        return $this->hasOne('App\Models\UserEventNotificationSetting', 'user_id', 'id');
    }
    public function calendarSettings()
    {
        return $this->hasMany(UserCalendarSetting::class, 'user_id');
    }

    /**
     * User Subsription
     */
    public function groupUsers()
    {
        return $this->hasMany('App\Models\GroupUser', 'user_id', 'id');
    }
}
