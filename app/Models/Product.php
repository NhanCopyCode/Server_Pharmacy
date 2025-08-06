<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'inventory',
        'categoryId',
        'price',
        'brandId',
        'outstanding',
        'approved',
        'main_image',
    ];

    public $timestamps = true;


    public function images()
    {
        return $this->hasMany(ProductImage::class, 'productId');
    }

    protected static function booted()
    {
        static::deleting(function ($product) {
            $product->images()->delete();
        });
    }



    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brandId');
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_product', 'productId', 'discountId');
    }
    
    public function targetAudiences()
    {
        return $this->belongsToMany(TargetAudience::class, 'product_target_audience');
    }

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_product', 'product_id', 'promotion_id');
    }

    // In app/Models/Product.php

    public static function getLatest($limit = 5)
    {
        return self::where('approved', 1)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
}
