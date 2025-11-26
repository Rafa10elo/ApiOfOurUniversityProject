<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'verification_state' => $this->verification_state,

            'profile_image' => $this->getFirstMediaUrl('profile_image'),
            'id_image' => $this->getFirstMediaUrl('id_image'),

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
