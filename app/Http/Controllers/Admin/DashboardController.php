<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Label;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'categories'  => Category::count(),
                'products'    => Product::count(),
                'ingredients' => Ingredient::count(),
                'labels'      => Label::count(),
            ],
        ]);
    }
}
