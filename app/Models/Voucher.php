<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'image',
        'description',
        'discount_type',
        'discount_value',
        'max_discount_value',
        'usage_limit',
        'usage_limit_per_user',
        'min_order_value',
        'applies_to',
        'approved',
        'start_date',
        'end_date'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'voucher_user_usages')->withPivot('usage_count');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'voucher_product');
    }
}
