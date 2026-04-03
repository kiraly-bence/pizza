<?php

namespace App\Services\Admin;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Product;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function all(): Collection
    {
        return Product::with(['category', 'ingredients', 'labels'])
            ->orderBy('sort_order')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'name'         => $p->name,
                'description'  => $p->description,
                'image'        => $p->image ? '/storage/' . $p->image : null,
                'price'        => $p->price,
                'sort_order'   => $p->sort_order,
                'is_available' => $p->is_available,
                'category_id'  => $p->category_id,
                'category'     => $p->category?->name,
                'ingredients'  => $p->ingredients->pluck('id'),
                'labels'       => $p->labels->pluck('id'),
            ]);
    }

    public function formData(): array
    {
        return [
            'categories'  => Category::orderBy('sort_order')->get(['id', 'name']),
            'ingredients' => Ingredient::orderBy('name')->get(['id', 'name']),
            'labels'      => Label::orderBy('name')->get(['id', 'name', 'type']),
        ];
    }

    public function create(array $data, ?UploadedFile $image): Product
    {
        if ($image) {
            $data['image'] = $image->store('products', 'public');
        }

        $product = Product::create($data);
        $product->ingredients()->sync($data['ingredients'] ?? []);
        $product->labels()->sync($data['labels'] ?? []);

        return $product;
    }

    public function update(Product $product, array $data, ?UploadedFile $image): void
    {
        if ($image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $image->store('products', 'public');
        } else {
            unset($data['image']);
        }

        $product->update($data);
        $product->ingredients()->sync($data['ingredients'] ?? []);
        $product->labels()->sync($data['labels'] ?? []);
    }

    public function delete(Product $product): void
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();
    }
}
