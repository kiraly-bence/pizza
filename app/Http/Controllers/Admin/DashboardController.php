<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'auth'  => ['user' => auth()->user()],
            'stats' => [
                'orders'      => Order::count(),
                'revenue'     => (int) Order::where('status', '!=', OrderStatus::Cancelled)->sum('total'),
                'users'       => User::count(),
                'products'    => Product::count(),
                'categories'  => Category::count(),
                'ingredients' => Ingredient::count(),
                'labels'      => Label::count(),
                'pending'     => Order::where('status', OrderStatus::Pending)->count(),
            ],
        ]);
    }
}
