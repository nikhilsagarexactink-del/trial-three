<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDashboardWidget extends Model
{
    use HasFactory;

    public function widget()
    {
        return $this->hasOne(MasterWidget::class,'id','widget_id')->where('status','!=','deleted');
    }
}
