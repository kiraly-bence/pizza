<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCategoryRequest;
use App\Models\Category;
use App\Services\Admin\CategoryService;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    public function index()
    {
        return Inertia::render('Admin/Categories', [
            'auth'       => ['user' => auth()->user()],
            'categories' => $this->categoryService->all(),
        ]);
    }

    public function store(SaveCategoryRequest $request)
    {
        $this->categoryService->create($request->validated());

        return back();
    }

    public function update(SaveCategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());

        return back();
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);

        return back();
    }
}
