<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'    => 'Az e-mail cím megadása kötelező.',
            'email.email'       => 'Érvényes e-mail címet adj meg.',
            'password.required' => 'A jelszó megadása kötelező.',
        ];
    }
}
