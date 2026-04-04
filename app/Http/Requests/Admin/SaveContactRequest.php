<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone'   => ['required', 'string', 'max:30'],
            'email'   => ['required', 'email', 'max:100'],
            'address' => ['required', 'string', 'max:200'],
        ];
    }
}
