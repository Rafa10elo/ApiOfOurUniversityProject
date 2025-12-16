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
    use InteractsWithMedia, Notifiable;
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'birth_date',
        'verification_state',
        'role',
        'password',
    ];

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }
    public function favorites()
    {
        return $this->belongsToMany(Apartment::class, 'favorites')->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')->singleFile();
        $this->addMediaCollection('id_image')->singleFile();
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getId()
    {
        return $this->id;
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
