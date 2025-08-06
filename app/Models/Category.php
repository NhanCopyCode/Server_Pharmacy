<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'image',
        'outstanding',
        'parentId',
        'approved',
    ];

    public $timestamps = true;

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parentId');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parentId');
    }

    public function positions()
    {
        return $this->hasMany(CategoryPosition::class, 'category_id');
    }
    public function getPositionName()
    {
        $positions = [
            'homepage' => 'Trang chủ',
            'header' => 'Thanh menu',
            'footer' => 'Chân trang',
            'body' => 'Body',
        ];

        return $this->positions->pluck('position')->map(function ($pos) use ($positions) {
            return $positions[$pos] ?? $pos;
        })->toArray(); 
    }



    public static function getListOutstanding()
    {
        return self::where('outstanding', 1)
            ->where('approved', 1)
            ->whereHas('positions', function($query) {
                $query->where('position', 'body');
            })
            ->get();
    }

    public static function getCategoryParentAndChild()
    {
        return  self::where('parentId', 0)
            ->where('approved', 1)
            ->with(['children' => function ($query) {
                $query->where('approved', 1);
            }])
            ->get();
    }


}
