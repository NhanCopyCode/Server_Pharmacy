<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VoucherProduct extends Model
{
    use HasFactory;

    protected $table = 'voucher_product';

    protected $fillable = [
        'voucher_id',
        'product_id',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
