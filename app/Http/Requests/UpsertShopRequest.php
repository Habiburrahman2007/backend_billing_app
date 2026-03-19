<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertShopRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'phone_number'  => ['nullable', 'string', 'max:20'],
            'upi_id'        => ['nullable', 'string', 'max:100'],
            'footer_text'   => ['nullable', 'string', 'max:255'],
        ];
    }
}
