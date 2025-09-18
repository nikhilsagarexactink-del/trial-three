<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDashboard extends Model
{
    use HasFactory;


    public function widgets()
    {
        return $this->hasMany(UserDashboardWidget::class,'user_dashboard_id','id')->where('status','!=','deleted')->orderBy('widget_order', 'asc');
    }
    public function user()
    {
        return $this->hasOne(User::class,'id','user_id')->where('status','!=','deleted');
    }
}
