<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'name'    => ['sometimes', 'required', 'string', 'max:255'],
            'barcode' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('products', 'barcode')
                    ->where('user_id', $this->user()->id)
                    ->ignore($productId, 'id'),
            ],
            'price'   => ['sometimes', 'required', 'numeric', 'min:0.01'],
            'stock'   => ['sometimes', 'required', 'integer', 'min:0'],
        ];
    }
}
