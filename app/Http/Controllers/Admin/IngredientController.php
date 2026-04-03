<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:100', 'unique:ingredients,name']]);
        Ingredient::create($request->only('name'));
        return back();
    }

    public function update(Request $request, Ingredient $ingredient)
    {
        $request->validate(['name' => ['required', 'string', 'max:100', 'unique:ingredients,name,' . $ingredient->id]]);
        $ingredient->update($request->only('name'));
        return back();
    }

    public function destroy(Ingredient $ingredient)
    {
        $ingredient->delete();
        return back();
    }
}
