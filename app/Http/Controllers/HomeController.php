<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::query()
            ->orderBy('sort_order')
            ->with([
                'products' => fn($q) => $q
                    ->where('is_available', true)
                    ->orderBy('sort_order')
                    ->with(['ingredients', 'labels']),
            ])
            ->get();

        return Inertia::render('Home', [
            'auth'       => ['user' => auth()->user()],
            'categories' => $categories,
        ]);
    }
}
