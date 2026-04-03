<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'A név megadása kötelező.',
            'email.required'     => 'Az e-mail cím megadása kötelező.',
            'email.email'        => 'Érvényes e-mail címet adj meg.',
            'email.unique'       => 'Ez az e-mail cím már foglalt.',
            'password.required'  => 'A jelszó megadása kötelező.',
            'password.confirmed' => 'A két jelszó nem egyezik meg.',
            'password.min'       => 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',
        ];
    }
}
