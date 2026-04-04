<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $categories = Category::query()
            ->orderBy('sort_order')
            ->with([
                'products' => fn($q) => $q
                    ->where('is_available', true)
                    ->orderBy('sort_order')
                    ->with(['ingredients', 'labels']),
            ])
            ->get()
            ->each(function ($category) {
                $category->products->each(function ($product) {
                    if ($product->image) {
                        $product->image = '/storage/' . $product->image;
                    }
                });
            });

        return Inertia::render('Home', [
            'auth'       => ['user' => auth()->user()],
            'categories' => $categories,
        ]);
    }
}
