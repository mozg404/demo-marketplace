<?php

namespace App\Http\Controllers\My\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyProduct\ProductChangeFeaturesRequest;
use App\Models\Feature;
use App\Models\Product;
use App\Services\Product\ProductFeatureAttacher;
use App\Services\Product\ProductService;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductChangeFeaturesController extends Controller
{
    public function index(Product $product): Response
    {
        return Inertia::render('my/products/ProductChangeFeaturesModal', [
            'product' => $product,
            'features' => Feature::query()->forCategoryAndAncestors($product->category)->get(),
            'featureValues' => $product->features->toIdValuePairs(),
        ]);
    }

    public function update(
        Product $product,
        ProductChangeFeaturesRequest $request,
        ProductFeatureAttacher $featureAttacher,
        Toaster $toaster,
    ): RedirectResponse {
        $featureAttacher->attachAllFromArray($product, $request->getValidatedValues());
        $toaster->success('Характеристики изменены');

        return back();
    }
}
