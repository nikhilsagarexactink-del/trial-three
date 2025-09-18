<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingVideo extends Model
{
    use HasFactory;

    /**
     * Image
     */
    public function media()
    {
        return $this->hasOne('App\Models\Media', 'id', 'media_id');
    }

    /**
     * Skill Levels
     */
    public function skillLevels()
    {
        return $this->hasMany('App\Models\TrainingVideoSkillLevel', 'training_video_id');
    }

    /**
     * Age Ranges
     */
    public function ageRanges()
    {
        return $this->hasMany('App\Models\TrainingVideoAgeRange', 'training_video_id');
    }
    public function categories()
    {
        return $this->hasMany('App\Models\TrainingVideoCategory','training_video_id');
    }

    /**
     * Rating
     */
    public function rating()
    {
        return $this->hasOne('App\Models\TrainingVideoRating', 'training_video_id');
    }

    /**
     * Ratings
     */
    public function ratings()
    {
        return $this->hasMany('App\Models\TrainingVideoRating', 'training_video_id');
    }

    /**
     * Favourite
     */
    public function favourite()
    {
        return $this->hasOne('App\Models\TrainingVideoFavourite', 'training_video_id', 'id');
    }

    /**
     * Image
     */
    public function category()
    {
        return $this->hasOne('App\Models\Category', 'id', 'training_video_category_id');
    }
    // Replece , with space and first later capitals
    public function getUserTypesAttribute($value){
        $type = str_replace(",", ", ",$value);
        return ucfirst($type);
    }
}
