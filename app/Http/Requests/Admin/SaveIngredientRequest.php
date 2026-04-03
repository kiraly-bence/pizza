<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveIngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $ingredientId = $this->route('ingredient')?->id;

        return [
            'name' => ['required', 'string', 'max:100', 'unique:ingredients,name,' . $ingredientId],
        ];
    }
}
