<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveIngredientRequest;
use App\Models\Ingredient;
use App\Services\Admin\IngredientService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class IngredientController extends Controller
{
    public function __construct(private readonly IngredientService $ingredientService) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Ingredients', [
            'ingredients' => $this->ingredientService->all(),
        ]);
    }

    public function store(SaveIngredientRequest $request): RedirectResponse
    {
        $this->ingredientService->create($request->validated());

        return back();
    }

    public function update(SaveIngredientRequest $request, Ingredient $ingredient): RedirectResponse
    {
        $this->ingredientService->update($ingredient, $request->validated());

        return back();
    }

    public function destroy(Ingredient $ingredient): RedirectResponse
    {
        $this->ingredientService->delete($ingredient);

        return back();
    }
}
