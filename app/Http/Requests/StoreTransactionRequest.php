<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'total_amount'              => ['required', 'numeric', 'min:0'],
            'note'                      => ['nullable', 'string', 'max:500'],
            'items'                     => ['required', 'array', 'min:1'],
            'items.*.product_id'        => ['required', 'uuid', 'exists:products,id'],
            'items.*.product_name'      => ['required', 'string', 'max:255'],
            'items.*.product_price'     => ['required', 'numeric', 'min:0'],
            'items.*.quantity'          => ['required', 'integer', 'min:1'],
            'items.*.subtotal'          => ['required', 'numeric', 'min:0'],
        ];
    }
}
