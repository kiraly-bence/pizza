<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\IngredientController as AdminIngredientController;
use App\Http\Controllers\Admin\LabelController as AdminLabelController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\CouponController;
use Illuminate\Support\Facades\Route;

// Főoldal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login',    [AuthController::class, 'login'])->name('login');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Password reset
Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');

// Coupon validation (auth required)
Route::middleware('auth')->post('/coupon/validate', [CouponController::class, 'validate'])->name('coupon.validate');

// Checkout & profile (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/rendeleseim', [OrderController::class, 'myOrders'])->name('orders.my');
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profil/cim', [ProfileController::class, 'updateAddress'])->name('profile.address');
});

// Admin
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Orders
        Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');

        // Users
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');

        // Categories
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::patch('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Products
        Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
        Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
        Route::patch('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');

        // Ingredients
        Route::get('/ingredients', [AdminIngredientController::class, 'index'])->name('ingredients.index');
        Route::post('/ingredients', [AdminIngredientController::class, 'store'])->name('ingredients.store');
        Route::patch('/ingredients/{ingredient}', [AdminIngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}', [AdminIngredientController::class, 'destroy'])->name('ingredients.destroy');

        // Labels
        Route::get('/labels', [AdminLabelController::class, 'index'])->name('labels.index');
        Route::post('/labels', [AdminLabelController::class, 'store'])->name('labels.store');
        Route::patch('/labels/{label}', [AdminLabelController::class, 'update'])->name('labels.update');
        Route::delete('/labels/{label}', [AdminLabelController::class, 'destroy'])->name('labels.destroy');

        // Coupons
        Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
        Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
        Route::patch('/coupons/{coupon}', [AdminCouponController::class, 'update'])->name('coupons.update');
        Route::delete('/coupons/{coupon}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
        Route::patch('/coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');
    });
