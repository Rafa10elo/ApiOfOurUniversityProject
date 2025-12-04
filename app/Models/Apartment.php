<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Apartment extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'owner_id',
        'title',
        'description',
        'price',
        'city',
        'governorate',
        'rooms',
    ];


    public function averageRating()
    {
        return $this->reviews()->avg('rating') ?? -1;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    public function favoredBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }
    public function totalRooms()
    {
        return $this->bedrooms + $this->livingrooms + $this->bathrooms;
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }


    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('apartment_images');
    }
}
