<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => 'sometimes|numeric|min:1',
            'city' => 'sometimes|string|max:255',
            'governorate' => 'sometimes|string|max:255',

            'bedrooms' => 'sometimes|integer|min:0',
            'livingrooms' => 'sometimes|integer|min:0',
            'bathrooms' => 'sometimes|integer|min:0',
            'space' => 'sometimes|numeric|min:1',
            'totalRooms' => 'sometimes|integer|min:1',

            'images.*' => 'sometimes|image|max:2048',
        ];
    }
}
