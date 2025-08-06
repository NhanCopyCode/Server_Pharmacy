<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'title',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_value',
        'min_order_value',
        'applies_to',
        'show_on_frontend',
        'approved',
        'start_date',
        'end_date'
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'promotion_product');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'promotion_category');
    }

    public function logs()
    {
        return $this->hasMany(PromotionLog::class);
    }

    public static function getPromotionShowOnFrontend()
    {
        return self::where('approved', 1)
            ->where('show_on_frontend', 1)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->with(['products' => function ($query) {
                $query->orderBy('created_at', 'desc')->take(10);
            }])
            ->take(10)
            ->get();
    }
}
