<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApartmentResource extends JsonResource
{
    public function toArray($request): array
    {

        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'price'       => $this->price,
            'city'        => $this->city,
            'governorate' => $this->governorate,
            'rooms'       => $this->rooms,
            'images' => $this->getMedia('apartment_images')->map(function ($media) {
                return $media->getUrl();
            }),
        ];
    }
}

