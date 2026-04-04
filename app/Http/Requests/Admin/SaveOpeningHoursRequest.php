<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveOpeningHoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'hours'            => ['required', 'array'],
            'hours.*'          => ['required', 'array'],
            'hours.*.closed'   => ['required', 'boolean'],
            'hours.*.open'     => ['nullable', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'hours.*.close'    => ['nullable', 'string', 'regex:/^\d{2}:\d{2}$/'],
        ];
    }
}
