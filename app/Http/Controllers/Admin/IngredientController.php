<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveIngredientRequest;
use App\Models\Ingredient;
use Inertia\Inertia;

class IngredientController extends Controller
{
    public function index()
    {
        $ingredients = Ingredient::withCount('products')->orderBy('name')->get();

        return Inertia::render('Admin/Ingredients', [
            'auth'        => ['user' => auth()->user()],
            'ingredients' => $ingredients,
        ]);
    }

    public function store(SaveIngredientRequest $request)
    {
        Ingredient::create($request->validated());

        return back();
    }

    public function update(SaveIngredientRequest $request, Ingredient $ingredient)
    {
        $ingredient->update($request->validated());

        return back();
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();

        return back();
    }
}
