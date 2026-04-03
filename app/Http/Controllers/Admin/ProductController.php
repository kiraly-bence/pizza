<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProductRequest;
use App\Models\Product;
use App\Services\Admin\ProductService;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index()
    {
        return Inertia::render('Admin/Products', [
            'auth'     => ['user' => auth()->user()],
            'products' => $this->productService->all(),
            ...$this->productService->formData(),
        ]);
    }

    public function store(SaveProductRequest $request)
    {
        $this->productService->create($request->validated(), $request->file('image'));

        return back();
    }

    public function update(SaveProductRequest $request, Product $product)
    {
        $this->productService->update($product, $request->validated(), $request->file('image'));

        return back();
    }

    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        return back();
    }
}
