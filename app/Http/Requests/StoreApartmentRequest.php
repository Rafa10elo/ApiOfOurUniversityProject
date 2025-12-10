<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price' => 'required|numeric|min:1',
            'city' => 'required|string|max:255',
            'governorate' => 'required|string|max:255',

            'bedrooms' => 'required|integer|min:0',
            'livingrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'space' => 'required|numeric|min:1',
            'totalRooms' => 'required|integer|min:1',

            'images.*' => 'nullable|image|max:2048',
        ];
    }
}
