<?php

namespace App\Services\Admin;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    public function all(): Collection
    {
        return Category::withCount('products')->orderBy('sort_order')->get();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): void
    {
        $category->update($data);
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
