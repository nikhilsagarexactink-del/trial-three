<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterWidget extends Model
{
    use HasFactory;

    protected $table = 'master_widgets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
    ];

    public function modules()
    {
        return $this->hasMany('App\Models\WidgetModule', 'widget_id', 'id');
    }

}
