<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'owner' => new UserResource($this->owner),
            'price' => $this->price,
            'city' => $this->city,
            'governorate' => $this->governorate,
            'bedrooms' => $this->bedrooms,
            'livingrooms' => $this->livingrooms,
            'bathrooms' => $this->bathrooms,
            'space' => $this->space,
            'total_rooms' => $this->totalRooms,
            'average_rating' => (float) ($this->reviews_avg_rating ?? $this->reviews->avg('rating') ?? 0),
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'images' => $this->getMedia('apartment_images')->map(function ($media) {
                return $media->getUrl();
            }),
        ];
    }
}
