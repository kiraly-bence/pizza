<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveIngredientRequest;
use App\Models\Ingredient;
use App\Services\Admin\IngredientService;
use Inertia\Inertia;

class IngredientController extends Controller
{
    public function __construct(private IngredientService $ingredientService) {}

    public function index()
    {
        return Inertia::render('Admin/Ingredients', [
            'auth'        => ['user' => auth()->user()],
            'ingredients' => $this->ingredientService->all(),
        ]);
    }

    public function store(SaveIngredientRequest $request)
    {
        $this->ingredientService->create($request->validated());

        return back();
    }

    public function update(SaveIngredientRequest $request, Ingredient $ingredient)
    {
        $this->ingredientService->update($ingredient, $request->validated());

        return back();
    }

    public function destroy(Ingredient $ingredient)
    {
        $this->ingredientService->delete($ingredient);

        return back();
    }
}
