<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAdmin = auth()->check() && auth()->user()->role === 'admin';

        $base = [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name'  => $this->last_name,
            'phone' => $this->phone,
            'birth_date' => $this->birth_date,
            'verification_state' => $this->verification_state,
            'profile_image'=> $this->getMedia('profile_image')->map(function ($media) {
                return $media->getUrl();
            }),
        ];

        if ($isAdmin) {
            $base['created_at'] = $this->created_at;
            $base['updated_at'] = $this->updated_at;
            $base['role'] = $this->role;
            $base['id_image']= $this->getMedia('id_image')->map(function ($media) {
                return $media->getUrl();
            });
        }

        return $base;
    }
}
