<?php

namespace App\Http\Controllers\My\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyProduct\ProductCreateRequest;
use App\Models\Category;
use App\Services\Product\ProductCreator;
use App\Services\Toaster;
use App\ValueObjects\Price;
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
        ProductCreator $creator,
        Toaster $toaster,
    ): RedirectResponse {
        try {
            $product = $creator->createBaseProduct(
                user: auth()->user(),
                category: Category::find($request->input('category_id')),
                name: $request->input('name'),
                price: Price::fromBaseAndDiscount($request->input('price_base'), $request->input('price_discount'))
            );
        } catch (\InvalidArgumentException $exception) {
            $toaster->error($exception->getMessage());
            return redirect()->back()->with('price_base', $exception->getMessage());
        }

        return redirect()->route('my.products.edit', $product);
    }
}
