<?php

namespace App\Http\Controllers\My\Product;

use App\DTO\Product\ProductBaseCreateDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyProduct\ProductCreateRequest;
use App\Models\Category;
use App\Services\Product\ProductManager;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductCreateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('my/products/ProductCreateModal', [
            'categoriesTree' => Category::query()->withDepth()->get()->toTree(),
        ]);
    }

    public function store(
        ProductCreateRequest $request,
        ProductManager $manager,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            $product = $manager->createBaseProduct(auth()->id(), $request->getDto());
        } catch (\InvalidArgumentException $exception) {
            $toaster->error($exception->getMessage());
            return redirect()->back()->with('price_base', $exception->getMessage());
        }

        return redirect()->route('my.products.edit', $product);
    }
}
