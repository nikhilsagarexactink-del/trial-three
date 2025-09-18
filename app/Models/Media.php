<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

class Media extends Model
{
    protected $table = 'media';

    protected $appends = ['base_url'];

    protected $fillable = [
        'name',
        'base_path',
        'base_url',
        'media_type',
        'media_for',
        'media_folder',
        'status',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'media_folder',
        'base_path',
        'media_for',
        'status',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by',
    ];

    /**
     * Get profile picture url.
     *
     * @return string
     */
    public function getBaseUrlAttribute()
    {
        $mediaUrl = '';
        if (! empty($this->attributes['name'])) {
            $mediaUrl = getImageExist($this->attributes['name'], $this->attributes['media_folder']);
            if(!empty($this->attributes['base_url']) && env('STORAGE_TYPE') == 's3') {
                $mediaUrl = $this->attributes['base_url'];
            }elseif (! empty($mediaUrl)) {
                $mediaUrl = URL::to($mediaUrl);
            }
        }
        return $mediaUrl;
    }
}
