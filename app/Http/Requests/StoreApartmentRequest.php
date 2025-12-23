<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreApartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        $governorates = [
            'Damascus',
            'Rif Dimashq',
            'Aleppo',
            'Homs',
            'Hama',
            'Latakia',
            'Tartus',
            'Idlib',
            'Deir ez-Zor',
            'Raqqa',
            'Hasakah',
            'Daraa',
            'As-Suwayda',
            'Quneitra',
        ];
        return [
            'price' => 'required|numeric|min:1',
            'city' => 'required|string|max:255',
            'governorate' => [
              'required',
                'string',
                Rule::in($governorates),
            ],

            'bedrooms' => 'required|integer|min:0',
            'livingrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'space' => 'required|numeric|min:1',
            'totalRooms' => 'required|integer|min:1',

            'images.*' => 'nullable|image|max:2048',
        ];
    }

}
