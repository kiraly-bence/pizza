<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProductRequest;
use App\Models\Product;
use App\Services\Admin\ProductService;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(): \Inertia\Response
    {
        return Inertia::render('Admin/Products', [
            'auth'     => ['user' => auth()->user()],
            'products' => $this->productService->all(),
            ...$this->productService->formData(),
        ]);
    }

    public function store(SaveProductRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->productService->create($request->validated(), $request->file('image'));

        return back();
    }

    public function update(SaveProductRequest $request, Product $product): \Illuminate\Http\RedirectResponse
    {
        $this->productService->update($product, $request->validated(), $request->file('image'));

        return back();
    }

    public function destroy(Product $product): \Illuminate\Http\RedirectResponse
    {
        $this->productService->delete($product);

        return back();
    }
}
