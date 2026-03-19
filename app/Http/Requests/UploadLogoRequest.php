<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'logo' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'A base64 image string is required.',
        ];
    }
}
