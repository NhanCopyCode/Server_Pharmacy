<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'surname',
        'name',
        'email',
        'phoneNumber',
        'password',
        'address',
        'roleId',
        'refresh_token',
        'social_provider',
        'social_id',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public $timestamps = false;

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // JWT required methods:
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleId');
    }
}
