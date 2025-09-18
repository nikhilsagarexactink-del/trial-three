<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Images
     */
    public function images()
    {
        return $this->hasMany('App\Models\ProductImage', 'product_id');
    }

    /**
     * Image
     */
    public function image()
    {
        return $this->hasOne('App\Models\ProductImage', 'product_id');
    }

    /**
     * Carts
     */
    public function carts()
    {
        return $this->hasOne('App\Models\Cart', 'product_id');
    }
}
