<?php

namespace App\Http\Controllers\My\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\Product\ProductService;
use App\Services\Product\ProductUpdater;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductChangeNameController extends Controller
{
    public function index(Product $product): Response
    {
        return Inertia::render('my/products/ProductChangeNameModal', [
            'product' => $product,
        ]);
    }

    public function update(
        Product $product,
        Request $request,
        ProductUpdater $productUpdater,
        Toaster $toaster,
    ): RedirectResponse {
        $productUpdater->updateName($product, $request->input('name'));
        $toaster->success('Название отредактировано');

        return back();
    }
}
