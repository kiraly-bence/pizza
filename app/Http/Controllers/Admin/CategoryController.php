<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        Category::create($validated);

        return back();
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $category->update($validated);

        return back();
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return back();
    }
}
