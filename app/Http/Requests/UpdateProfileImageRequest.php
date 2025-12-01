<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'You must upload an image.',
            'image.image'    => 'The uploaded file must be an image.',
            'image.max'      => 'Image size cannot exceed 2MB.',
        ];
    }
}
