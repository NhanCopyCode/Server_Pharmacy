<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ⬅️ import this

class ProductImage extends Model
{
    use HasFactory, SoftDeletes; 

    protected $fillable = ['productId', 'image'];

    public $table = 'productImages';

    public $timestamps = true; // or true, if you also added created_at/updated_at

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
