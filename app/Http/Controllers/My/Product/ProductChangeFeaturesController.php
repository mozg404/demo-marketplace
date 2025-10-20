<?php

namespace App\Http\Controllers\My\Product;

use App\DTO\Product\ProductAttachFeaturesDto;
use App\Http\Controllers\Controller;
use App\Models\Feature;
use App\Models\Product;
use App\Services\Feature\FeatureQuery;
use App\Services\Product\ProductManager;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductChangeFeaturesController extends Controller
{
    public function index(Product $product, FeatureQuery $featureQuery): Response
    {
        return Inertia::render('my/products/ProductChangeFeaturesModal', [
            'product' => $product,
            'features' => $featureQuery->getFeaturesByCategory($product->category_id),
            'featureValues' => $product->features->toIdValuePairs(),
        ]);
    }

    public function update(
        Product $product,
        ProductAttachFeaturesDto $dto,
        ProductManager $manager,
        Toaster $toaster,
    ): RedirectResponse {
        $manager->attachFeatures($product, $dto);
        $toaster->success('Характеристики изменены');

        return back();
    }
}
