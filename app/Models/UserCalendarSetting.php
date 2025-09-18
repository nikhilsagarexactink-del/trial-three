<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCalendarSetting extends Model
{
    use HasFactory;
    // public function module(){
    //     return $this->hasOne(CalendarModule::class, 'id', 'calendar_module_id');
    // }
    public function module()
    {
        return $this->belongsTo(CalendarModule::class, 'calendar_module_id'); // Adjust 'module_id' if the foreign key is different.
    }
}
