<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SaveProductRequest;
use App\Models\Product;
use App\Services\Admin\ProductService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private readonly ProductService $productService) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Products', [
            'products' => $this->productService->all(),
            ...$this->productService->formData(),
        ]);
    }

    public function store(SaveProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated(), $request->file('image'));

        return back();
    }

    public function update(SaveProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->update($product, $request->validated(), $request->file('image'));

        return back();
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->delete($product);

        return back();
    }
}
