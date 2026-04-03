<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'zip'    => ['nullable', 'string', 'max:10'],
            'city'   => ['nullable', 'string', 'max:100'],
            'street' => ['nullable', 'string', 'max:255'],
            'note'   => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'zip.max'    => 'Az irányítószám legfeljebb 10 karakter lehet.',
            'city.max'   => 'A város neve legfeljebb 100 karakter lehet.',
            'street.max' => 'Az utca, házszám legfeljebb 255 karakter lehet.',
        ];
    }
}
