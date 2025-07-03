<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'orderId',
        'payment_gateway',
        'transaction_code',
        'amount',
        'payment_date',
        'payment_time',
        'vnp_response_code',
        'bank_code',
        'note',
        'createdAt',
        'updatedAt'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
