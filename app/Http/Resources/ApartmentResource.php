<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use \App\Http\Resources;
class ApartmentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [

            'id' => $this->id,
            'owner' => new UserResource($this->owner),
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'city' => $this->city,
            'governorate' => $this->governorate,
            'bedrooms' => $this->bedrooms,
            'livingrooms' => $this->livingrooms,
            'bathrooms' => $this->bathrooms,
            'total_rooms' => ($this->bedrooms ?? 0) + ($this->livingrooms ?? 0) + ($this->bathrooms ?? 0),
            'average_rating' => (float) optional($this->reviews)->avg('rating') ?? 0,
            'reviews' => ReviewResource::collection($this->whenLoaded('reviews')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
