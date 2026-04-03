<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'auth'  => ['user' => auth()->user()],
            'stats' => [
                'orders'      => Order::count(),
                'revenue'     => (int) Order::where('status', '!=', 'cancelled')->sum('total'),
                'users'       => User::count(),
                'products'    => Product::count(),
                'categories'  => Category::count(),
                'ingredients' => Ingredient::count(),
                'labels'      => Label::count(),
                'pending'     => Order::where('status', 'pending')->count(),
            ],
        ]);
    }
}
