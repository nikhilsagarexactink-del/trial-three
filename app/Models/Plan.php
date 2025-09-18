<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Plan extends Model
{
    use HasFactory;

    // Define the local scope for free plans
    public function scopeFreePlan(Builder $query)
    {
        return $query->where('is_default_free_plan', 1)->where('status', 'active');
    }
}
