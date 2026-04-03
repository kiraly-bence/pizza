<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'     => 'Az e-mail cím megadása kötelező.',
            'email.email'        => 'Érvényes e-mail címet adj meg.',
            'password.required'  => 'A jelszó megadása kötelező.',
            'password.confirmed' => 'A két jelszó nem egyezik meg.',
            'password.min'       => 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',
        ];
    }
}
