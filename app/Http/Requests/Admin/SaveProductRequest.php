<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'image'         => ['nullable', 'image', 'max:4096'],
            'price'          => ['required', 'integer', 'min:0'],
            'original_price' => ['nullable', 'integer', 'gt:price'],
            'sort_order'    => ['required', 'integer', 'min:0'],
            'is_available'  => ['boolean'],
            'category_id'   => ['required', 'exists:categories,id'],
            'ingredients'   => ['array'],
            'ingredients.*' => ['exists:ingredients,id'],
            'labels'        => ['array'],
            'labels.*'      => ['exists:labels,id'],
        ];
    }
}
