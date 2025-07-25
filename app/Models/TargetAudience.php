<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TargetAudience extends Model
{
    protected $fillable = ['name', 'description'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_target_audience');
    }
}
