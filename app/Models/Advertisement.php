<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Advertisement extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'advertisements';

    protected $fillable = [
        'title',
        'image',
        'approved',
        'start_date',
        'end_date',
    ];

    public $timestamps = true;
}
