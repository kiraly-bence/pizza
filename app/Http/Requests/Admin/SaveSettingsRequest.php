<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'delivery_fee' => ['required', 'integer', 'min:0'],
            'service_fee'  => ['required', 'integer', 'min:0'],
        ];
    }
}
