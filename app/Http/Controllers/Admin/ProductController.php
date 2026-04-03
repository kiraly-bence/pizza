<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'ingredients', 'labels'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'name'         => $p->name,
                'description'  => $p->description,
                'image'        => $p->image ? '/storage/' . $p->image : null,
                'image_path'   => $p->image,
                'price'        => $p->price,
                'sort_order'   => $p->sort_order,
                'is_available' => $p->is_available,
                'category_id'  => $p->category_id,
                'category'     => $p->category?->name,
                'ingredients'  => $p->ingredients->pluck('id'),
                'labels'       => $p->labels->pluck('id'),
            ]);

        return Inertia::render('Admin/Products', [
            'auth'        => ['user' => auth()->user()],
            'products'    => $products,
            'categories'  => Category::orderBy('sort_order')->get(['id', 'name']),
            'ingredients' => Ingredient::orderBy('name')->get(['id', 'name']),
            'labels'      => Label::orderBy('name')->get(['id', 'name', 'type']),
        ]);
    }

    private function rules(): array
    {
        return [
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'image'         => ['nullable', 'image', 'max:4096'],
            'price'         => ['required', 'integer', 'min:0'],
            'sort_order'    => ['required', 'integer', 'min:0'],
            'is_available'  => ['boolean'],
            'category_id'   => ['required', 'exists:categories,id'],
            'ingredients'   => ['array'],
            'ingredients.*' => ['exists:ingredients,id'],
            'labels'        => ['array'],
            'labels.*'      => ['exists:labels,id'],
        ];
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($validated['image']);
        }

        $product = Product::create($validated);
        $product->ingredients()->sync($validated['ingredients'] ?? []);
        $product->labels()->sync($validated['labels'] ?? []);

        return back();
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate($this->rules());

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        } else {
            unset($validated['image']);
        }

        $product->update($validated);
        $product->ingredients()->sync($validated['ingredients'] ?? []);
        $product->labels()->sync($validated['labels'] ?? []);

        return back();
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return back();
    }
}
