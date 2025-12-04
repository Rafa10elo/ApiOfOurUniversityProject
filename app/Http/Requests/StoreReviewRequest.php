<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:1',
            'city' => 'required|string',
            'governorate' => 'required|string',
            'bedrooms' => 'required|integer|min:0',
            'livingrooms' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
        ];
    }

}
