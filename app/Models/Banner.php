<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'image',
        'approved',
        'position_id'
    ];

    public $timestamps = true;

    public function position()
    {
        return $this->belongsTo(BannerPosition::class, 'position_id');
    }

    public static function getTopBanners()
    {
        return self::whereHas('position', function ($query) {
            $query->where('name', 'Đầu trang');
        })
            ->where('approved', 1)
            ->latest()
            ->get();
    }

    public static function getBannersHomepage()
    {
        return self::whereHas('position', function ($query) {
            $query->where('name', 'Trang chủ');
        })
            ->where('approved', 1)
            ->latest()
            ->get();
    }
}
