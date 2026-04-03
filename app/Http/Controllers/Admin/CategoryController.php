<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveCategoryRequest;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('sort_order')->get();

        return Inertia::render('Admin/Categories', [
            'auth'       => ['user' => auth()->user()],
            'categories' => $categories,
        ]);
    }

    public function store(SaveCategoryRequest $request)
    {
        Category::create($request->validated());

        return back();
    }

    public function update(SaveCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return back();
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back();
    }
}
