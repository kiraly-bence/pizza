<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCategoryRequest;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categoryService) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Categories', [
            'categories' => $this->categoryService->all(),
        ]);
    }

    public function store(SaveCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->create($request->validated());

        return back();
    }

    public function update(SaveCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->update($category, $request->validated());

        return back();
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->delete($category);

        return back();
    }
}
