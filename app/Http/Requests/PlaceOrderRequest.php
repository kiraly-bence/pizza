<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'items'            => ['required', 'array', 'min:1'],
            'items.*.id'       => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment_method'   => ['required', 'in:card,cash'],
            'zip'              => ['required', 'string', 'max:10'],
            'city'             => ['required', 'string', 'max:100'],
            'street'           => ['required', 'string', 'max:255'],
            'note'             => ['nullable', 'string', 'max:255'],
            'delivery_message' => ['nullable', 'string', 'max:1000'],
            'save_address'     => ['boolean'],
            'coupon_code'      => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'          => 'A kosár üres.',
            'payment_method.required' => 'Válassz fizetési módot.',
            'zip.required'            => 'Az irányítószám megadása kötelező.',
            'city.required'           => 'A város megadása kötelező.',
            'street.required'         => 'Az utca, házszám megadása kötelező.',
        ];
    }
}
