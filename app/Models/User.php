<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements JWTSubject, HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'birth_date',
        'verification_state',
        'password',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')->singleFile();
        $this->addMediaCollection('id_image')->singleFile();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
