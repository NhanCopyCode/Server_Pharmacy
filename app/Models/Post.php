<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'description', 'userId', 'approved', 'image', 'post_category_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function category()
    {
        return $this->belongsTo(PostCategory::class, 'post_category_id');
    }
}
