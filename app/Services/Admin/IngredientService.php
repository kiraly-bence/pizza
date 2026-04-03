<?php

namespace App\Services\Admin;

use App\Models\Ingredient;
use Illuminate\Database\Eloquent\Collection;

class IngredientService
{
    public function all(): Collection
    {
        return Ingredient::withCount('products')->orderBy('name')->get();
    }

    public function create(array $data): Ingredient
    {
        return Ingredient::create($data);
    }

    public function update(Ingredient $ingredient, array $data): void
    {
        $ingredient->update($data);
    }

    public function delete(Ingredient $ingredient): void
    {
        $ingredient->delete();
    }
}
