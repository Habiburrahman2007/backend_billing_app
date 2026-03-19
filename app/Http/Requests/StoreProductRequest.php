<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'      => ['nullable', 'uuid'],
            'name'    => ['required', 'string', 'max:255'],
            'barcode' => [
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'barcode')->where('user_id', $this->user()->id),
            ],
            'price'   => ['required', 'numeric', 'min:0.01'],
            'stock'   => ['required', 'integer', 'min:0'],
        ];
    }
}
