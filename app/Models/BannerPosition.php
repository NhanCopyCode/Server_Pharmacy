<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BannerPosition extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'approved'];

    public function banners()
    {
        return $this->hasMany(Banner::class, 'position_id');
    }
}
