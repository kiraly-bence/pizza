<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveIngredientRequest;
use App\Models\Ingredient;
use App\Services\Admin\IngredientService;
use Inertia\Inertia;

class IngredientController extends Controller
{
    public function __construct(private readonly IngredientService $ingredientService) {}

    public function index(): \Inertia\Response
    {
        return Inertia::render('Admin/Ingredients', [
            'auth'        => ['user' => auth()->user()],
            'ingredients' => $this->ingredientService->all(),
        ]);
    }

    public function store(SaveIngredientRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->ingredientService->create($request->validated());

        return back();
    }

    public function update(SaveIngredientRequest $request, Ingredient $ingredient): \Illuminate\Http\RedirectResponse
    {
        $this->ingredientService->update($ingredient, $request->validated());

        return back();
    }

    public function destroy(Ingredient $ingredient): \Illuminate\Http\RedirectResponse
    {
        $this->ingredientService->delete($ingredient);

        return back();
    }
}
