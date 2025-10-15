<?php

namespace App\Http\Controllers\My\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyProduct\ProductChangePriceRequest;
use App\Models\Product;
use App\Services\Product\ProductService;
use App\Services\Product\ProductUpdater;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductChangePriceController extends Controller
{
    public function index(Product $product): Response
    {
        return Inertia::render('my/products/ProductChangePriceModal', [
            'product' => $product,
        ]);
    }

    public function update(
        Product $product,
        ProductChangePriceRequest $request,
        ProductUpdater $updater,
        Toaster $toaster,
    ): RedirectResponse {
        $updater->updatePrice($product, $request->getPrice());
        $toaster->success('Цена изменена');

        return back();
    }
}
